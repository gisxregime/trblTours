<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\OtpVerificationController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // ===== SIMPLIFIED GMAIL OTP AUTH SYSTEM (Signup + Login) =====
    // Registration with OTP verification
    Route::get('gmail/register', [RegisterController::class, 'showForm'])->name('gmail.register');
    Route::post('gmail/register', [RegisterController::class, 'register'])->name('gmail.register.store');

    // OTP verification (used for both signup AND login)
    Route::get('gmail/verify-otp', [OtpVerificationController::class, 'showForm'])->name('verify.otp.form');
    Route::post('gmail/verify-otp', [OtpVerificationController::class, 'verify'])->name('verify.otp');
    Route::post('gmail/resend-otp', [OtpVerificationController::class, 'resend'])->name('resend.otp');

    // Login with OTP
    Route::get('gmail/login', [LoginController::class, 'showForm'])->name('gmail.login');
    Route::post('gmail/login', [LoginController::class, 'sendOTP'])->name('gmail.login.store');

    // Password Reset
    Route::get('gmail/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('gmail.forgot.password');
    Route::post('gmail/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('gmail.reset.link.send');
    Route::get('gmail/reset-password/{token}', [ResetPasswordController::class, 'showForm'])->name('password.reset.form');
    Route::post('gmail/reset-password', [ResetPasswordController::class, 'update'])->name('password.reset.update');

    // ===== ORIGINAL BREEZE AUTH ROUTES (Partial compatibility) =====
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login/email', [AuthenticatedSessionController::class, 'storeEmail'])
        ->name('login.email.store');

    Route::get('login/{token}/otp', [AuthenticatedSessionController::class, 'createOtp'])
        ->name('login.otp');

    Route::post('login/{token}/otp', [AuthenticatedSessionController::class, 'storeOtp'])
        ->name('login.otp.store');

    Route::post('login/{token}/otp/resend', [AuthenticatedSessionController::class, 'resendOtp'])
        ->middleware('throttle:6,1')
        ->name('login.otp.resend');

    Route::get('login/{token}/role', [AuthenticatedSessionController::class, 'createRole'])
        ->name('login.role');

    Route::post('login/{token}/role', [AuthenticatedSessionController::class, 'storeRole'])
        ->name('login.role.store');

    Route::get('login/{token}/password', [AuthenticatedSessionController::class, 'createPassword'])
        ->name('login.password');

    Route::post('login/{token}/password', [AuthenticatedSessionController::class, 'storePassword'])
        ->name('login.password.store');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    Route::post('gmail/logout', [LoginController::class, 'logout'])
        ->name('gmail.logout');
});

Route::middleware('throttle:6,1')->group(function () {
    Route::get('portal/system-access', [AuthenticatedSessionController::class, 'createAdmin'])
        ->name('admin.login');

    Route::post('portal/system-access', [AuthenticatedSessionController::class, 'storeAdmin'])
        ->name('admin.login.store');
});
