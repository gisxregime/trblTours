<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ResetPasswordController extends Controller
{
    public function showForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Find reset token
        $hashedToken = hash('sha256', $validated['token']);
        $reset = PasswordReset::where('token', $hashedToken)
            ->where('created_at', '>', now()->subHour())
            ->first();

        if (! $reset) {
            return back()->with('error', 'This password reset link is invalid or has expired.');
        }

        $user = User::find($reset->user_id);

        if ($user->email !== $validated['email']) {
            return back()->with('error', 'The email does not match.');
        }

        // Update password
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Delete used reset token
        $reset->delete();

        return redirect()->route('login')->with('success', 'Password reset successfully! Please log in with your new password.');
    }
}
