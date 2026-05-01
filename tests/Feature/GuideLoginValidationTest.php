<?php

use App\Models\User;

use function Pest\Laravel\assertAuthenticatedAs;
use function Pest\Laravel\post;

it('shows a sign up prompt when no guide account exists for the email', function () {
    post(route('guide.login.store'), [
        'email' => 'missing-guide@example.com',
        'password' => 'password',
    ])
        ->assertSessionHasErrors([
            'email' => 'No account found. Please sign up as a Tour Guide.',
        ])
        ->assertRedirect();
});

it('shows a tourist-specific error when a tourist uses the guide login', function () {
    User::factory()->create([
        'email' => 'tourist@example.com',
        'role' => 'tourist',
        'password' => 'password',
    ]);

    post(route('guide.login.store'), [
        'email' => 'tourist@example.com',
        'password' => 'password',
    ])
        ->assertSessionHasErrors([
            'email' => 'This email is registered as a Tourist. Please log in as a Tourist or apply to be a Guide.',
        ])
        ->assertRedirect();
});

it('shows a guide-specific error when a guide tries to log in as tourist', function () {
    User::factory()->guide()->create([
        'email' => 'guide@example.com',
        'password' => 'password',
    ]);

    post(route('login'), [
        'email' => 'guide@example.com',
        'password' => 'password',
        'role' => 'tourist',
    ])
        ->assertSessionHasErrors([
            'email' => 'This email is registered as a Tour Guide. Please log in as a Tour Guide.',
        ])
        ->assertRedirect();
});

it('shows a clear invalid credentials error for wrong password or email combination', function () {
    User::factory()->guide()->create([
        'email' => 'guide-password@example.com',
        'password' => 'password',
    ]);

    post(route('guide.login.store'), [
        'email' => 'guide-password@example.com',
        'password' => 'wrong-password',
    ])
        ->assertSessionHasErrors([
            'email' => 'Incorrect email or password. Please check your details and try again.',
        ])
        ->assertRedirect();
});

it('logs in the guide account when both tourist and guide share the same email', function () {
    User::factory()->create([
        'email' => 'dual-role@example.com',
        'role' => 'tourist',
        'password' => 'password',
    ]);

    $guideUser = User::factory()->create([
        'email' => 'dual-role@example.com',
        'role' => 'tour_guide',
        'password' => 'password',
    ]);

    post(route('guide.login.store'), [
        'email' => 'dual-role@example.com',
        'password' => 'password',
    ])->assertRedirect();

    assertAuthenticatedAs($guideUser);
});
