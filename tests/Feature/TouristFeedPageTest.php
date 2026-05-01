<?php

use App\Livewire\ExploreToursFeed;
use App\Models\BookingRequest;
use App\Models\Tour;
use App\Models\User;

use function Pest\Laravel\actingAs;

it('shows tour listings and request posts on the tourist feed', function () {
    $tourist = User::factory()->create([
        'name' => 'Tourist User',
        'role' => 'tourist',
        'status' => 'active',
    ]);

    $guide = User::factory()->guide()->create([
        'name' => 'Verified Guide',
        'status' => 'active',
    ]);

    $tour = Tour::factory()->create([
        'guide_id' => $guide->id,
        'title' => 'Bohol Countryside Adventure',
        'region' => 'Central Visayas',
        'duration_label' => 'Full-day',
        'price_per_person' => 2500,
    ]);

    BookingRequest::query()->create([
        'tourist_id' => $tourist->id,
        'guide_id' => $guide->id,
        'tour_id' => $tour->id,
        'requested_date' => now()->addWeek()->toDateString(),
        'group_size' => 4,
        'total_price' => 9800,
        'special_requests' => 'Need child-friendly itinerary with flexible lunch stop.',
        'status' => 'pending',
    ]);

    actingAs($tourist)
        ->get(route('dashboard.tourist'))
        ->assertSuccessful()
        ->assertSee('Explore Tours')
        ->assertSee('My Posts')
        ->assertSee('Create Tour Request')
        ->assertSee('Create Your Request')
        ->assertSee('Enter request title')
        ->assertSee('href="'.url('/').'"', false)
        ->assertSee('Bohol Countryside Adventure')
        ->assertSeeLivewire(ExploreToursFeed::class);
});

it('shows filtering controls on the tourist feed', function () {
    $tourist = User::factory()->create([
        'role' => 'tourist',
        'status' => 'active',
    ]);

    $guide = User::factory()->guide()->create(['status' => 'active']);

    $tour = Tour::factory()->create([
        'guide_id' => $guide->id,
        'title' => 'Siargao Surf Escape',
        'region' => 'Caraga',
    ]);

    BookingRequest::query()->create([
        'tourist_id' => $tourist->id,
        'guide_id' => $guide->id,
        'tour_id' => $tour->id,
        'requested_date' => now()->addDays(10)->toDateString(),
        'group_size' => 3,
        'total_price' => 7600,
        'special_requests' => 'Sunrise activity preferred.',
        'status' => 'pending',
    ]);

    actingAs($tourist)
        ->get(route('dashboard.tourist'))
        ->assertSuccessful()
        ->assertSee('Location')
        ->assertSee('Sort By')
        ->assertSee('Apply')
        ->assertSee('Reset');
});
