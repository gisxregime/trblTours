<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\GmailMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpVerificationController extends Controller
{
    public function showForm()
    {
        return view('auth.gmail-verify-otp');
    }

    public function verify(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'otp_code' => ['required', 'string', 'size:6'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        // Check if OTP is valid and not expired
        if (! $user || $user->otp_code !== $validated['otp_code']) {
            return back()->with('error', 'Invalid verification code.');
        }

        if ($user->otp_expires_at->isPast()) {
            return back()->with('error', 'Verification code has expired. Please request a new one.');
        }

        // Clear OTP
        $user->update([
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        // If email not yet verified (signup flow), mark as verified
        if ($user->email_verified_at === null) {
            $user->update(['email_verified_at' => now()]);
            $message = 'Email verified successfully! Welcome aboard.';
        } else {
            // Login flow - already verified
            $message = 'Logged in successfully!';
        }

        // Log user in automatically
        Auth::login($user);

        return redirect('/dashboard')->with('success', $message);
    }

    public function resend(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        // Generate new OTP
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp_code' => $otpCode,
            'otp_expires_at' => now()->addMinutes(15),
        ]);

        // Send OTP via Gmail
        $mailService = new GmailMailService;
        $sent = $mailService->sendOTP($user->email, $otpCode, $user->name);

        if (! $sent) {
            return back()->with('error', 'Failed to resend verification code. Please try again.');
        }

        return back()->with('message', 'New verification code sent to your email.');
    }
}
