<?php

use App\Models\TouristRequest;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

it('accepts modal request payload and stores a tourist request', function () {
    $tourist = User::factory()->create([
        'role' => 'tourist',
        'status' => 'active',
        'email_verified_at' => now(),
    ]);

    actingAs($tourist)
        ->postJson(route('requests.store'), [
            'title' => 'Weekend Cebu Food and Beach Escape',
            'location' => 'Region VII - Central Visayas - Cebu City',
            'preferred_date' => now()->addDays(5)->toDateString(),
            'passenger_count' => 3,
            'budget_min' => 5000,
            'budget_max' => 5000,
            'description' => 'We want a relaxed but local experience with food stops.',
            'duration' => '3 Days',
            'region' => 'Region VII - Central Visayas',
            'adults' => 2,
            'children' => 1,
            'interests' => 'Food, Beach',
        ])
        ->assertSuccessful()
        ->assertJson([
            'success' => true,
        ]);

    expect(TouristRequest::query()->count())->toBe(1);

    assertDatabaseHas('tourist_requests', [
        'title' => 'Weekend Cebu Food and Beach Escape',
        'location' => 'Region VII - Central Visayas - Cebu City',
        'duration' => '3 Days',
        'region' => 'Region VII - Central Visayas',
        'adults' => 2,
        'children' => 1,
        'interests' => 'Food, Beach',
        'passenger_count' => 3,
    ]);
});
