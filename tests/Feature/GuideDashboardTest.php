<?php

use App\Models\Comment;
use App\Models\Tour;
use App\Models\TouristRequest;
use App\Models\User;
use App\Notifications\TourActivityNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;

it('shows request posts and listing controls on guide dashboard', function () {
    $guide = User::factory()->create([
        'role' => 'guide',
        'full_name' => 'Maria Santos',
    ]);

    $tourist = User::factory()->create([
        'role' => 'tourist',
        'full_name' => 'Lia Ramos',
    ]);

    TouristRequest::query()->create([
        'tourist_id' => $tourist->id,
        'title' => 'Island Hopping in Samal',
        'location' => 'Samal Island',
        'region' => 'Davao Region',
        'duration' => '1 Day',
        'preferred_date' => now()->addDays(14)->toDateString(),
        'passenger_count' => 2,
        'adults' => 2,
        'children' => 0,
        'budget_min' => 5000,
        'budget_max' => 5000,
        'interests' => 'Beach, Island Hopping, Food',
        'description' => 'Looking for a smooth island hopping itinerary with snorkeling and lunch options.',
        'status' => 'open',
    ]);

    Tour::query()->create(
        collect([
            'guide_id' => $guide->id,
            'created_by' => $guide->id,
            'title' => 'Coastal Escape Tour',
            'region' => 'Davao Region',
            'summary' => str_repeat('Premium coastal package. ', 4),
            'duration_label' => '1 Day',
            'price_per_person' => 3500,
            'status' => 'active',
        ])
            ->filter(fn (mixed $value, string $column): bool => Schema::hasColumn('tours', $column))
            ->all()
    );

    actingAs($guide)
        ->get(route('dashboard.guide'))
        ->assertSuccessful()
        ->assertSee('Request Posts')
        ->assertSee('My Listings')
        ->assertSee('Island Hopping in Samal')
        ->assertSee('Coastal Escape Tour')
        ->assertSee('Create Tour Listing');
});

it('allows a guide to comment on a tourist request and notifies the tourist', function () {
    Notification::fake();

    $guide = User::factory()->create([
        'role' => 'guide',
        'full_name' => 'Luis Rivera',
    ]);

    $tourist = User::factory()->create([
        'role' => 'tourist',
        'full_name' => 'Ana Cruz',
    ]);

    $touristRequest = TouristRequest::query()->create([
        'tourist_id' => $tourist->id,
        'title' => 'Mountain View Weekend',
        'location' => 'Bukidnon',
        'preferred_date' => now()->addDays(10)->toDateString(),
        'passenger_count' => 3,
        'budget_min' => 8000,
        'budget_max' => 12000,
        'description' => 'Looking for a comfortable mountain itinerary with food stops.',
        'status' => 'open',
    ]);

    actingAs($guide)
        ->post(route('dashboard.guide.request-posts.comments.store', $touristRequest), [
            'body' => 'I can offer a 2-day package with meals and transport included.',
            'offer_price' => 9500,
        ])
        ->assertRedirect();

    $comment = Comment::query()->where('tourist_request_id', $touristRequest->id)->first();

    expect($comment)->not->toBeNull()
        ->and($comment?->user_id)->toBe($guide->id)
        ->and((float) ($comment?->offer_price ?? 0))->toBe(9500.0);

    expect($touristRequest->fresh()?->status)->toBe('negotiating');

    Notification::assertSentTo(
        $tourist,
        TourActivityNotification::class,
        fn (TourActivityNotification $notification): bool => $notification->title === 'New guide response'
            && $notification->actionUrl === route('dashboard.my-posts')
    );
});

it('allows tour_guide role to access the guide dashboard', function () {
    $guide = User::factory()->create([
        'role' => 'tour_guide',
        'full_name' => 'Aira Santos',
    ]);

    actingAs($guide)
        ->get(route('dashboard.guide'))
        ->assertSuccessful()
        ->assertSee('Request Posts')
        ->assertSee('My Listings');
});

it('shows only open and negotiating request posts in the guide request feed', function () {
    $guide = User::factory()->create([
        'role' => 'guide',
        'full_name' => 'Miko Lanting',
    ]);

    $tourist = User::factory()->create([
        'role' => 'tourist',
        'full_name' => 'Aly Mercado',
    ]);

    TouristRequest::query()->create([
        'tourist_id' => $tourist->id,
        'title' => 'Open Request Card',
        'location' => 'Samal',
        'preferred_date' => now()->addDays(5)->toDateString(),
        'passenger_count' => 2,
        'budget_min' => 4000,
        'budget_max' => 6000,
        'description' => 'Open request description',
        'status' => 'open',
    ]);

    TouristRequest::query()->create([
        'tourist_id' => $tourist->id,
        'title' => 'Negotiating Request Card',
        'location' => 'Siargao',
        'preferred_date' => now()->addDays(6)->toDateString(),
        'passenger_count' => 3,
        'budget_min' => 7000,
        'budget_max' => 9000,
        'description' => 'Negotiating request description',
        'status' => 'negotiating',
    ]);

    TouristRequest::query()->create([
        'tourist_id' => $tourist->id,
        'title' => 'Completed Request Card',
        'location' => 'Bohol',
        'preferred_date' => now()->addDays(7)->toDateString(),
        'passenger_count' => 2,
        'budget_min' => 5000,
        'budget_max' => 6500,
        'description' => 'Completed request description',
        'status' => 'completed',
    ]);

    TouristRequest::query()->create([
        'tourist_id' => $tourist->id,
        'title' => 'Cancelled Request Card',
        'location' => 'Palawan',
        'preferred_date' => now()->addDays(8)->toDateString(),
        'passenger_count' => 2,
        'budget_min' => 6000,
        'budget_max' => 8000,
        'description' => 'Cancelled request description',
        'status' => 'cancelled',
    ]);

    TouristRequest::query()->create([
        'tourist_id' => $tourist->id,
        'title' => 'Inactive Request Card',
        'location' => 'Cebu',
        'preferred_date' => now()->addDays(9)->toDateString(),
        'passenger_count' => 2,
        'budget_min' => 6000,
        'budget_max' => 9000,
        'description' => 'Inactive request description',
        'status' => 'inactive',
    ]);

    actingAs($guide)
        ->get(route('dashboard.guide'))
        ->assertSuccessful()
        ->assertSee('Open Request Card')
        ->assertSee('Negotiating Request Card')
        ->assertDontSee('Completed Request Card')
        ->assertDontSee('Cancelled Request Card')
        ->assertDontSee('Inactive Request Card');
});

it('requires cover image and at least three gallery photos when publishing listing', function () {
    Storage::fake('public');

    $guide = User::factory()->create([
        'role' => 'guide',
    ]);

    actingAs($guide)
        ->post(route('dashboard.guide.listings.store'), [
            'title' => 'Test Listing Validation',
            'region' => 'Region IV-B - Mimaropa',
            'location' => 'El Nido, Palawan',
            'short_description' => 'A short listing summary for validation.',
            'duration_label' => 'Full-day',
            'min_guests' => 1,
            'max_guests' => 6,
            'base_price' => 3900,
            'price_type' => 'per_person',
            'gallery_images' => [
                UploadedFile::fake()->create('gallery-1.jpg', 120, 'image/jpeg'),
                UploadedFile::fake()->create('gallery-2.jpg', 120, 'image/jpeg'),
            ],
        ])
        ->assertSessionHasErrors([
            'cover_image',
            'gallery_images',
        ]);
});

it('publishes listing builder fields to the tour and keeps it out of featured tours', function () {
    Storage::fake('public');

    $guide = User::factory()->create([
        'role' => 'guide',
        'full_name' => 'Guide Publisher',
    ]);

    $response = actingAs($guide)
        ->post(route('dashboard.guide.listings.store'), [
            'title' => 'El Nido Lagoon Explorer',
            'region' => 'Region IV-B - Mimaropa',
            'location' => 'El Nido, Palawan',
            'short_description' => 'Boat-based island hopping through limestone lagoons, hidden coves, and swim stops.',
            'cover_image' => UploadedFile::fake()->create('cover.jpg', 140, 'image/jpeg'),
            'gallery_images' => [
                UploadedFile::fake()->create('gallery-1.jpg', 120, 'image/jpeg'),
                UploadedFile::fake()->create('gallery-2.jpg', 120, 'image/jpeg'),
                UploadedFile::fake()->create('gallery-3.jpg', 120, 'image/jpeg'),
            ],
            'guide_name' => 'Guide Publisher',
            'guide_bio' => 'Certified Local Guide',
            'duration_label' => 'Full-day',
            'activity_types' => ['Boat', 'Island Hopping'],
            'difficulty' => 'Flexible',
            'min_guests' => 2,
            'max_guests' => 10,
            'languages_spoken' => ['English', 'Filipino'],
            'base_price' => 3800,
            'price_type' => 'per_person',
            'overview_description' => 'Full-day guided island hopping with hidden lagoons and flexible swim stops.',
            'itinerary_pickup' => 'El Nido Port',
            'itinerary_stop_1' => 'Big Lagoon',
            'itinerary_stop_2' => 'Secret Beach',
            'itinerary_lunch' => 'Floating lunch stop',
            'itinerary_return' => 'El Nido Port Return',
            'included' => ['Guide Fee', 'Boat Transfer', 'Water'],
            'exclusions_text' => 'Personal expenses and optional activities.',
            'start_time' => '08:00',
            'end_time' => '17:00',
            'instant_book' => '1',
            'requires_approval' => '1',
            'max_slots' => 14,
            'safety_certified' => '1',
            'eco_friendly' => '1',
            'audience_badges' => ['families', 'couples'],
        ]);

    $response
        ->assertRedirect(route('dashboard.guide', ['tab' => 'listings']))
        ->assertSessionHas('status');

    $tour = Tour::query()->latest('id')->first();

    expect($tour)->not->toBeNull();
    expect($tour?->title)->toBe('El Nido Lagoon Explorer');
    expect((string) $tour?->summary)->toContain('Boat-based island hopping');
    expect((bool) $tour?->is_featured)->toBeFalse();

    if (Schema::hasColumn('tours', 'gallery_images')) {
        expect(is_array($tour?->gallery_images))->toBeTrue();
        expect(count((array) $tour?->gallery_images))->toBeGreaterThanOrEqual(3);
    }

    if (Schema::hasColumn('tours', 'full_itinerary')) {
        expect((string) $tour?->full_itinerary)->toContain('Pickup Point: El Nido Port');
        expect((string) $tour?->full_itinerary)->toContain('Stop 1: Big Lagoon');
    }

    if (Schema::hasColumn('tours', 'available_on')) {
        expect($tour?->available_on)->toBeNull();
    }
});
