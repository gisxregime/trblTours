<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\User;
use App\Services\GmailMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function showForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        // For security, always show success message even if user doesn't exist
        if (! $user) {
            return back()->with('message', 'If an account exists with this email, a reset link has been sent.');
        }

        // Generate unique reset token
        $token = Str::random(60);

        // Store reset token in database
        PasswordReset::create([
            'user_id' => $user->id,
            'token' => hash('sha256', $token),
        ]);

        // Create reset URL
        $resetUrl = url(route('password.reset.form', ['token' => $token], false));

        // Send reset link via Gmail
        $mailService = new GmailMailService;
        $sent = $mailService->sendResetLink($user->email, $token, $user->name, $resetUrl);

        if (! $sent) {
            return back()->with('error', 'Failed to send reset link. Please try again.');
        }

        return back()->with('message', 'If an account exists with this email, a reset link has been sent.');
    }
}
