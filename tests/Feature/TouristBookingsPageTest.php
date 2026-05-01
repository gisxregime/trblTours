<?php

use App\Models\Booking;
use App\Models\BookingRequest;
use App\Models\Tour;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('tourist dropdown bookings link points to my bookings page', function () {
    $tourist = User::factory()->create([
        'role' => 'tourist',
        'status' => 'active',
    ]);

    actingAs($tourist);

    $response = get(route('dashboard.tourist'));

    $response
        ->assertOk()
        ->assertSee(route('dashboard.my-bookings'), false);
});

test('tourist can view booked cards in my bookings page', function () {
    $tourist = User::factory()->create(['role' => 'tourist']);
    $guide = User::factory()->create(['role' => 'guide']);

    $tour = Tour::query()->create([
        'guide_id' => $guide->id,
        'title' => 'Palawan Reef Escape',
        'region' => 'Palawan',
        'summary' => str_repeat('Island hopping and snorkeling tour with local food stops. ', 3),
        'duration_label' => 'Full-day',
        'price_per_person' => 3200,
        'is_featured' => true,
        'available_on' => now()->addDays(5)->format('Y-m-d'),
    ]);

    BookingRequest::query()->create([
        'tourist_id' => $tourist->id,
        'guide_id' => $guide->id,
        'tour_id' => $tour->id,
        'requested_date' => now()->addDays(8)->format('Y-m-d'),
        'group_size' => 2,
        'total_price' => 6400,
        'status' => 'pending',
    ]);

    actingAs($tourist);

    $response = get(route('dashboard.my-bookings'));

    $response
        ->assertOk()
        ->assertSee('My Bookings')
        ->assertSee('Palawan Reef Escape')
        ->assertSee('Pending');
});

test('tourist can submit rating for completed booking', function () {
    $tourist = User::factory()->create(['role' => 'tourist']);
    $guide = User::factory()->create(['role' => 'guide']);

    $tour = Tour::query()->create([
        'guide_id' => $guide->id,
        'title' => 'El Nido Cove Adventure',
        'region' => 'Palawan',
        'summary' => str_repeat('Kayak and cliff lookout adventure with beach lunch. ', 3),
        'duration_label' => 'Full-day',
        'price_per_person' => 3500,
        'is_featured' => true,
        'available_on' => now()->addDays(4)->format('Y-m-d'),
    ]);

    $bookingRequest = BookingRequest::query()->create([
        'tourist_id' => $tourist->id,
        'guide_id' => $guide->id,
        'tour_id' => $tour->id,
        'requested_date' => now()->addDays(6)->format('Y-m-d'),
        'group_size' => 3,
        'total_price' => 10500,
        'status' => 'accepted',
    ]);

    $booking = Booking::query()->create([
        'booking_request_id' => $bookingRequest->id,
        'tourist_id' => $tourist->id,
        'guide_id' => $guide->id,
        'tour_id' => $tour->id,
        'booking_date' => now()->addDays(6)->format('Y-m-d'),
        'group_size' => 3,
        'total_amount' => 10500,
        'commission_amount' => 1050,
        'net_amount' => 9450,
        'status' => 'completed',
    ]);

    actingAs($tourist);

    $response = post(route('dashboard.my-bookings.rating', $booking), [
        'rating' => 5,
        'review' => 'Amazing experience with the guide.',
    ]);

    $response
        ->assertRedirect(route('dashboard.my-bookings'))
        ->assertSessionHas('status', 'Thanks for rating your completed booking.');

    assertDatabaseHas('tour_reviews', [
        'booking_id' => $booking->id,
        'tourist_id' => $tourist->id,
        'guide_id' => $guide->id,
        'tour_id' => $tour->id,
        'rating' => 5,
    ]);
});

test('tourist cannot rate an incomplete booking', function () {
    $tourist = User::factory()->create(['role' => 'tourist']);
    $guide = User::factory()->create(['role' => 'guide']);

    $tour = Tour::query()->create([
        'guide_id' => $guide->id,
        'title' => 'Coron Island Circuit',
        'region' => 'Palawan',
        'summary' => str_repeat('Island circuit with snorkeling and lagoon visit. ', 3),
        'duration_label' => 'Full-day',
        'price_per_person' => 2800,
        'is_featured' => true,
        'available_on' => now()->addDays(4)->format('Y-m-d'),
    ]);

    $bookingRequest = BookingRequest::query()->create([
        'tourist_id' => $tourist->id,
        'guide_id' => $guide->id,
        'tour_id' => $tour->id,
        'requested_date' => now()->addDays(6)->format('Y-m-d'),
        'group_size' => 2,
        'total_price' => 5600,
        'status' => 'accepted',
    ]);

    $booking = Booking::query()->create([
        'booking_request_id' => $bookingRequest->id,
        'tourist_id' => $tourist->id,
        'guide_id' => $guide->id,
        'tour_id' => $tour->id,
        'booking_date' => now()->addDays(6)->format('Y-m-d'),
        'group_size' => 2,
        'total_amount' => 5600,
        'commission_amount' => 560,
        'net_amount' => 5040,
        'status' => 'confirmed',
    ]);

    actingAs($tourist);

    $response = post(route('dashboard.my-bookings.rating', $booking), [
        'rating' => 4,
    ]);

    $response
        ->assertRedirect()
        ->assertSessionHasErrors('rating');
});

test('tourist cannot rate another tourists booking', function () {
    $owner = User::factory()->create(['role' => 'tourist']);
    $intruder = User::factory()->create(['role' => 'tourist']);
    $guide = User::factory()->create(['role' => 'guide']);

    $tour = Tour::query()->create([
        'guide_id' => $guide->id,
        'title' => 'Bohol River Discovery',
        'region' => 'Central Visayas',
        'summary' => str_repeat('River cruise and countryside heritage stops. ', 3),
        'duration_label' => 'Half-day',
        'price_per_person' => 1800,
        'is_featured' => true,
        'available_on' => now()->addDays(5)->format('Y-m-d'),
    ]);

    $bookingRequest = BookingRequest::query()->create([
        'tourist_id' => $owner->id,
        'guide_id' => $guide->id,
        'tour_id' => $tour->id,
        'requested_date' => now()->addDays(7)->format('Y-m-d'),
        'group_size' => 2,
        'total_price' => 3600,
        'status' => 'accepted',
    ]);

    $booking = Booking::query()->create([
        'booking_request_id' => $bookingRequest->id,
        'tourist_id' => $owner->id,
        'guide_id' => $guide->id,
        'tour_id' => $tour->id,
        'booking_date' => now()->addDays(7)->format('Y-m-d'),
        'group_size' => 2,
        'total_amount' => 3600,
        'commission_amount' => 360,
        'net_amount' => 3240,
        'status' => 'completed',
    ]);

    actingAs($intruder);

    $response = post(route('dashboard.my-bookings.rating', $booking), [
        'rating' => 5,
    ]);

    $response->assertForbidden();
});
