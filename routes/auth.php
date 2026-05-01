<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\OtpVerificationController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SignupController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::get('gmail-login', [LoginController::class, 'showForm'])
    ->name('gmail.login');

Route::post('gmail-login', [LoginController::class, 'sendOTP'])
    ->name('gmail.login.store');

Route::get('gmail-register', [RegisterController::class, 'showForm'])
    ->name('gmail.register');

Route::post('gmail-register', [RegisterController::class, 'register'])
    ->name('gmail.register.store');

Route::get('verify-otp', [OtpVerificationController::class, 'showForm'])
    ->name('verify.otp.form');

Route::post('verify-otp', [OtpVerificationController::class, 'verify'])
    ->name('verify.otp');

Route::post('resend-otp', [OtpVerificationController::class, 'resend'])
    ->name('resend.otp');

Route::middleware('guest')->group(function () {
    Route::get('signup', [SignupController::class, 'create'])
        ->name('signup.start');

    Route::post('signup/email', [SignupController::class, 'storeEmail'])
        ->name('signup.email.store');

    Route::get('signup/{token}/otp', [SignupController::class, 'createOtp'])
        ->name('signup.otp');

    Route::post('signup/{token}/otp', [SignupController::class, 'storeOtp'])
        ->name('signup.otp.store');

    Route::post('signup/{token}/otp/resend', [SignupController::class, 'resendOtp'])
        ->name('signup.otp.resend');

    Route::get('signup/{token}/role', [SignupController::class, 'createRole'])
        ->name('signup.role');

    Route::post('signup/{token}/role', [SignupController::class, 'storeRole'])
        ->name('signup.role.store');

    Route::get('signup/{token}/details', [SignupController::class, 'createDetails'])
        ->name('signup.details');

    Route::post('signup/{token}/details', [SignupController::class, 'storeDetails'])
        ->name('signup.details.store');

    Route::get('register', [SignupController::class, 'create'])
        ->name('register');

    Route::post('register', [SignupController::class, 'storeEmail']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('guide-login', [AuthenticatedSessionController::class, 'createGuide'])
        ->name('guide.login');

    Route::post('guide-login', [AuthenticatedSessionController::class, 'storeGuide'])
        ->name('guide.login.store');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::get('admin-access/login', [AuthenticatedSessionController::class, 'createAdmin'])
    ->name('admin.login');

Route::post('admin-access/login', [AuthenticatedSessionController::class, 'storeAdmin'])
    ->name('admin.login.store');

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
});
