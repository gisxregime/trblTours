<?php

use App\Models\Tour;
use App\Models\User;

use function Pest\Laravel\get;

it('shows featured tours from the database', function () {
    $guide = User::factory()->guide()->create([
        'name' => 'Mara Santos',
        'region' => 'National Capital Region',
        'specialty' => 'Heritage walks',
        'bio' => 'City stories and food stops.',
    ]);

    Tour::factory()->create([
        'guide_id' => $guide->id,
        'title' => 'Intramuros Heritage Walk',
        'region' => 'National Capital Region',
        'summary' => 'A culture-rich walking route through old Manila landmarks.',
        'is_featured' => true,
    ]);

    get('/')
        ->assertSuccessful()
        ->assertSee('Intramuros Heritage Walk')
        ->assertSee('Mara Santos')
        ->assertSee('National Capital Region')
        ->assertSee('A culture-rich walking route through old Manila landmarks.');
});

it('filters featured tours by region', function () {
    $ncrGuide = User::factory()->guide()->create([
        'name' => 'Mara Santos',
        'region' => 'National Capital Region',
        'specialty' => 'Heritage walks',
    ]);

    $visayasGuide = User::factory()->guide()->create([
        'name' => 'Jasper Dela Cruz',
        'region' => 'Central Visayas',
        'specialty' => 'Island hopping',
    ]);

    Tour::factory()->create([
        'guide_id' => $ncrGuide->id,
        'title' => 'Old Manila Food Trail',
        'region' => 'National Capital Region',
        'is_featured' => true,
    ]);

    Tour::factory()->create([
        'guide_id' => $visayasGuide->id,
        'title' => 'Cebu Island Hopping',
        'region' => 'Central Visayas',
        'is_featured' => true,
    ]);

    get('/?region=Central%20Visayas')
        ->assertSuccessful()
        ->assertSee('Cebu Island Hopping')
        ->assertDontSee('Old Manila Food Trail');
});
