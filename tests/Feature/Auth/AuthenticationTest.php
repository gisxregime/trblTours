<?php

use App\Models\User;
use App\Services\GmailMailService;
use Illuminate\Support\Facades\DB;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\assertGuest;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

beforeEach(function (): void {
    $otpMailer = Mockery::mock(GmailMailService::class);
    $otpMailer->shouldReceive('sendOTP')->andReturn(true);

    app()->instance(GmailMailService::class, $otpMailer);
});

test('login screen can be rendered', function () {
    $response = get('/login');

    $response->assertSuccessful();
});

test('login otp can be requested', function () {
    post('/login/email', [
        'email' => 'tourist@example.com',
    ]);

    $draft = DB::table('login_drafts')->where('email', 'tourist@example.com')->first();

    expect($draft)->not->toBeNull();
    expect($draft->otp_code)->not->toBeNull();
    expect($draft->otp_expires_at)->not->toBeNull();
});

test('role step is blocked until email is verified', function () {
    DB::table('login_drafts')->insert([
        'token' => 'login-token-1',
        'email' => 'pending-login@example.com',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    get(route('login.role', ['token' => 'login-token-1']))
        ->assertRedirect(route('login', absolute: false));
});

test('users can authenticate using the staged login flow', function () {
    $user = User::factory()->create([
        'email' => 'tourist@example.com',
        'role' => 'tourist',
    ]);

    DB::table('login_drafts')->insert([
        'token' => 'login-token-2',
        'email' => $user->email,
        'email_verified_at' => now(),
        'role' => 'tourist',
        'role_selected_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = post(route('login.password.store', ['token' => 'login-token-2']), [
        'password' => 'password',
    ]);

    assertAuthenticated();
    $response->assertRedirect(route('dashboard.tourist', absolute: false));
});

test('otp verification redirects to role step', function () {
    DB::table('login_drafts')->insert([
        'token' => 'login-token-3',
        'email' => 'verified-login@example.com',
        'otp_code' => '123456',
        'otp_expires_at' => now()->addMinutes(10),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    post(route('login.otp.store', ['token' => 'login-token-3']), [
        'otp_code' => '123456',
    ])
        ->assertRedirect(route('login.role', ['token' => 'login-token-3'], absolute: false));

    expect(DB::table('login_drafts')->where('token', 'login-token-3')->value('email_verified_at'))->not->toBeNull();
});

test('tour guides can authenticate with guide role selection', function () {
    $user = User::factory()->create([
        'email' => 'guide@example.com',
        'role' => 'guide',
    ]);

    DB::table('login_drafts')->insert([
        'token' => 'login-token-4',
        'email' => $user->email,
        'email_verified_at' => now(),
        'role' => 'tour_guide',
        'role_selected_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = post(route('login.password.store', ['token' => 'login-token-4']), [
        'password' => 'password',
    ]);

    assertAuthenticated();
    $response->assertRedirect(route('dashboard.guide', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create([
        'email' => 'tourist-bad@example.com',
        'role' => 'tourist',
    ]);

    DB::table('login_drafts')->insert([
        'token' => 'login-token-5',
        'email' => $user->email,
        'email_verified_at' => now(),
        'role' => 'tourist',
        'role_selected_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    post(route('login.password.store', ['token' => 'login-token-5']), [
        'password' => 'wrong-password',
    ]);

    assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = actingAs($user)->post('/logout');

    assertGuest();
    $response->assertRedirect('/');
});
