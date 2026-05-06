<?php

use App\Livewire\Guide\GuideBookingRequests;
use App\Models\Booking;
use App\Models\BookingRequest;
use App\Models\Tour;
use App\Models\TourReview;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('shows the guide booking requests page', function () {
    $guide = User::factory()->create([
        'role' => 'guide',
        'full_name' => 'Maria Santos',
    ]);

    actingAs($guide)
        ->get(route('dashboard.guide.requests'))
        ->assertSuccessful()
        ->assertSee('Booking Requests')
        ->assertSee('Back to Dashboard');
});

it('allows guide to accept a pending booking request', function () {
    $guide = User::factory()->create([
        'role' => 'guide',
        'full_name' => 'Luis Rivera',
    ]);

    $tourist = User::factory()->create();

    $tour = Tour::query()->create([
        'guide_id' => $guide->id,
        'title' => 'Sunset Harbor Walk',
        'region' => 'Central Visayas',
        'summary' => str_repeat('Scenic boardwalk and market stop with local stories. ', 3),
        'duration_label' => 'Half-day',
        'price_per_person' => 1800,
        'is_featured' => true,
        'available_on' => now()->addDays(5)->format('Y-m-d'),
    ]);

    $request = BookingRequest::query()->create([
        'tourist_id' => $tourist->id,
        'guide_id' => $guide->id,
        'tour_id' => $tour->id,
        'requested_date' => now()->addWeek()->format('Y-m-d'),
        'group_size' => 4,
        'total_price' => 7200,
        'status' => 'pending',
    ]);

    actingAs($guide);

    Livewire::test(GuideBookingRequests::class)
        ->call('acceptRequest', $request->id)
        ->assertHasNoErrors();

    expect($request->fresh()?->status)->toBe('accepted');
});

it('allows guide to decline a pending booking request with reason', function () {
    $guide = User::factory()->create([
        'role' => 'tour_guide',
        'full_name' => 'Aira Cruz',
    ]);

    $tourist = User::factory()->create();

    $tour = Tour::query()->create([
        'guide_id' => $guide->id,
        'title' => 'Lagoon Paddle Tour',
        'region' => 'Palawan',
        'summary' => str_repeat('Paddle through mangrove channels with wildlife spotting. ', 3),
        'duration_label' => 'Full-day',
        'price_per_person' => 2400,
        'is_featured' => true,
        'available_on' => now()->addDays(8)->format('Y-m-d'),
    ]);

    $request = BookingRequest::query()->create([
        'tourist_id' => $tourist->id,
        'guide_id' => $guide->id,
        'tour_id' => $tour->id,
        'requested_date' => now()->addDays(10)->format('Y-m-d'),
        'group_size' => 2,
        'total_price' => 4800,
        'status' => 'pending',
    ]);

    actingAs($guide);

    Livewire::test(GuideBookingRequests::class)
        ->set('declineReasons.'.$request->id, 'Unavailable due to existing booking')
        ->call('declineRequest', $request->id)
        ->assertHasNoErrors();

    $request->refresh();

    expect($request->status)->toBe('declined')
        ->and($request->decline_reason)->toBe('Unavailable due to existing booking');
});

it('shows latest tourist reviews in guide booking requests panel', function () {
    $guide = User::factory()->create([
        'role' => 'guide',
        'full_name' => 'Marco Del Rosario',
    ]);

    $tourist = User::factory()->create([
        'role' => 'tourist',
        'full_name' => 'Clara Lim',
    ]);

    $tour = Tour::query()->create([
        'guide_id' => $guide->id,
        'title' => 'Iloilo Heritage Flavor Tour',
        'region' => 'Western Visayas',
        'summary' => str_repeat('Local food crawl with museum and river walk.', 3),
        'duration_label' => 'Half-day',
        'price_per_person' => 2100,
        'is_featured' => true,
        'available_on' => now()->addDays(5)->format('Y-m-d'),
    ]);

    $bookingRequest = BookingRequest::query()->create([
        'tourist_id' => $tourist->id,
        'guide_id' => $guide->id,
        'tour_id' => $tour->id,
        'requested_date' => now()->subDays(2)->format('Y-m-d'),
        'group_size' => 2,
        'total_price' => 4200,
        'status' => 'accepted',
    ]);

    $booking = Booking::query()->create([
        'booking_request_id' => $bookingRequest->id,
        'tourist_id' => $tourist->id,
        'guide_id' => $guide->id,
        'tour_id' => $tour->id,
        'booking_date' => now()->subDays(2)->format('Y-m-d'),
        'group_size' => 2,
        'total_amount' => 4200,
        'commission_amount' => 420,
        'net_amount' => 3780,
        'status' => 'completed',
    ]);

    TourReview::query()->create([
        'booking_id' => $booking->id,
        'tourist_id' => $tourist->id,
        'guide_id' => $guide->id,
        'tour_id' => $tour->id,
        'rating' => 5,
        'review' => 'Fantastic trip and super friendly guiding.',
        'is_featured' => false,
    ]);

    actingAs($guide);

    Livewire::test(GuideBookingRequests::class)
        ->assertSee('Latest Tourist Reviews')
        ->assertSee('Fantastic trip and super friendly guiding.');
});
