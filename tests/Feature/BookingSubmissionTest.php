<?php

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
