<?php

namespace App\Http\Controllers;

use App\Models\BookingRequest;
use App\Models\Tour;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function redirect(Request $request): RedirectResponse
    {
        return redirect()->route($request->user()->dashboardRouteName());
    }

    public function tourist(Request $request): View
    {
        $user = $request->user();

        abort_unless($user->role === 'tourist', 403);

        $regions = [
            'National Capital Region',
            'Cordillera Administrative Region',
            'Ilocos Region',
            'Cagayan Valley',
            'Central Luzon',
            'CALABARZON',
            'MIMAROPA',
            'Bicol Region',
            'Western Visayas',
            'Central Visayas',
            'Eastern Visayas',
            'Zamboanga Peninsula',
            'Northern Mindanao',
            'Davao Region',
            'SOCCSKSARGEN',
            'Caraga',
            'BARMM',
        ];

        $location = trim((string) $request->query('location', ''));
        $postType = (string) $request->query('post_type', 'all');
        $sortBy = (string) $request->query('sort_by', 'latest');
        $perPage = 12;

        if (! in_array($postType, ['all', 'tour_listings', 'request_posts'], true)) {
            $postType = 'all';
        }

        if (! in_array($sortBy, ['latest', 'price_low_high', 'price_high_low'], true)) {
            $sortBy = 'latest';
        }

        $tourListings = collect();
        if (in_array($postType, ['all', 'tour_listings'], true)) {
            $userColumns = Schema::getColumnListing('users');

            $tourListings = Tour::query()
                ->with(['marketplaceGuide:id,name,full_name,role,status'])
                ->whereHas('marketplaceGuide', function (Builder $query) use ($userColumns): void {
                    $query->whereIn('role', ['guide', 'tour_guide'])
                        ->where('status', 'active');

                    $query->where(function (Builder $verificationQuery) use ($userColumns): void {
                        $hasAnyVerificationColumn = false;

                        if (in_array('verification_status', $userColumns, true)) {
                            $verificationQuery->orWhere('verification_status', 'verified');
                            $hasAnyVerificationColumn = true;
                        }

                        if (in_array('is_verified', $userColumns, true)) {
                            $verificationQuery->orWhere('is_verified', true);
                            $hasAnyVerificationColumn = true;
                        }

                        if (in_array('approved_by_admin', $userColumns, true)) {
                            $verificationQuery->orWhere('approved_by_admin', true);
                            $hasAnyVerificationColumn = true;
                        }

                        if (! $hasAnyVerificationColumn) {
                            $verificationQuery->orWhereRaw('1 = 1');
                        }
                    });
                })
                ->when($location !== '', function ($query) use ($location): void {
                    $query->where('region', $location);
                })
                ->latest()
                ->get()
                ->map(function (Tour $tour): array {
                    return [
                        'type' => 'tour_listing',
                        'id' => (int) $tour->id,
                        'created_at' => $tour->created_at,
                        'price_value' => (float) ($tour->price_per_person ?? $tour->price ?? 0),
                        'data' => $tour,
                    ];
                });
        }

        $requestPosts = collect();
        if (in_array($postType, ['all', 'request_posts'], true)) {
            $requestPosts = BookingRequest::query()
                ->select('booking_requests.*')
                ->addSelect([
                    'comment_count' => DB::table('messages')
                        ->join('conversations', 'conversations.id', '=', 'messages.conversation_id')
                        ->selectRaw('count(messages.id)')
                        ->whereColumn('conversations.tourist_id', 'booking_requests.tourist_id')
                        ->whereColumn('conversations.guide_id', 'booking_requests.guide_id')
                        ->whereColumn('conversations.tour_id', 'booking_requests.tour_id'),
                ])
                ->with([
                    'tourist:id,name,full_name',
                    'tour:id,title,region,duration_label',
                ])
                ->when($location !== '', function ($query) use ($location): void {
                    $query->whereHas('tour', function ($tourQuery) use ($location): void {
                        $tourQuery->where('region', $location);
                    });
                })
                ->latest()
                ->get()
                ->map(function (BookingRequest $bookingRequest): array {
                    return [
                        'type' => 'request_post',
                        'id' => (int) $bookingRequest->id,
                        'created_at' => $bookingRequest->created_at,
                        'price_value' => (float) $bookingRequest->total_price,
                        'data' => $bookingRequest,
                    ];
                });
        }

        $posts = $tourListings->concat($requestPosts);

        $posts = match ($sortBy) {
            'price_low_high' => $posts->sortBy('price_value')->values(),
            'price_high_low' => $posts->sortByDesc('price_value')->values(),
            default => $posts->sortByDesc('created_at')->values(),
        };

        $page = LengthAwarePaginator::resolveCurrentPage();
        $total = $posts->count();
        $results = $posts->slice(($page - 1) * $perPage, $perPage)->values();

        $paginatedPosts = new LengthAwarePaginator(
            $results,
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $bootstrapTours = $results
            ->filter(fn (array $item): bool => ($item['type'] ?? '') === 'tour_listing')
            ->map(fn (array $item): array => $this->transformTourForFrontend($item['data']))
            ->values();

        $bootstrapRequests = $results
            ->filter(fn (array $item): bool => ($item['type'] ?? '') === 'request_post')
            ->map(fn (array $item): array => $this->transformRequestForFrontend($item['data']))
            ->values();

        $bootstrapMyPosts = BookingRequest::query()
            ->with(['tour:id,title,region'])
            ->where('tourist_id', $user->id)
            ->latest()
            ->limit(12)
            ->get()
            ->map(fn (BookingRequest $bookingRequest): array => $this->transformRequestForFrontend($bookingRequest, true))
            ->values();

        return view('dashboards.tourist', [
            'regions' => $regions,
            'location' => $location,
            'postType' => $postType,
            'sortBy' => $sortBy,
            'posts' => $paginatedPosts,
            'bootstrapTours' => $bootstrapTours,
            'bootstrapRequests' => $bootstrapRequests,
            'bootstrapMyPosts' => $bootstrapMyPosts,
        ]);
    }

    public function touristApiTours(Request $request): JsonResponse
    {
        $user = $request->user();

        abort_unless($user?->role === 'tourist', 403);

        $location = trim((string) $request->query('location', ''));
        $sortBy = (string) $request->query('sort_by', 'latest');

        if (! in_array($sortBy, ['latest', 'price_low_high', 'price_high_low'], true)) {
            $sortBy = 'latest';
        }

        $query = Tour::query()
            ->with(['marketplaceGuide:id,name,full_name,role,status'])
            ->whereHas('marketplaceGuide', function (Builder $builder): void {
                $builder->whereIn('role', ['guide', 'tour_guide'])
                    ->where('status', 'active');
            })
            ->when($location !== '', function (Builder $builder) use ($location): void {
                $builder->where('region', 'like', "%{$location}%");
            });

        if ($sortBy === 'price_low_high') {
            $query->orderByRaw('COALESCE(price_per_person, price, 0) asc');
        } elseif ($sortBy === 'price_high_low') {
            $query->orderByRaw('COALESCE(price_per_person, price, 0) desc');
        } else {
            $query->latest();
        }

        $data = $query
            ->limit(36)
            ->get()
            ->map(fn (Tour $tour): array => $this->transformTourForFrontend($tour))
            ->values();

        return response()->json(['data' => $data]);
    }

    public function touristApiRequests(Request $request): JsonResponse
    {
        $user = $request->user();

        abort_unless($user?->role === 'tourist', 403);

        $location = trim((string) $request->query('location', ''));
        $sortBy = (string) $request->query('sort_by', 'latest');

        if (! in_array($sortBy, ['latest', 'price_low_high', 'price_high_low'], true)) {
            $sortBy = 'latest';
        }

        $query = BookingRequest::query()
            ->addSelect([
                'comment_count' => DB::table('messages')
                    ->join('conversations', 'conversations.id', '=', 'messages.conversation_id')
                    ->selectRaw('count(messages.id)')
                    ->whereColumn('conversations.tourist_id', 'booking_requests.tourist_id')
                    ->whereColumn('conversations.guide_id', 'booking_requests.guide_id')
                    ->whereColumn('conversations.tour_id', 'booking_requests.tour_id'),
            ])
            ->with(['tour:id,title,region'])
            ->when($location !== '', function (Builder $builder) use ($location): void {
                $builder->whereHas('tour', function (Builder $tourQuery) use ($location): void {
                    $tourQuery->where('region', 'like', "%{$location}%");
                });
            });

        if ($sortBy === 'price_low_high') {
            $query->orderBy('total_price');
        } elseif ($sortBy === 'price_high_low') {
            $query->orderByDesc('total_price');
        } else {
            $query->latest();
        }

        $data = $query
            ->limit(36)
            ->get()
            ->map(fn (BookingRequest $bookingRequest): array => $this->transformRequestForFrontend($bookingRequest))
            ->values();

        return response()->json(['data' => $data]);
    }

    public function touristApiMyPosts(Request $request): JsonResponse
    {
        $user = $request->user();

        abort_unless($user?->role === 'tourist', 403);

        $data = BookingRequest::query()
            ->where('tourist_id', $user->id)
            ->with(['tour:id,title,region'])
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn (BookingRequest $bookingRequest): array => $this->transformRequestForFrontend($bookingRequest, true))
            ->values();

        return response()->json(['data' => $data]);
    }

    public function touristApiUpdateMyPost(Request $request, BookingRequest $bookingRequest): JsonResponse
    {
        $user = $request->user();

        abort_unless($user?->role === 'tourist', 403);
        abort_unless((int) $bookingRequest->tourist_id === (int) $user->id, 403);

        $validated = $request->validate([
            'budget' => ['nullable', 'numeric', 'min:0'],
            'vibe' => ['nullable', 'string', 'max:255'],
            'traveler_count' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $bookingRequest->fill([
            'total_price' => $validated['budget'] ?? $bookingRequest->total_price,
            'special_requests' => $validated['vibe'] ?? $bookingRequest->special_requests,
            'group_size' => $validated['traveler_count'] ?? $bookingRequest->group_size,
        ]);

        $bookingRequest->save();
        $bookingRequest->loadMissing('tour:id,title,region');

        return response()->json([
            'message' => 'Post updated successfully.',
            'data' => $this->transformRequestForFrontend($bookingRequest, true),
        ]);
    }

    public function touristApiDeleteMyPost(Request $request, BookingRequest $bookingRequest): JsonResponse
    {
        $user = $request->user();

        abort_unless($user?->role === 'tourist', 403);
        abort_unless((int) $bookingRequest->tourist_id === (int) $user->id, 403);

        $bookingRequest->delete();

        return response()->json(['message' => 'Post deleted successfully.']);
    }

    public function guide(Request $request): View
    {
        $user = $request->user();

        abort_unless(in_array($user->role, ['guide', 'tour_guide'], true), 403);

        return view('dashboards.guide-unavailable');
    }

    // Scroll nav CSS in blade

    public function admin(): View
    {
        abort_unless(request()->user()?->role === 'admin', 403);

        $totalUsers = User::query()->count();
        $activeGuides = User::query()
            ->whereIn('role', ['guide', 'tour_guide'])
            ->where('status', 'active')
            ->count();

        $featuredTours = Schema::hasTable('tours') && Schema::hasColumn('tours', 'is_featured')
            ? Tour::query()->where('is_featured', true)->count()
            : 0;

        $pendingGuideApprovals = Schema::hasTable('tour_guides_profile') && Schema::hasColumn('tour_guides_profile', 'approved_by_admin')
            ? DB::table('tour_guides_profile')->where('approved_by_admin', false)->count()
            : 0;

        $recentUsers = User::query()
            ->select(['name', 'email', 'role', 'status', 'created_at'])
            ->latest()
            ->limit(8)
            ->get();

        return view('dashboards.admin', [
            'stats' => [
                'total_users' => $totalUsers,
                'active_guides' => $activeGuides,
                'pending_guide_approvals' => $pendingGuideApprovals,
                'featured_tours' => $featuredTours,
            ],
            'recentUsers' => $recentUsers,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function getGuideProfile(int $userId): array
    {
        if (! Schema::hasTable('tour_guides_profile')) {
            return [];
        }

        return (array) (DB::table('tour_guides_profile')->where('user_id', $userId)->first() ?? []);
    }

    private function firstName(string $name): string
    {
        $trimmed = trim($name);

        if ($trimmed === '') {
            return 'Guide';
        }

        return explode(' ', $trimmed)[0];
    }

    /**
     * @param  array<string, mixed>  $record
     * @param  array<int, string>  $keys
     */
    private function isFilled(array $record, array $keys): bool
    {
        foreach ($keys as $key) {
            if (! array_key_exists($key, $record)) {
                continue;
            }

            $value = $record[$key];

            if (is_string($value) && trim($value) !== '') {
                return true;
            }

            if (is_numeric($value) && (string) $value !== '') {
                return true;
            }
        }

        return false;
    }

    private function hasAtLeastOneSelection(mixed $value): bool
    {
        if ($value === null) {
            return false;
        }

        if (is_array($value)) {
            return count(array_filter($value, fn (mixed $item): bool => filled($item))) >= 1;
        }

        if (! is_string($value)) {
            return false;
        }

        $decoded = json_decode($value, true);
        if (is_array($decoded)) {
            return count(array_filter($decoded, fn (mixed $item): bool => filled($item))) >= 1;
        }

        return collect(explode(',', $value))
            ->map(fn (string $item): string => trim($item))
            ->filter(fn (string $item): bool => $item !== '')
            ->isNotEmpty();
    }

    /**
     * @param  array<string, mixed>  $profile
     */
    private function hasPendingVerification(array $profile): bool
    {
        $pendingPairs = [
            ['path' => 'id_front_path', 'verified' => 'id_front_verified'],
            ['path' => 'id_back_path', 'verified' => 'id_back_verified'],
            ['path' => 'selfie_path', 'verified' => 'selfie_verified'],
            ['path' => 'nbi_clearance_path', 'verified' => 'nbi_clearance_validated'],
        ];

        foreach ($pendingPairs as $pair) {
            $pathValue = $profile[$pair['path']] ?? null;
            $verificationValue = $profile[$pair['verified']] ?? null;

            if (is_string($pathValue) && trim($pathValue) !== '' && ! (bool) $verificationValue) {
                return true;
            }
        }

        if (array_key_exists('approved_by_admin', $profile) && ! (bool) $profile['approved_by_admin']) {
            return $this->isFilled($profile, ['id_front_path', 'id_back_path', 'selfie_path', 'nbi_clearance_path', 'barangay_clearance_path']);
        }

        return false;
    }

    /**
     * @return array<string, mixed>
     */
    private function transformTourForFrontend(Tour $tour): array
    {
        $guide = $tour->marketplaceGuide;

        return [
            'id' => (int) $tour->id,
            'type' => 'tour',
            'title' => $tour->title ?? $tour->name ?? 'Untitled Tour',
            'price' => (float) ($tour->price_per_person ?? $tour->price ?? 0),
            'location' => $tour->region ?? 'Philippines',
            'image_url' => $this->resolveImageUrl($tour->image_path, $tour->image_url),
            'guide_name' => $guide?->full_name ?? $guide?->name ?? 'Verified Guide',
            'verified_guide' => true,
            'created_at' => $tour->created_at?->toIso8601String(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function transformRequestForFrontend(BookingRequest $bookingRequest, bool $isMyPost = false): array
    {
        $requestedDate = $bookingRequest->requested_date;

        return [
            'id' => (int) $bookingRequest->id,
            'type' => 'request',
            'title' => $bookingRequest->tour?->title ?? 'Custom Tour Request',
            'budget' => (float) $bookingRequest->total_price,
            'vibe' => $bookingRequest->special_requests ?: 'Flexible vibe',
            'traveler_count' => (int) ($bookingRequest->group_size ?? 1),
            'comment_count' => (int) ($bookingRequest->comment_count ?? 0),
            'location' => $bookingRequest->tour?->region ?? 'Philippines',
            'image_url' => $this->resolveImageUrl($bookingRequest->tour?->image_path, $bookingRequest->tour?->image_url),
            'status' => $isMyPost
                ? $this->requestStatusLabel($bookingRequest->status, $requestedDate)
                : null,
            'created_at' => $bookingRequest->created_at?->toIso8601String(),
        ];
    }

    private function requestStatusLabel(?string $status, CarbonInterface|string|null $requestedDate): string
    {
        if (is_string($status) && in_array(strtolower($status), ['expired', 'cancelled', 'canceled'], true)) {
            return 'Expired';
        }

        if ($requestedDate instanceof CarbonInterface && $requestedDate->isPast()) {
            return 'Expired';
        }

        return 'Open';
    }

    private function resolveImageUrl(?string $imagePath, ?string $imageUrl): ?string
    {
        if (is_string($imageUrl) && trim($imageUrl) !== '') {
            return $imageUrl;
        }

        if (is_string($imagePath) && trim($imagePath) !== '') {
            return asset($imagePath);
        }

        return null;
    }
}
