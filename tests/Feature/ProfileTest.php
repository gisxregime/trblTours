<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response
        ->assertOk()
        ->assertSee('Profile Details')
        ->assertSee('Full Name')
        ->assertSee('Bio / Short Description')
        ->assertSee('Location')
        ->assertSee('Edit');
});

test('profile information can be updated', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $originalEmail = $user->email;
    $originalEmailVerifiedAt = $user->email_verified_at;
    $tinyPng = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO8W6xsAAAAASUVORK5CYII=');
    $profilePhoto = UploadedFile::fake()->createWithContent('profile.png', $tinyPng);
    $coverPhoto = UploadedFile::fake()->createWithContent('cover.png', $tinyPng);

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'bio' => 'Adventure traveler who loves cultural immersion.',
            'region' => 'Baguio City',
            'profile_photo' => $profilePhoto,
            'cover_photo' => $coverPhoto,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('Test User', $user->name);
    $this->assertSame('Test User', $user->full_name);
    $this->assertSame($originalEmail, $user->email);
    $this->assertSame('Adventure traveler who loves cultural immersion.', $user->bio);
    $this->assertSame('Baguio City', $user->region);
    $this->assertNotNull($user->profile_photo_path);
    $this->assertNotNull($user->cover_photo_path);
    $this->assertSame($originalEmailVerifiedAt?->toDateTimeString(), $user->email_verified_at?->toDateTimeString());

    Storage::disk('public')->assertExists($user->profile_photo_path);
    Storage::disk('public')->assertExists($user->cover_photo_path);
});

test('profile information can be updated via json request', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $originalEmail = $user->email;
    $tinyPng = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO8W6xsAAAAASUVORK5CYII=');
    $profilePhoto = UploadedFile::fake()->createWithContent('profile.png', $tinyPng);
    $coverPhoto = UploadedFile::fake()->createWithContent('cover.png', $tinyPng);

    $response = $this
        ->actingAs($user)
        ->withHeaders(['Accept' => 'application/json'])
        ->patch('/profile', [
            'full_name' => 'Json User',
            'email' => 'json@example.com',
            'bio' => 'JSON profile save flow',
            'region' => 'Cebu',
            'profile_photo' => $profilePhoto,
            'cover_photo' => $coverPhoto,
        ]);

    $response
        ->assertOk()
        ->assertJsonPath('status', 'profile-updated')
        ->assertJsonPath('data.display_name', 'Json User')
        ->assertJsonPath('data.bio', 'JSON profile save flow')
        ->assertJsonPath('data.region', 'Cebu');

    $user->refresh();

    expect($user->name)->toBe('Json User')
        ->and($user->full_name)->toBe('Json User')
        ->and($user->email)->toBe($originalEmail)
        ->and($user->profile_photo_path)->not->toBeNull()
        ->and($user->cover_photo_path)->not->toBeNull();

    Storage::disk('public')->assertExists($user->profile_photo_path);
    Storage::disk('public')->assertExists($user->cover_photo_path);
});

test('json profile update returns validation errors for invalid files', function () {
    $user = User::factory()->create();
    $invalidFile = UploadedFile::fake()->create('not-an-image.txt', 5, 'text/plain');

    $response = $this
        ->actingAs($user)
        ->withHeaders(['Accept' => 'application/json'])
        ->patch('/profile', [
            'full_name' => 'Validation User',
            'email' => $user->email,
            'profile_photo' => $invalidFile,
        ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['profile_photo']);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($user->fresh());
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/profile');

    $this->assertNotNull($user->fresh());
});
