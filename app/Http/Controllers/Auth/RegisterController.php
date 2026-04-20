<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\GmailMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function showForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Generate 6-digit OTP
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Create user with OTP
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'otp_code' => $otpCode,
            'otp_expires_at' => now()->addMinutes(15),
            'email_verified_at' => null,
        ]);

        // Send OTP via Gmail
        $mailService = new GmailMailService;
        $sent = $mailService->sendOTP($user->email, $otpCode, $user->name);

        if (! $sent) {
            $user->delete();

            return back()->with('error', 'Failed to send verification email. Please try again.');
        }

        return redirect()->route('verify.otp.form')->with('message', 'Verification code sent to your email. Please check your inbox.');
    }
}
