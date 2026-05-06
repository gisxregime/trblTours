<?php

namespace App\Http\Controllers;

use App\Events\ConversationRead;
use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DashboardMessagesController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        $userId = (int) $user->id;
        $selectedConversationId = $request->integer('conversation');

        if (! $selectedConversationId) {
            $selectedConversationId = $this->resolveTourConversationId($request, $user);
        }

        $conversations = $this->conversationQuery($userId)->get();

        if (! $selectedConversationId && $conversations->isNotEmpty()) {
            $selectedConversationId = (int) $conversations->first()->id;
        }

        $selectedConversation = null;
        $selectedMessages = collect();

        if ($selectedConversationId) {
            $selectedConversation = $this->conversationQuery($userId)
                ->whereKey($selectedConversationId)
                ->first();

            if ($selectedConversation) {
                $this->markConversationAsRead($selectedConversation, $userId);

                $selectedMessages = $this->conversationMessages($selectedConversation)
                    ->map(fn (Message $message): array => $this->formatMessage($message));
            }
        }

        $dashboardRouteName = $user->dashboardRouteName();
        $dashboardUrl = Route::has($dashboardRouteName)
            ? route($dashboardRouteName)
            : route('dashboard.tourist');

        return view('dashboards.messages', [
            'currentUserId' => $userId,
            'dashboardUrl' => $dashboardUrl,
            'conversations' => $conversations
                ->map(fn (Conversation $conversation): array => $this->formatConversationSummary($conversation, $userId))
                ->values(),
            'selectedConversationId' => $selectedConversation?->id,
            'selectedConversation' => $selectedConversation
                ? $this->formatConversationSummary($selectedConversation, $userId, unreadCountOverride: 0)
                : null,
            'selectedMessages' => $selectedMessages,
        ]);
    }

    private function resolveTourConversationId(Request $request, User $user): ?int
    {
        if ((string) $user->role !== 'tourist') {
            return null;
        }

        $tourId = $request->integer('tour');

        if ($tourId <= 0) {
            return null;
        }

        $tour = Tour::query()
            ->select(['id', 'guide_id'])
            ->whereKey($tourId)
            ->first();

        $guideId = (int) ($tour?->guide_id ?? 0);

        if (! $tour || $guideId <= 0 || $guideId === (int) $user->id) {
            return null;
        }

        $conversation = Conversation::query()->firstOrCreate(
            [
                'tourist_id' => (int) $user->id,
                'guide_id' => $guideId,
                'tour_id' => (int) $tour->id,
            ],
            [
                'last_message_at' => now(),
            ]
        );

        return (int) $conversation->id;
    }

    public function show(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        $userId = (int) $user->id;
        $this->ensureUserBelongsToConversation($conversation, $userId);

        $conversation = $this->conversationQuery($userId)
            ->whereKey($conversation->id)
            ->firstOrFail();

        $this->markConversationAsRead($conversation, $userId);

        return response()->json([
            'conversation' => $this->formatConversationSummary($conversation, $userId, unreadCountOverride: 0),
            'messages' => $this->conversationMessages($conversation)
                ->map(fn (Message $message): array => $this->formatMessage($message))
                ->values(),
        ]);
    }

    public function store(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        $userId = (int) $user->id;
        $this->ensureUserBelongsToConversation($conversation, $userId);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $body = trim($validated['body']);

        $messagePayload = [
            'sender_id' => $userId,
        ];

        if (Schema::hasColumn('messages', 'body')) {
            $messagePayload['body'] = $body;
        }

        if (Schema::hasColumn('messages', 'message')) {
            $messagePayload['message'] = $body;
        }

        if (Schema::hasColumn('messages', 'read_at')) {
            $messagePayload['read_at'] = null;
        }

        if (Schema::hasColumn('messages', 'is_read')) {
            $messagePayload['is_read'] = false;
        }

        $message = $conversation->messages()->create($messagePayload);

        $conversation->forceFill([
            'last_message_at' => $message->created_at,
        ])->save();

        $message->load('sender:id,name,full_name,profile_photo_path');

        $conversation = $this->conversationQuery($userId)
            ->whereKey($conversation->id)
            ->firstOrFail();

        broadcast(new MessageSent($conversation, $message));

        return response()->json([
            'conversation' => $this->formatConversationSummary($conversation, $userId),
            'message' => $this->formatMessage($message),
        ], 201);
    }

    public function markRead(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        $userId = (int) $user->id;
        $this->ensureUserBelongsToConversation($conversation, $userId);

        $unreadBefore = $this->unreadCountForParticipant($conversation, $userId);

        $remainingUnreadCount = $this->markConversationAsRead($conversation, $userId);

        if ($unreadBefore > 0) {
            broadcast(new ConversationRead($conversation, $userId, now()->toIso8601String()));
        }

        return response()->json([
            'conversation_id' => $conversation->id,
            'unread_count' => $remainingUnreadCount,
        ]);
    }

    private function conversationQuery(int $userId): Builder
    {
        return Conversation::query()
            ->with([
                'tourist:id,name,full_name,profile_photo_path,role',
                'guide:id,name,full_name,profile_photo_path,role',
                'latestMessage',
            ])
            ->withCount([
                'messages as unread_count' => function (Builder $query) use ($userId): void {
                    $query->where('sender_id', '!=', $userId);

                    if (Schema::hasColumn('messages', 'read_at')) {
                        $query->whereNull('read_at');
                    } else {
                        $query->where('is_read', false);
                    }
                },
            ])
            ->where(fn (Builder $query) => $query
                ->where('tourist_id', $userId)
                ->orWhere('guide_id', $userId)
            )
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at');
    }

    /**
     * @return Collection<int, Message>
     */
    private function conversationMessages(Conversation $conversation): Collection
    {
        return $conversation->messages()
            ->with('sender:id,name,full_name,profile_photo_path')
            ->orderBy('created_at')
            ->limit(200)
            ->get();
    }

    private function ensureUserBelongsToConversation(Conversation $conversation, int $userId): void
    {
        abort_unless(
            $conversation->tourist_id === $userId || $conversation->guide_id === $userId,
            403
        );
    }

    private function markConversationAsRead(Conversation $conversation, int $userId): int
    {
        $query = $conversation->messages()
            ->where('sender_id', '!=', $userId);

        if (Schema::hasColumn('messages', 'read_at')) {
            $query->whereNull('read_at');
        } else {
            $query->where('is_read', false);
        }

        $updates = [];

        if (Schema::hasColumn('messages', 'read_at')) {
            $updates['read_at'] = now();
        }

        if (Schema::hasColumn('messages', 'is_read')) {
            $updates['is_read'] = true;
        }

        if ($updates !== []) {
            $query->update($updates);
        }

        $remainingUnreadQuery = $conversation->messages()
            ->where('sender_id', '!=', $userId);

        if (Schema::hasColumn('messages', 'read_at')) {
            $remainingUnreadQuery->whereNull('read_at');
        } else {
            $remainingUnreadQuery->where('is_read', false);
        }

        return (int) $remainingUnreadQuery->count();
    }

    private function unreadCountForParticipant(Conversation $conversation, int $participantId): int
    {
        $query = $conversation->messages()
            ->where('sender_id', '!=', $participantId);

        if (Schema::hasColumn('messages', 'read_at')) {
            $query->whereNull('read_at');
        } else {
            $query->where('is_read', false);
        }

        return (int) $query->count();
    }

    /**
     * @return array<string, mixed>
     */
    private function formatConversationSummary(Conversation $conversation, int $userId, ?int $unreadCountOverride = null): array
    {
        $participant = $conversation->tourist_id === $userId
            ? $conversation->guide
            : $conversation->tourist;

        $lastMessageBody = $this->messageBodyFromModel($conversation->latestMessage);
        $lastMessageAt = $conversation->latestMessage?->created_at?->toIso8601String()
            ?? $conversation->last_message_at?->toIso8601String();

        return [
            'id' => $conversation->id,
            'participant' => [
                'id' => $participant?->id,
                'name' => $participant?->full_name ?: $participant?->name ?: 'User',
                'role' => $participant?->role,
                'avatar_url' => $this->avatarUrl($participant),
            ],
            'last_message' => $lastMessageBody,
            'last_message_at' => $lastMessageAt,
            'unread_count' => $unreadCountOverride ?? (int) ($conversation->unread_count ?? 0),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function formatMessage(Message $message): array
    {
        return [
            'id' => $message->id,
            'body' => $this->messageBodyFromModel($message),
            'sender_id' => $message->sender_id,
            'sender_name' => $message->sender?->full_name ?: $message->sender?->name ?: 'User',
            'sender_avatar_url' => $this->avatarUrl($message->sender),
            'created_at' => $message->created_at?->toIso8601String(),
            'read_at' => $message->read_at?->toIso8601String(),
        ];
    }

    private function messageBodyFromModel(?Message $message): ?string
    {
        if (! $message) {
            return null;
        }

        $body = $message->getAttribute('body');

        if (is_string($body) && $body !== '') {
            return $body;
        }

        $legacyMessage = $message->getAttribute('message');

        return is_string($legacyMessage) ? $legacyMessage : null;
    }

    private function avatarUrl(?User $user): ?string
    {
        if (! $user || ! $user->profile_photo_path) {
            return null;
        }

        return Storage::url($user->profile_photo_path);
    }
}
