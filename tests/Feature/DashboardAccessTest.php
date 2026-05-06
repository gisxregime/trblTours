<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

it('redirects each role from generic dashboard to their own dashboard', function (string $role, string $expectedRoute) {
    $user = User::factory()->create(['role' => $role]);

    actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route($expectedRoute, absolute: false));
})->with([
    ['tourist', 'dashboard.tourist'],
    ['guide', 'dashboard.guide.dashboard'],
    ['tour_guide', 'dashboard.guide.dashboard'],
    ['admin', 'dashboard.admin'],
]);

it('allows users to access their own role dashboard', function (string $role, string $routeName) {
    $user = User::factory()->create(['role' => $role]);

    actingAs($user)
        ->get(route($routeName))
        ->assertSuccessful();
})->with([
    ['tourist', 'dashboard.tourist'],
    ['guide', 'dashboard.guide.dashboard'],
    ['tour_guide', 'dashboard.guide.dashboard'],
    ['admin', 'dashboard.admin'],
]);

it('forbids access to dashboards from other roles', function () {
    $tourist = User::factory()->create(['role' => 'tourist']);

    actingAs($tourist)
        ->get(route('dashboard.admin'))
        ->assertForbidden();

    $guide = User::factory()->create(['role' => 'guide']);

    actingAs($guide)
        ->get(route('dashboard.tourist'))
        ->assertForbidden();

    $tourGuide = User::factory()->create(['role' => 'tour_guide']);

    actingAs($tourGuide)
        ->get(route('dashboard.tourist'))
        ->assertForbidden();
});
