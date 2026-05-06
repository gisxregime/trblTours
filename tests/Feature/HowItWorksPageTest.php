<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

it('redirects guests away from how it works page', function () {
    $this->get(route('how-it-works'))
        ->assertRedirect(route('login'));
});

it('shows how it works page for tourist users', function () {
    $tourist = User::factory()->create([
        'role' => 'tourist',
    ]);

    actingAs($tourist)
        ->get(route('how-it-works'))
        ->assertSuccessful()
        ->assertSee('About & How Trbltours Works', false)
        ->assertSee('HOW IT WORKS - Your Journey, Simplified')
        ->assertSee('For Tourists')
        ->assertSee('For Guides');
});

it('shows how it works page for guide users', function () {
    $guide = User::factory()->create([
        'role' => 'guide',
    ]);

    actingAs($guide)
        ->get(route('how-it-works'))
        ->assertSuccessful()
        ->assertSee('Why TrblTours Exists')
        ->assertSee('WITHOUT TRBLTOURS')
        ->assertSee('OUR MISSION & VISION', false);
});
