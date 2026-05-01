<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('settings page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/settings');

    $response
        ->assertOk()
        ->assertSee('Account Settings')
        ->assertSee('Update Email')
        ->assertSee('Change Password');
});

test('settings update allows unchanged email for current user', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/settings', [
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/settings');

    expect($user->refresh()->email)->toBe($user->email);
});

test('settings update rejects duplicate email from another user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/settings')
        ->patch('/settings', [
            'email' => $otherUser->email,
        ]);

    $response
        ->assertSessionHasErrors('email')
        ->assertRedirect('/settings');
});

test('settings update changes password when current password is valid', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    $response = $this
        ->actingAs($user)
        ->patch('/settings', [
            'email' => $user->email,
            'current_password' => 'password',
            'new_password' => 'SecurePass123',
            'new_password_confirmation' => 'SecurePass123',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/settings');

    expect(Hash::check('SecurePass123', $user->refresh()->password))->toBeTrue();
});

test('settings update fails password change when current password is incorrect', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    $response = $this
        ->actingAs($user)
        ->from('/settings')
        ->patch('/settings', [
            'email' => $user->email,
            'current_password' => 'invalid-password',
            'new_password' => 'SecurePass123',
            'new_password_confirmation' => 'SecurePass123',
        ]);

    $response
        ->assertSessionHasErrors('current_password')
        ->assertRedirect('/settings');
});
