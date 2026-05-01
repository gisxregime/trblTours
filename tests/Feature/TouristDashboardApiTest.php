<?php

use App\Models\BookingRequest;
use App\Models\Tour;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\patchJson;

it('returns tourist dashboard API data collections', function () {
    $tourist = User::factory()->create([
        'role' => 'tourist',
        'email_verified_at' => now(),
    ]);

    $guide = User::factory()->create([
        'role' => 'tour_guide',
        'status' => 'active',
    ]);

    Tour::factory()->create([
        'guide_id' => $guide->id,
        'price_per_person' => 3500,
    ]);

    BookingRequest::factory()->create([
        'tourist_id' => $tourist->id,
        'guide_id' => $guide->id,
        'status' => 'pending',
    ]);

    actingAs($tourist);

    getJson('/api/tours')
        ->assertSuccessful()
        ->assertJsonStructure(['data']);

    getJson('/api/requests')
        ->assertSuccessful()
        ->assertJsonStructure(['data']);

    getJson('/api/my-posts')
        ->assertSuccessful()
        ->assertJsonStructure(['data']);
});

it('supports update and delete actions for tourist own request posts', function () {
    $tourist = User::factory()->create([
        'role' => 'tourist',
        'email_verified_at' => now(),
    ]);

    $guide = User::factory()->create([
        'role' => 'tour_guide',
        'status' => 'active',
    ]);

    $tour = Tour::factory()->create([
        'guide_id' => $guide->id,
    ]);

    $requestPost = BookingRequest::factory()->create([
        'tourist_id' => $tourist->id,
        'guide_id' => $guide->id,
        'tour_id' => $tour->id,
        'total_price' => 2200,
        'group_size' => 2,
        'special_requests' => 'Relaxed beach vibe',
        'status' => 'pending',
    ]);

    actingAs($tourist);

    patchJson("/api/requests/{$requestPost->id}", [
        'budget' => 5400,
        'vibe' => 'Adventure and food crawl',
        'traveler_count' => 4,
    ])
        ->assertSuccessful()
        ->assertJsonPath('data.budget', 5400);

    $requestPost->refresh();

    expect((float) $requestPost->total_price)->toBe(5400.0);
    expect((int) $requestPost->group_size)->toBe(4);

    deleteJson("/api/requests/{$requestPost->id}")
        ->assertSuccessful();

    expect(BookingRequest::query()->whereKey($requestPost->id)->exists())->toBeFalse();
});
