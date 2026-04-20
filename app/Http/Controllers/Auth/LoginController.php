<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\GmailMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showForm()
    {
        return view('auth.gmail-login');
    }

    public function sendOTP(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Find user by email
        $user = User::where('email', $validated['email'])->first();

        // For security, show message even if user doesn't exist
        if (! $user) {
            return redirect()->route('verify.otp.form')
                ->with('message', 'If an account exists with this email, an OTP has been sent.')
                ->with('email', $validated['email']);
        }

        // User must have verified email (completed signup)
        if ($user->email_verified_at === null) {
            return redirect()->route('gmail.register')
                ->with('error', 'Please complete registration first.');
        }

        // Generate OTP
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp_code' => $otpCode,
            'otp_expires_at' => now()->addMinutes(15),
        ]);

        // Send OTP via Gmail
        $mailService = new GmailMailService;
        $sent = $mailService->sendOTP($user->email, $otpCode, $user->name);

        if (! $sent) {
            return back()->with('error', 'Failed to send OTP. Please try again.');
        }

        return redirect()->route('verify.otp.form')
            ->with('message', 'OTP sent to your email. Please check your inbox.')
            ->with('email', $validated['email']);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
