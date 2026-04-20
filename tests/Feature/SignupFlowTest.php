<?php

use App\Services\GmailMailService;
use Illuminate\Support\Facades\DB;

use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

beforeEach(function (): void {
    $otpMailer = Mockery::mock(GmailMailService::class);
    $otpMailer->shouldReceive('sendOTP')->andReturn(true);

    app()->instance(GmailMailService::class, $otpMailer);
});

it('renders the signup email step', function () {
    get(route('signup.start'))
        ->assertSuccessful()
        ->assertSee('Step 1 of 4: Enter your email.');
});

it('sends a signup otp and stores a draft', function () {
    $response = post(route('signup.email.store'), [
        'email' => 'new-guide@example.com',
    ]);

    $draft = DB::table('signup_drafts')->where('email', 'new-guide@example.com')->first();

    expect($draft)->not->toBeNull();
    expect($draft->otp_code)->not->toBeNull();
    expect($draft->otp_expires_at)->not->toBeNull();

    $response->assertRedirect(route('signup.otp', ['token' => $draft->token], absolute: false));
});

it('blocks role selection until the email is verified', function () {
    DB::table('signup_drafts')->insert([
        'token' => 'draft-token-1',
        'email' => 'pending@example.com',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    get(route('signup.role', ['token' => 'draft-token-1']))
        ->assertRedirect(route('signup.start', absolute: false));
});

it('verifies otp and advances to role selection', function () {
    DB::table('signup_drafts')->insert([
        'token' => 'draft-token-2',
        'email' => 'verified@example.com',
        'otp_code' => '123456',
        'otp_expires_at' => now()->addMinutes(10),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    post(route('signup.otp.store', ['token' => 'draft-token-2']), [
        'otp_code' => '123456',
    ])
        ->assertRedirect(route('signup.role', ['token' => 'draft-token-2'], absolute: false));

    expect(DB::table('signup_drafts')->where('token', 'draft-token-2')->value('email_verified_at'))->not->toBeNull();
});

it('renders the role and details steps for a verified draft', function () {
    DB::table('signup_drafts')->insert([
        'token' => 'draft-token-4',
        'email' => 'role-step@example.com',
        'email_verified_at' => now(),
        'role' => 'tourist',
        'role_selected_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    get(route('signup.role', ['token' => 'draft-token-4']))
        ->assertSuccessful()
        ->assertSee('Step 3 of 4: Choose your role.');

    get(route('signup.details', ['token' => 'draft-token-4']))
        ->assertSuccessful()
        ->assertSee('Step 4 of 4: Complete your profile details.');
});

it('creates a tourist account after completing the final step', function () {
    DB::table('signup_drafts')->insert([
        'token' => 'draft-token-3',
        'email' => 'tourist@example.com',
        'email_verified_at' => now(),
        'role' => 'tourist',
        'role_selected_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    post(route('signup.details.store', ['token' => 'draft-token-3']), [
        'full_name' => 'Tourist User',
        'password' => 'password',
        'password_confirmation' => 'password',
        'date_of_birth' => '1998-04-20',
        'phone_number' => '+639171234567',
        'nationality' => 'Filipino',
        'terms_agreed' => '1',
        'identity_consent' => '1',
    ])->assertRedirect(route('dashboard.tourist', absolute: false));

    assertAuthenticated();

    assertDatabaseHas('users', [
        'email' => 'tourist@example.com',
        'full_name' => 'Tourist User',
        'role' => 'tourist',
        'status' => 'active',
    ]);

    assertDatabaseHas('tourists_profile', [
        'phone_number' => '+639171234567',
        'nationality' => 'Filipino',
    ]);
});
