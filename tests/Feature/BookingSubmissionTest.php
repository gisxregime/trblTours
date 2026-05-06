<?php

use App\Models\BookingRequest;
use App\Models\GuideAvailability;
use App\Models\Tour;
use App\Models\User;
use App\Notifications\TourActivityNotification;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;
use function Pest\Laravel\postJson;

test('tourist booking submission creates pending request and notifies guide', function () {
    Notification::fake();

    $tourist = User::factory()->create([
        'role' => 'tourist',
        'status' => 'active',
    ]);

    $guide = User::factory()->create([
        'role' => 'guide',
        'status' => 'active',
    ]);

    $tour = Tour::query()->create([
        'guide_id' => $guide->id,
        'title' => 'Island Sunrise Trail',
        'region' => 'Central Visayas',
        'summary' => str_repeat('Guided sunrise hike with coastal lookout stops. ', 3),
        'duration_label' => 'Half-day',
        'price_per_person' => 2500,
        'is_featured' => true,
        'available_on' => now()->addDays(5)->format('Y-m-d'),
    ]);

    actingAs($tourist);

    $response = post(route('bookings.store'), [
        'tour_id' => $tour->id,
        'booking_date' => now()->addDays(8)->format('Y-m-d'),
        'group_size' => 3,
        'special_requests' => 'Need child-friendly stops',
    ]);

    $response
        ->assertRedirect()
        ->assertSessionHas('status', 'Booking request sent to guide! You\'ll be notified soon.');

    assertDatabaseHas('booking_requests', [
        'tourist_id' => $tourist->id,
        'guide_id' => $guide->id,
        'tour_id' => $tour->id,
        'group_size' => 3,
        'status' => 'pending',
    ]);

    Notification::assertSentTo($guide, TourActivityNotification::class, function (TourActivityNotification $notification): bool {
        return $notification->title === 'New booking request'
            && $notification->type === 'booking'
            && $notification->actionUrl === route('dashboard.guide.requests');
    });

    Notification::assertSentTo($tourist, TourActivityNotification::class, function (TourActivityNotification $notification): bool {
        return $notification->title === 'Booking sent'
            && $notification->type === 'booking'
            && $notification->actionUrl === route('dashboard.my-bookings');
    });
});

test('tourist booking submission supports json response', function () {
    Notification::fake();

    $tourist = User::factory()->create(['role' => 'tourist']);
    $guide = User::factory()->create(['role' => 'guide']);

    $tour = Tour::query()->create([
        'guide_id' => $guide->id,
        'title' => 'Mangrove River Cruise',
        'region' => 'Palawan',
        'summary' => str_repeat('Calm river cruise with wildlife viewing and local snacks. ', 3),
        'duration_label' => 'Full-day',
        'price_per_person' => 3200,
        'is_featured' => true,
        'available_on' => now()->addDays(6)->format('Y-m-d'),
    ]);

    actingAs($tourist);

    $response = postJson(route('bookings.store'), [
        'tour_id' => $tour->id,
        'booking_date' => now()->addDays(10)->format('Y-m-d'),
        'group_size' => 2,
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Booking request sent to guide! You\'ll be notified soon.');
});

test('non tourist cannot submit booking request', function () {
    $guide = User::factory()->create([
        'role' => 'guide',
        'status' => 'active',
    ]);

    $tour = Tour::query()->create([
        'guide_id' => $guide->id,
        'title' => 'Coastal Heritage Walk',
        'region' => 'Ilocos Region',
        'summary' => str_repeat('Walk through local heritage streets with food tastings. ', 3),
        'duration_label' => 'Half-day',
        'price_per_person' => 1800,
        'is_featured' => true,
        'available_on' => now()->addDays(7)->format('Y-m-d'),
    ]);

    actingAs($guide);

    $response = post(route('bookings.store'), [
        'tour_id' => $tour->id,
        'booking_date' => now()->addDays(8)->format('Y-m-d'),
        'group_size' => 2,
    ]);

    $response->assertForbidden();
});

test('tourist cannot submit booking on unavailable date when guide availability is configured', function () {
    Notification::fake();

    $tourist = User::factory()->create(['role' => 'tourist']);
    $guide = User::factory()->create(['role' => 'guide']);

    $tour = Tour::query()->create([
        'guide_id' => $guide->id,
        'title' => 'Camiguin Volcano Loop',
        'region' => 'Northern Mindanao',
        'summary' => str_repeat('Scenic loop with spring stops and local food.', 3),
        'duration_label' => 'Full-day',
        'price_per_person' => 3100,
        'is_featured' => true,
        'available_on' => now()->addDays(5)->format('Y-m-d'),
    ]);

    GuideAvailability::query()->create([
        'guide_id' => $guide->id,
        'date' => now()->addDays(10)->format('Y-m-d'),
        'status' => 'available',
    ]);

    GuideAvailability::query()->create([
        'guide_id' => $guide->id,
        'date' => now()->addDays(11)->format('Y-m-d'),
        'status' => 'fully_booked',
    ]);

    actingAs($tourist);

    $response = post(route('bookings.store'), [
        'tour_id' => $tour->id,
        'booking_date' => now()->addDays(11)->format('Y-m-d'),
        'group_size' => 2,
    ]);

    $response
        ->assertRedirect()
        ->assertSessionHasErrors('booking_date');
});

test('tourist can submit booking on available date from guide availability', function () {
    Notification::fake();

    $tourist = User::factory()->create(['role' => 'tourist']);
    $guide = User::factory()->create(['role' => 'guide']);

    $tour = Tour::query()->create([
        'guide_id' => $guide->id,
        'title' => 'Romblon Island Hopper',
        'region' => 'Mimaropa',
        'summary' => str_repeat('Island hopping with snorkeling and beach breaks.', 3),
        'duration_label' => 'Full-day',
        'price_per_person' => 3300,
        'is_featured' => true,
        'available_on' => now()->addDays(5)->format('Y-m-d'),
    ]);

    $availableDate = now()->addDays(9)->format('Y-m-d');

    GuideAvailability::query()->create([
        'guide_id' => $guide->id,
        'date' => $availableDate,
        'status' => 'available',
        'special_price' => 3500,
    ]);

    actingAs($tourist);

    $response = post(route('bookings.store'), [
        'tour_id' => $tour->id,
        'booking_date' => $availableDate,
        'group_size' => 2,
    ]);

    $response
        ->assertRedirect()
        ->assertSessionHas('status', 'Booking request sent to guide! You\'ll be notified soon.');

    assertDatabaseHas('booking_requests', [
        'tourist_id' => $tourist->id,
        'guide_id' => $guide->id,
        'tour_id' => $tour->id,
        'total_price' => 7000,
        'status' => 'pending',
    ]);
});

test('tourist cannot book limited slot date when requested group exceeds remaining capacity', function () {
    Notification::fake();

    $tourist = User::factory()->create(['role' => 'tourist']);
    $existingTourist = User::factory()->create(['role' => 'tourist']);
    $guide = User::factory()->create(['role' => 'guide']);

    $tour = Tour::query()->create([
        'guide_id' => $guide->id,
        'title' => 'Apo Reef Day Run',
        'region' => 'Mimaropa',
        'summary' => str_repeat('Guided reef exploration with safety support.', 3),
        'duration_label' => 'Full-day',
        'price_per_person' => 2800,
        'is_featured' => true,
        'available_on' => now()->addDays(5)->format('Y-m-d'),
    ]);

    $limitedDate = now()->addDays(12)->format('Y-m-d');

    GuideAvailability::query()->create([
        'guide_id' => $guide->id,
        'date' => $limitedDate,
        'status' => 'limited_slots',
        'slots' => 4,
    ]);

    BookingRequest::query()->create([
        'tourist_id' => $existingTourist->id,
        'guide_id' => $guide->id,
        'tour_id' => $tour->id,
        'requested_date' => $limitedDate,
        'group_size' => 3,
        'total_price' => 8400,
        'status' => 'pending',
    ]);

    actingAs($tourist);

    $response = post(route('bookings.store'), [
        'tour_id' => $tour->id,
        'booking_date' => $limitedDate,
        'group_size' => 2,
    ]);

    $response
        ->assertRedirect()
        ->assertSessionHasErrors('booking_date');
});

test('tourist can book limited slot date when remaining capacity is enough', function () {
    Notification::fake();

    $tourist = User::factory()->create(['role' => 'tourist']);
    $existingTourist = User::factory()->create(['role' => 'tourist']);
    $guide = User::factory()->create(['role' => 'guide']);

    $tour = Tour::query()->create([
        'guide_id' => $guide->id,
        'title' => 'Siquijor Coastal Drive',
        'region' => 'Central Visayas',
        'summary' => str_repeat('Coastal route with heritage and beach stops.', 3),
        'duration_label' => 'Full-day',
        'price_per_person' => 2600,
        'is_featured' => true,
        'available_on' => now()->addDays(5)->format('Y-m-d'),
    ]);

    $limitedDate = now()->addDays(13)->format('Y-m-d');

    GuideAvailability::query()->create([
        'guide_id' => $guide->id,
        'date' => $limitedDate,
        'status' => 'limited_slots',
        'slots' => 6,
    ]);

    BookingRequest::query()->create([
        'tourist_id' => $existingTourist->id,
        'guide_id' => $guide->id,
        'tour_id' => $tour->id,
        'requested_date' => $limitedDate,
        'group_size' => 2,
        'total_price' => 5200,
        'status' => 'pending',
    ]);

    actingAs($tourist);

    $response = post(route('bookings.store'), [
        'tour_id' => $tour->id,
        'booking_date' => $limitedDate,
        'group_size' => 4,
    ]);

    $response
        ->assertRedirect()
        ->assertSessionHas('status', 'Booking request sent to guide! You\'ll be notified soon.');

    assertDatabaseHas('booking_requests', [
        'tourist_id' => $tourist->id,
        'guide_id' => $guide->id,
        'tour_id' => $tour->id,
        'group_size' => 4,
        'status' => 'pending',
    ]);
});
