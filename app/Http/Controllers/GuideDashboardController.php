<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Tour;
use App\Models\TouristRequest;
use App\Models\User;
use App\Notifications\TourActivityNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class GuideDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        abort_unless($user !== null, 401);
        abort_unless(in_array((string) $user->role, ['guide', 'tour_guide'], true), 403);

        $requestPosts = TouristRequest::query()
            ->with([
                'tourist:id,name,full_name,profile_photo_path',
                'comments' => fn ($query) => $query
                    ->with('user:id,name,full_name,role,profile_photo_path')
                    ->latest()
                    ->limit(3),
            ])
            ->withCount('comments')
            ->whereIn('status', ['open', 'negotiating'])
            ->latest()
            ->limit(60)
            ->get();

        $guideListings = $this->guideToursQuery((int) $user->id)
            ->latest()
            ->limit(30)
            ->get();

        return view('dashboards.guide', [
            'requestPosts' => $requestPosts,
            'guideListings' => $guideListings,
        ]);
    }

    public function storeRequestComment(Request $request, TouristRequest $touristRequest): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user !== null, 401);
        abort_unless(in_array((string) $user->role, ['guide', 'tour_guide'], true), 403);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
            'offer_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        Comment::query()->create([
            'tourist_request_id' => $touristRequest->id,
            'user_id' => $user->id,
            'body' => trim((string) $validated['body']),
            'offer_price' => $validated['offer_price'] ?? null,
        ]);

        if (strtolower((string) $touristRequest->status) === 'open') {
            $touristRequest->forceFill(['status' => 'negotiating'])->save();
        }

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

        return back()->with('status', 'Response posted successfully. The tourist has been notified.');
    }

    public function storeListing(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user !== null, 401);
        abort_unless(in_array((string) $user->role, ['guide', 'tour_guide'], true), 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'region' => ['required', 'string', 'max:160'],
            'location' => ['required', 'string', 'max:255'],
            'short_description' => ['required', 'string', 'max:150'],
            'cover_image' => ['required', 'image', 'max:4096'],
            'gallery_images' => ['required', 'array', 'min:3', 'max:5'],
            'gallery_images.*' => ['required', 'image', 'max:4096'],
            'guide_name' => ['nullable', 'string', 'max:120'],
            'guide_bio' => ['nullable', 'string', 'max:255'],
            'duration_label' => ['required', 'string', 'max:120'],
            'activity_types' => ['nullable', 'array', 'max:7'],
            'activity_types.*' => ['string', 'max:60'],
            'difficulty' => ['nullable', 'string', 'max:60'],
            'min_guests' => ['required', 'integer', 'min:1', 'max:100'],
            'max_guests' => ['required', 'integer', 'min:1', 'max:100', 'gte:min_guests'],
            'languages_spoken' => ['nullable', 'array', 'max:10'],
            'languages_spoken.*' => ['string', 'max:80'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'price_type' => ['required', 'in:per_person,per_group,custom_quote'],
            'weekend_rate' => ['nullable', 'numeric', 'min:0'],
            'holiday_rate' => ['nullable', 'numeric', 'min:0'],
            'overview_description' => ['nullable', 'string', 'max:12000'],
            'itinerary_pickup' => ['nullable', 'string', 'max:255'],
            'itinerary_stop_1' => ['nullable', 'string', 'max:255'],
            'itinerary_stop_2' => ['nullable', 'string', 'max:255'],
            'itinerary_lunch' => ['nullable', 'string', 'max:255'],
            'itinerary_return' => ['nullable', 'string', 'max:255'],
            'included' => ['nullable', 'array', 'max:20'],
            'included.*' => ['string', 'max:120'],
            'exclusions_text' => ['nullable', 'string', 'max:1200'],
            'available_days' => ['nullable', 'array', 'max:7'],
            'available_days.*' => ['in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'instant_book' => ['nullable', 'boolean'],
            'requires_approval' => ['nullable', 'boolean'],
            'max_slots' => ['nullable', 'integer', 'min:1', 'max:100'],
            'verified_id_upload' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'safety_certified' => ['nullable', 'boolean'],
            'eco_friendly' => ['nullable', 'boolean'],
            'audience_badges' => ['nullable', 'array', 'max:3'],
            'audience_badges.*' => ['in:families,couples,solo'],
        ]);

        $coverImagePath = $request->file('cover_image')?->store('guide/tour-cover', 'public');

        $galleryImages = collect($request->file('gallery_images', []))
            ->take(5)
            ->map(fn ($image): string => $image->store('guide/tour-gallery', 'public'))
            ->values()
            ->all();

        $verifiedIdPath = $request->file('verified_id_upload')?->store('guide/tour-verification', 'public');

        [$durationHours, $durationUnit] = $this->normalizeDuration((string) $validated['duration_label']);

        $activityTypes = collect($validated['activity_types'] ?? [])
            ->map(fn (mixed $value): string => trim((string) $value))
            ->filter(fn (string $value): bool => $value !== '')
            ->unique()
            ->values();

        $languagesSpoken = collect($validated['languages_spoken'] ?? [])
            ->map(fn (mixed $value): string => trim((string) $value))
            ->filter(fn (string $value): bool => $value !== '')
            ->unique()
            ->values();

        $availableDays = collect($validated['available_days'] ?? [])
            ->map(fn (mixed $value): string => trim((string) $value))
            ->filter(fn (string $value): bool => $value !== '')
            ->unique()
            ->values();

        $itinerarySegments = [
            'Pickup Point' => $validated['itinerary_pickup'] ?? null,
            'Stop 1' => $validated['itinerary_stop_1'] ?? null,
            'Stop 2' => $validated['itinerary_stop_2'] ?? null,
            'Lunch' => $validated['itinerary_lunch'] ?? null,
            'Return' => $validated['itinerary_return'] ?? null,
        ];

        $fullItinerary = collect($itinerarySegments)
            ->filter(fn (mixed $value): bool => is_string($value) && trim($value) !== '')
            ->map(fn (string $value, string $label): string => $label.': '.trim($value))
            ->implode(PHP_EOL);

        $inclusions = collect($validated['included'] ?? [])
            ->map(fn (mixed $value): string => trim((string) $value))
            ->filter(fn (string $value): bool => $value !== '')
            ->unique()
            ->values()
            ->all();

        $priceType = (string) $validated['price_type'];
        $basePrice = (float) $validated['base_price'];
        $availableOn = $this->nextAvailableDate($availableDays->all());
        $normalizedDifficulty = $this->normalizeDifficulty((string) ($validated['difficulty'] ?? 'Easy'));

        $metadata = [
            'guide_name' => trim((string) ($validated['guide_name'] ?? '')),
            'guide_bio' => trim((string) ($validated['guide_bio'] ?? '')),
            'difficulty_display' => trim((string) ($validated['difficulty'] ?? '')),
            'price_type' => $priceType,
            'weekend_rate' => isset($validated['weekend_rate']) ? (float) $validated['weekend_rate'] : null,
            'holiday_rate' => isset($validated['holiday_rate']) ? (float) $validated['holiday_rate'] : null,
            'languages_spoken' => $languagesSpoken->all(),
            'available_days' => $availableDays->all(),
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'instant_book' => $request->boolean('instant_book'),
            'requires_approval' => $request->boolean('requires_approval'),
            'max_slots' => isset($validated['max_slots']) ? (int) $validated['max_slots'] : null,
            'verified_id_upload_path' => $verifiedIdPath,
            'safety_certified' => $request->boolean('safety_certified'),
            'eco_friendly' => $request->boolean('eco_friendly'),
            'audience_badges' => array_values($validated['audience_badges'] ?? []),
        ];

        $description = trim((string) ($validated['overview_description'] ?? ''));
        if ($description === '') {
            $description = trim((string) $validated['short_description']);
        }

        $payload = $this->existingTourColumns([
            'guide_id' => $user->id,
            'created_by' => $user->id,
            'title' => $validated['title'],
            'name' => $validated['title'],
            'region' => $validated['region'],
            'city' => $validated['location'],
            'summary' => $validated['short_description'],
            'short_description' => $validated['short_description'],
            'description' => $description,
            'duration_label' => $validated['duration_label'],
            'duration_hours' => $durationHours,
            'duration_unit' => $durationUnit,
            'difficulty' => $normalizedDifficulty,
            'category' => $activityTypes->implode(', '),
            'activities' => $activityTypes->implode(', '),
            'max_guests' => (int) $validated['max_guests'],
            'min_guests' => (int) $validated['min_guests'],
            'max_people' => (int) ($validated['max_slots'] ?? $validated['max_guests']),
            'price_per_person' => $basePrice,
            'price' => $basePrice,
            'base_price' => $basePrice,
            'full_itinerary' => $fullItinerary,
            'inclusions' => $inclusions,
            'exclusions' => $validated['exclusions_text'] ?? null,
            'available_on' => $availableOn,
            'pricing_tiers' => $metadata,
            'blackout_dates' => [],
            'gallery_images' => $galleryImages,
            'image_path' => $coverImagePath,
            'featured_image' => $coverImagePath,
            'image_url' => $coverImagePath,
            'status' => 'active',
            'admin_approved' => true,
            'is_featured' => false,
        ]);

        Tour::query()->create($payload);

        return redirect()->route('dashboard.guide', ['tab' => 'listings'])
            ->with('status', 'Tour listing published successfully. It is now in My Listings.');
    }

    public function destroyListing(Request $request, Tour $tour): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user !== null, 401);
        abort_unless(in_array((string) $user->role, ['guide', 'tour_guide'], true), 403);

        $isOwner = ((int) $tour->guide_id === (int) $user->id)
            || ((int) $tour->created_by === (int) $user->id);

        abort_unless($isOwner, 403);

        $tour->delete();

        return back()->with('status', 'Tour listing deleted successfully.');
    }

    /**
     * @return Builder<Tour>
     */
    private function guideToursQuery(int $guideId): Builder
    {
        $hasGuideId = Schema::hasColumn('tours', 'guide_id');
        $hasCreatedBy = Schema::hasColumn('tours', 'created_by');

        return Tour::query()
            ->where(function ($query) use ($guideId, $hasGuideId, $hasCreatedBy): void {
                if ($hasGuideId && $hasCreatedBy) {
                    $query->where('guide_id', $guideId)
                        ->orWhere(function ($fallback) use ($guideId): void {
                            $fallback->whereNull('guide_id')->where('created_by', $guideId);
                        });

                    return;
                }

                if ($hasGuideId) {
                    $query->where('guide_id', $guideId);

                    return;
                }

                if ($hasCreatedBy) {
                    $query->where('created_by', $guideId);

                    return;
                }

                $query->whereRaw('1 = 0');
            });
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function existingTourColumns(array $payload): array
    {
        return collect($payload)
            ->filter(fn (mixed $value, string $column): bool => Schema::hasColumn('tours', $column))
            ->all();
    }

    /**
     * @return array{int, string}
     */
    private function normalizeDuration(string $duration): array
    {
        $hours = 1;
        if (preg_match('/\d+/', $duration, $matches) === 1) {
            $hours = max(1, (int) $matches[0]);
        }

        $unit = str_contains(strtolower($duration), 'day') ? 'days' : 'hours';

        return [$hours, $unit];
    }

    private function nextAvailableDate(array $availableDays): ?string
    {
        if ($availableDays === []) {
            return null;
        }

        $firstDay = trim((string) $availableDays[0]);
        if ($firstDay === '') {
            return null;
        }

        return Carbon::parse('next '.$firstDay)->toDateString();
    }

    private function normalizeDifficulty(string $difficulty): string
    {
        $normalized = strtolower(trim($difficulty));

        return match (true) {
            str_contains($normalized, 'challeng') => 'challenging',
            str_contains($normalized, 'moderate') => 'moderate',
            default => 'easy',
        };
    }
}
