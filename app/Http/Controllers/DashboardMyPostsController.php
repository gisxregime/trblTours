<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\TouristRequest;
use App\Models\User;
use App\Notifications\TourActivityNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardMyPostsController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user?->role === 'tourist', 403);

        $this->seedDemoContentIfNeeded($user);

        $posts = TouristRequest::query()
            ->where('tourist_id', $user->id)
            ->with([
                'selectedGuide:id,name,full_name,status,profile_photo_path',
                'comments' => fn ($query) => $query
                    ->with('user:id,name,full_name,status,profile_photo_path,role')
                    ->latest()
                    ->limit(5),
            ])
            ->withCount('comments')
            ->latest()
            ->get();

        $stats = [
            'total_requests' => $posts->count(),
            'open_requests' => $posts->filter(fn (TouristRequest $post): bool => in_array($this->normalizeStatus($post->status), ['open', 'negotiating'], true))->count(),
            'selected_guides' => $posts->filter(fn (TouristRequest $post): bool => $this->normalizeStatus($post->status) === 'guide_selected')->count(),
            'completed_trips' => $posts->filter(fn (TouristRequest $post): bool => $this->normalizeStatus($post->status) === 'completed')->count(),
        ];

        $requestConversations = Conversation::query()
            ->where('tourist_id', $user->id)
            ->whereNotNull('tourist_request_id')
            ->with([
                'tourist:id,name,full_name,profile_photo_path,role',
                'guide:id,name,full_name,profile_photo_path,role',
                'latestMessage',
            ])
            ->withCount([
                'messages as unread_count' => fn ($query) => $query
                    ->where('sender_id', '!=', $user->id)
                    ->when(Schema::hasColumn('messages', 'read_at'), fn ($builder) => $builder->whereNull('read_at'))
                    ->when(! Schema::hasColumn('messages', 'read_at'), fn ($builder) => $builder->where('is_read', false)),
            ])
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at')
            ->get();

        return view('dashboards.my-posts', [
            'stats' => $stats,
            'bootstrapPosts' => $posts->map(fn (TouristRequest $touristRequest): array => $this->formatRequestPost($touristRequest))->values(),
            'bootstrapConversations' => $requestConversations
                ->map(fn (Conversation $conversation): array => $this->formatConversationSummary($conversation, (int) $user->id))
                ->values(),
            'demoPosts' => $this->demoPosts(),
            'showUrlTemplate' => route('dashboard.messages.show', ['conversation' => '__conversation__']),
            'storeUrlTemplate' => route('dashboard.messages.store', ['conversation' => '__conversation__']),
            'readUrlTemplate' => route('dashboard.messages.read', ['conversation' => '__conversation__']),
        ]);
    }

    public function update(Request $request, TouristRequest $touristRequest): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->role === 'tourist', 403);
        abort_unless((int) $touristRequest->tourist_id === (int) $user->id, 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'region' => ['nullable', 'string', 'max:255'],
            'duration' => ['nullable', 'string', 'max:120'],
            'budget_min' => ['required', 'numeric', 'min:500'],
            'budget_max' => ['required', 'numeric', 'gte:budget_min'],
            'preferred_date' => ['required', 'date'],
            'adults' => ['nullable', 'integer', 'min:0', 'max:20'],
            'children' => ['nullable', 'integer', 'min:0', 'max:20'],
            'interests' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
        ]);

        $touristRequest->fill([
            'title' => $validated['title'],
            'location' => $validated['location'],
            'region' => $validated['region'] ?? null,
            'duration' => $validated['duration'] ?? null,
            'budget_min' => $validated['budget_min'],
            'budget_max' => $validated['budget_max'],
            'preferred_date' => $validated['preferred_date'],
            'adults' => $validated['adults'] ?? 0,
            'children' => $validated['children'] ?? 0,
            'passenger_count' => (int) ($validated['adults'] ?? 0) + (int) ($validated['children'] ?? 0),
            'interests' => $validated['interests'] ?? null,
            'description' => $validated['description'],
        ]);

        $touristRequest->save();
        $touristRequest->loadMissing(['selectedGuide:id,name,full_name,status,profile_photo_path']);
        $touristRequest->loadCount('comments');

        return response()->json([
            'message' => 'Post updated successfully.',
            'data' => $this->formatRequestPost($touristRequest),
        ]);
    }

    public function destroy(Request $request, TouristRequest $touristRequest): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->role === 'tourist', 403);
        abort_unless((int) $touristRequest->tourist_id === (int) $user->id, 403);

        $touristRequest->forceFill([
            'status' => 'cancelled',
            'completed_at' => null,
        ])->save();

        $touristRequest->loadMissing(['selectedGuide:id,name,full_name,status,profile_photo_path']);
        $touristRequest->loadCount('comments');

        return response()->json([
            'message' => 'Request cancelled successfully.',
            'data' => $this->formatRequestPost($touristRequest),
        ]);
    }

    public function markComplete(Request $request, TouristRequest $touristRequest): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->role === 'tourist', 403);
        abort_unless((int) $touristRequest->tourist_id === (int) $user->id, 403);

        $touristRequest->forceFill([
            'status' => 'completed',
            'completed_at' => now(),
        ])->save();

        $touristRequest->loadMissing(['selectedGuide:id,name,full_name,status,profile_photo_path']);
        $touristRequest->loadCount('comments');

        return response()->json([
            'message' => 'Trip marked as completed.',
            'data' => $this->formatRequestPost($touristRequest),
        ]);
    }

    public function comments(Request $request, TouristRequest $touristRequest): JsonResponse
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        abort_unless($this->canViewRequestComments($touristRequest, $user), 403);

        $comments = Comment::query()
            ->where('tourist_request_id', $touristRequest->id)
            ->with('user:id,name,full_name,status,profile_photo_path,role')
            ->orderBy('created_at')
            ->get();

        $ratings = $this->guideRatings($comments);

        return response()->json([
            'data' => $comments
                ->map(fn (Comment $comment): array => $this->formatComment($comment, $ratings))
                ->values(),
        ]);
    }

    public function storeComment(Request $request, TouristRequest $touristRequest): JsonResponse
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        $isGuide = in_array((string) $user->role, ['guide', 'tour_guide'], true);
        $isOwner = (int) $touristRequest->tourist_id === (int) $user->id;

        abort_unless($isGuide || $isOwner, 403);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
            'offer_price' => ['nullable', 'numeric', 'min:0'],
            'parent_id' => ['nullable', 'integer', 'exists:comments,id'],
        ]);

        $comment = Comment::query()->create([
            'tourist_request_id' => $touristRequest->id,
            'user_id' => $user->id,
            'body' => trim($validated['body']),
            'offer_price' => $isGuide ? ($validated['offer_price'] ?? null) : null,
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        if ($isGuide && $this->normalizeStatus($touristRequest->status) === 'open') {
            $touristRequest->forceFill(['status' => 'negotiating'])->save();
        }

        if ($isGuide) {
            $tourist = $touristRequest->tourist;

            if ($tourist instanceof User) {
                $tourist->notify(new TourActivityNotification(
                    title: 'New guide response',
                    message: ($user->full_name ?: $user->name ?: 'A guide').' responded to your request "'.($touristRequest->title ?: 'Custom Tour Request').'".',
                    icon: 'fa-comments',
                    type: 'request',
                    actionUrl: route('dashboard.my-posts'),
                ));
            }
        }

        $comment->loadMissing('user:id,name,full_name,status,profile_photo_path,role');

        return response()->json([
            'message' => 'Comment posted successfully.',
            'data' => $this->formatComment($comment, $this->guideRatings(collect([$comment]))),
            'post_status' => $this->normalizeStatus((string) $touristRequest->fresh()->status),
        ], 201);
    }

    public function selectGuide(Request $request, TouristRequest $touristRequest, Comment $comment): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->role === 'tourist', 403);
        abort_unless((int) $touristRequest->tourist_id === (int) $user->id, 403);
        abort_unless((int) $comment->tourist_request_id === (int) $touristRequest->id, 422);

        $guide = $comment->user;

        abort_unless($guide !== null, 422);
        abort_unless(in_array((string) $guide->role, ['guide', 'tour_guide'], true), 422);

        if (
            $touristRequest->selected_guide_id !== null
            && (int) $touristRequest->selected_guide_id !== (int) $guide->id
        ) {
            return response()->json([
                'message' => 'A guide is already selected for this request.',
            ], 422);
        }

        $touristRequest->forceFill([
            'selected_guide_id' => $guide->id,
            'selected_comment_id' => $comment->id,
            'status' => 'guide_selected',
        ])->save();

        $conversation = Conversation::query()->firstOrCreate(
            [
                'tourist_request_id' => $touristRequest->id,
                'tourist_id' => $touristRequest->tourist_id,
                'guide_id' => $guide->id,
            ],
            [
                'tour_id' => null,
                'last_message_at' => now(),
            ]
        );

        $conversation->loadMissing([
            'tourist:id,name,full_name,profile_photo_path,role',
            'guide:id,name,full_name,profile_photo_path,role',
            'latestMessage',
        ]);

        return response()->json([
            'message' => 'Guide selected. Private chat is ready.',
            'post' => $this->formatRequestPost($touristRequest->fresh(['selectedGuide'])->loadCount('comments')),
            'conversation' => $this->formatConversationSummary($conversation, (int) $user->id),
            'redirect_url' => route('dashboard.messages', ['conversation' => $conversation->id]),
            'messages' => $conversation->messages()
                ->with('sender:id,name,full_name,profile_photo_path')
                ->orderBy('created_at')
                ->limit(200)
                ->get()
                ->map(fn (Message $message): array => $this->formatMessage($message))
                ->values(),
        ]);
    }

    public function reportComment(Request $request, TouristRequest $touristRequest, Comment $comment): JsonResponse
    {
        $user = $request->user();
        abort_unless($user !== null, 401);
        abort_unless((int) $comment->tourist_request_id === (int) $touristRequest->id, 422);

        if ((int) $touristRequest->tourist_id !== (int) $user->id) {
            abort_unless(in_array((string) $user->role, ['guide', 'tour_guide'], true), 403);
        }

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $comment->forceFill([
            'is_reported' => true,
            'report_reason' => $validated['reason'] ?? 'Reported from My Posts page',
            'reported_at' => now(),
        ])->save();

        return response()->json([
            'message' => 'Comment reported successfully.',
        ]);
    }

    private function canViewRequestComments(TouristRequest $touristRequest, User $user): bool
    {
        if ((int) $touristRequest->tourist_id === (int) $user->id) {
            return true;
        }

        return in_array((string) $user->role, ['guide', 'tour_guide'], true);
    }

    /**
     * @return array<string, mixed>
     */
    private function formatRequestPost(TouristRequest $touristRequest): array
    {
        $status = $this->normalizeStatus($touristRequest->status);
        $interests = $this->parseInterests($touristRequest->interests);
        $preferredDate = $touristRequest->preferred_date;
        $preferredDateValue = null;

        if ($preferredDate instanceof \DateTimeInterface) {
            $preferredDateValue = $preferredDate->format('Y-m-d');
        } elseif (is_string($preferredDate) && trim($preferredDate) !== '') {
            $preferredDateValue = $preferredDate;
        }

        $recentComments = [];

        if ($touristRequest->relationLoaded('comments')) {
            $ratings = $this->guideRatings($touristRequest->comments);

            $recentComments = $touristRequest->comments
                ->map(fn (Comment $comment): array => $this->formatComment($comment, $ratings))
                ->values()
                ->all();
        }

        return [
            'id' => (int) $touristRequest->id,
            'title' => $touristRequest->title ?: 'Untitled Request',
            'destination' => $touristRequest->location ?: 'Philippines',
            'region' => $touristRequest->region,
            'duration' => $touristRequest->duration,
            'budget' => (float) ($touristRequest->budget_max ?? $touristRequest->budget_min ?? 0),
            'adults' => (int) ($touristRequest->adults ?? 0),
            'children' => (int) ($touristRequest->children ?? 0),
            'preferred_date' => $preferredDateValue,
            'interests' => $interests,
            'description' => $touristRequest->description ?: (string) $touristRequest->message,
            'posted_at' => $touristRequest->created_at?->toIso8601String(),
            'status' => $status,
            'status_label' => $this->statusLabel($status),
            'selected_guide_id' => $touristRequest->selected_guide_id,
            'selected_guide_name' => $touristRequest->selectedGuide?->full_name ?: $touristRequest->selectedGuide?->name,
            'selected_comment_id' => $touristRequest->selected_comment_id,
            'comments_count' => (int) ($touristRequest->comments_count ?? $touristRequest->comments()->count()),
            'recent_comments' => $recentComments,
        ];
    }

    /**
     * @param  Collection<int, Comment>  $comments
     * @return array<int, float>
     */
    private function guideRatings(Collection $comments): array
    {
        if (! Schema::hasTable('tour_reviews')) {
            return [];
        }

        $guideIds = $comments
            ->pluck('user_id')
            ->filter(fn (mixed $id): bool => $id !== null)
            ->map(fn (mixed $id): int => (int) $id)
            ->unique()
            ->values();

        if ($guideIds->isEmpty()) {
            return [];
        }

        return DB::table('tour_reviews')
            ->selectRaw('guide_id, AVG(rating) as avg_rating')
            ->whereIn('guide_id', $guideIds)
            ->groupBy('guide_id')
            ->pluck('avg_rating', 'guide_id')
            ->map(fn (mixed $value): float => round((float) $value, 1))
            ->all();
    }

    /**
     * @param  array<int, float>  $guideRatings
     * @return array<string, mixed>
     */
    private function formatComment(Comment $comment, array $guideRatings): array
    {
        $guide = $comment->user;
        $isGuide = in_array((string) $guide?->role, ['guide', 'tour_guide'], true);

        return [
            'id' => (int) $comment->id,
            'request_post_id' => (int) $comment->tourist_request_id,
            'guide_id' => $comment->user_id,
            'guide_name' => $guide?->full_name ?: $guide?->name ?: 'Guide',
            'guide_avatar_url' => $guide?->profile_photo_path ? asset('storage/'.$guide->profile_photo_path) : null,
            'is_verified' => $isGuide && (string) $guide?->status === 'active',
            'rating' => $guide ? ($guideRatings[(int) $guide->id] ?? null) : null,
            'message' => (string) $comment->body,
            'offered_price' => $comment->offer_price !== null ? (float) $comment->offer_price : null,
            'timestamp' => $comment->created_at?->toIso8601String(),
            'parent_id' => $comment->parent_id,
            'is_reported' => (bool) $comment->is_reported,
            'role' => $isGuide ? 'guide' : 'tourist',
        ];
    }

    private function normalizeStatus(?string $status): string
    {
        $normalized = strtolower(trim((string) $status));

        return match ($normalized) {
            'open' => 'open',
            'negotiating' => 'negotiating',
            'guide_selected', 'selected' => 'guide_selected',
            'completed', 'done' => 'completed',
            'cancelled', 'canceled' => 'cancelled',
            default => 'open',
        };
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            'open' => 'Open',
            'negotiating' => 'Negotiating',
            'guide_selected' => 'Guide Selected',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => 'Open',
        };
    }

    /**
     * @return array<int, string>
     */
    private function parseInterests(?string $interests): array
    {
        if ($interests === null || trim($interests) === '') {
            return [];
        }

        $decoded = json_decode($interests, true);

        if (is_array($decoded)) {
            return collect($decoded)
                ->map(fn (mixed $item): string => trim((string) $item))
                ->filter(fn (string $item): bool => $item !== '')
                ->values()
                ->all();
        }

        return collect(explode(',', $interests))
            ->map(fn (string $item): string => trim($item))
            ->filter(fn (string $item): bool => $item !== '')
            ->values()
            ->all();
    }

    private function seedDemoContentIfNeeded(User $tourist): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $existingCount = TouristRequest::query()
            ->where('tourist_id', $tourist->id)
            ->count();

        if ($existingCount > 0) {
            return;
        }

        $maria = User::query()->firstOrCreate(
            ['email' => 'maria.santos.guide@trbltours.demo'],
            [
                'name' => 'Maria Santos',
                'full_name' => 'Maria Santos',
                'role' => 'tour_guide',
                'status' => 'active',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $john = User::query()->firstOrCreate(
            ['email' => 'john.reyes.guide@trbltours.demo'],
            [
                'name' => 'John Reyes',
                'full_name' => 'John Reyes',
                'role' => 'tour_guide',
                'status' => 'active',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $carlo = User::query()->firstOrCreate(
            ['email' => 'carlo.diaz.guide@trbltours.demo'],
            [
                'name' => 'Carlo Diaz',
                'full_name' => 'Carlo Diaz',
                'role' => 'tour_guide',
                'status' => 'active',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $postOne = TouristRequest::query()->create([
            'tourist_id' => $tourist->id,
            'title' => 'Island Hopping in Samal',
            'location' => 'Samal Island',
            'region' => 'Davao Region',
            'duration' => '1 Day',
            'budget_min' => 5000,
            'budget_max' => 5000,
            'adults' => 2,
            'children' => 0,
            'passenger_count' => 2,
            'preferred_date' => '2026-06-10',
            'interests' => 'Beach, Island Hopping, Food',
            'description' => 'Looking for a smooth island hopping itinerary with snorkeling and lunch options.',
            'status' => 'negotiating',
        ]);

        $postTwo = TouristRequest::query()->create([
            'tourist_id' => $tourist->id,
            'title' => 'Tagum Food Crawl Adventure',
            'location' => 'Tagum City',
            'region' => 'Davao Region',
            'duration' => 'Half-day',
            'budget_min' => 2000,
            'budget_max' => 2000,
            'adults' => 2,
            'children' => 0,
            'passenger_count' => 2,
            'preferred_date' => now()->addDays(14)->toDateString(),
            'interests' => 'Food, Local Culture',
            'description' => 'Need a friendly local guide for a curated food crawl with must-try local spots.',
            'status' => 'negotiating',
        ]);

        Comment::query()->create([
            'tourist_request_id' => $postOne->id,
            'user_id' => $maria->id,
            'body' => 'I can provide a full package with boat, entrance fees, and snorkeling stops.',
            'offer_price' => 4800,
        ]);

        Comment::query()->create([
            'tourist_request_id' => $postOne->id,
            'user_id' => $john->id,
            'body' => 'I can do this at ₱4,500 and include lunch at a beachfront restaurant.',
            'offer_price' => 4500,
        ]);

        Comment::query()->create([
            'tourist_request_id' => $postTwo->id,
            'user_id' => $carlo->id,
            'body' => 'I am available weekends only. I can create a route for street food and local cafes.',
            'offer_price' => null,
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function demoPosts(): array
    {
        return [
            [
                'title' => 'Island Hopping in Samal',
                'budget' => 5000,
                'preferred_date' => '2026-06-10',
                'offers' => [
                    'Maria Santos: ₱4,800 full package',
                    'John Reyes: ₱4,500 with lunch included',
                ],
            ],
            [
                'title' => 'Tagum Food Crawl Adventure',
                'budget' => 2000,
                'preferred_date' => null,
                'offers' => [
                    'Carlo Diaz: Available weekends only',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function formatConversationSummary(Conversation $conversation, int $currentUserId): array
    {
        $participant = $conversation->tourist_id === $currentUserId
            ? $conversation->guide
            : $conversation->tourist;

        $latestBody = null;

        if ($conversation->latestMessage) {
            $latestBody = (string) ($conversation->latestMessage->body ?: $conversation->latestMessage->message);
        }

        return [
            'id' => $conversation->id,
            'participant' => [
                'id' => $participant?->id,
                'name' => $participant?->full_name ?: $participant?->name ?: 'Guide',
                'role' => $participant?->role,
                'avatar_url' => $participant?->profile_photo_path ? asset('storage/'.$participant->profile_photo_path) : null,
            ],
            'last_message' => $latestBody,
            'last_message_at' => $conversation->latestMessage?->created_at?->toIso8601String() ?? $conversation->last_message_at?->toIso8601String(),
            'unread_count' => 0,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function formatMessage(Message $message): array
    {
        return [
            'id' => $message->id,
            'body' => (string) ($message->body ?: $message->message),
            'sender_id' => $message->sender_id,
            'sender_name' => $message->sender?->full_name ?: $message->sender?->name ?: 'User',
            'sender_avatar_url' => $message->sender?->profile_photo_path ? asset('storage/'.$message->sender->profile_photo_path) : null,
            'created_at' => $message->created_at?->toIso8601String(),
            'read_at' => $message->read_at?->toIso8601String(),
        ];
    }
}
