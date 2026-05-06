<?php

use App\Livewire\Guide\GuideDashboard;
use App\Models\BookingRequest;
use App\Models\Tour;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('shows the dedicated guide dashboard page from dashboard route', function () {
    $guide = User::factory()->create([
        'role' => 'guide',
        'full_name' => 'Maya Delos Santos',
    ]);

    actingAs($guide)
        ->get(route('dashboard.guide.dashboard'))
        ->assertSuccessful()
        ->assertSee('Guide Control Center')
        ->assertSee('Total Earnings')
        ->assertSee('Average Ratings')
        ->assertSee('Total Feedbacks')
        ->assertSee('Total Bookings')
        ->assertSee('Pending Bookings')
        ->assertSee('On Going Tours')
        ->assertSee('Completed Tours')
        ->assertSee('Declined Bookings')
        ->assertSee('History Bookings');
});

it('allows guide to accept booking request', function () {
    $guide = User::factory()->create([
        'role' => 'guide',
        'full_name' => 'Alfred Reyes',
    ]);

    $tourist = User::factory()->create([
        'role' => 'tourist',
        'full_name' => 'Lia Ramos',
    ]);

    $tour = Tour::query()->create([
        'guide_id' => $guide->id,
        'title' => 'Cultural Food and Heritage Walk',
        'region' => 'Metro Manila',
        'summary' => str_repeat('A relaxed city walk with food tastings and local stories. ', 3),
        'duration_label' => 'Half-day',
        'price_per_person' => 1800,
        'is_featured' => true,
        'available_on' => now()->addDays(5)->format('Y-m-d'),
    ]);

    $request = BookingRequest::query()->create([
        'tourist_id' => $tourist->id,
        'guide_id' => $guide->id,
        'tour_id' => $tour->id,
        'requested_date' => now()->addDays(7)->format('Y-m-d'),
        'group_size' => 3,
        'total_price' => 5400,
        'status' => 'pending',
    ]);

    actingAs($guide);

    Livewire::test(GuideDashboard::class)
        ->call('acceptPending', $request->id)
        ->assertSet('pendingActions.'.$request->id, 'accepted')
        ->assertSet('actionMessage', '✓ Accepted Lia Ramos — message sent to tourist')
        ->assertHasNoErrors();

    expect($request->fresh()?->status)->toBe('pending');
});

it('allows guide to decline booking request and save decline reason', function () {
    $guide = User::factory()->create([
        'role' => 'tour_guide',
        'full_name' => 'Nico Villanueva',
    ]);

    $tourist = User::factory()->create([
        'role' => 'tourist',
        'full_name' => 'Celine Cruz',
    ]);

    $tour = Tour::query()->create([
        'guide_id' => $guide->id,
        'title' => 'Lake Sunrise Photo Tour',
        'region' => 'Laguna',
        'summary' => str_repeat('Catch sunrise viewpoints with flexible pacing and stops. ', 3),
        'duration_label' => 'Full-day',
        'price_per_person' => 2200,
        'is_featured' => true,
        'available_on' => now()->addDays(9)->format('Y-m-d'),
    ]);

    $request = BookingRequest::query()->create([
        'tourist_id' => $tourist->id,
        'guide_id' => $guide->id,
        'tour_id' => $tour->id,
        'requested_date' => now()->addDays(10)->format('Y-m-d'),
        'group_size' => 2,
        'total_price' => 4400,
        'status' => 'pending',
    ]);

    actingAs($guide);

    Livewire::test(GuideDashboard::class)
        ->call('declinePending', $request->id)
        ->assertSet('pendingActions.'.$request->id, 'declined')
        ->assertSet('actionMessage', '✗ Declined Celine Cruz — message sent to tourist')
        ->assertHasNoErrors();

    $request->refresh();

    expect($request->status)->toBe('pending')
        ->and($request->decline_reason)->toBeNull();
});
