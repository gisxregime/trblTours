<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Services\GmailMailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login', [
            'step' => 1,
            'draft' => null,
        ]);
    }

    public function storeEmail(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $token = (string) Str::uuid();
        $now = now();

        DB::table('login_drafts')->updateOrInsert(
            ['email' => $validated['email']],
            [
                'token' => $token,
                'otp_code' => null,
                'otp_expires_at' => null,
                'otp_sent_at' => null,
                'email_verified_at' => null,
                'role' => null,
                'role_selected_at' => null,
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        $draft = $this->findDraft($token);

        if ($draft === null || ! $this->sendOtpForDraft($draft)) {
            return back()->withErrors(['email' => 'Failed to send verification code. Please try again.']);
        }

        return redirect()->route('login.otp', ['token' => $token])
            ->with('status', 'If this email exists in our records, we sent a verification code.');
    }

    public function createOtp(string $token): RedirectResponse|View
    {
        $draft = $this->findDraft($token);

        if ($draft === null) {
            return redirect()->route('login')->with('status', 'That verification session is no longer available.');
        }

        if ($draft->email_verified_at !== null) {
            return redirect()->route('login.role', ['token' => $token]);
        }

        return view('auth.login', [
            'step' => 2,
            'draft' => $draft,
        ]);
    }

    public function storeOtp(Request $request, string $token): RedirectResponse
    {
        $draft = $this->findDraft($token);

        if ($draft === null) {
            return redirect()->route('login')->with('status', 'That verification session is no longer available.');
        }

        $validated = $request->validate([
            'otp_code' => ['required', 'digits:6'],
        ]);

        if (($draft->otp_expires_at === null) || now()->greaterThan($draft->otp_expires_at) || (string) $draft->otp_code !== $validated['otp_code']) {
            return back()->withErrors(['otp_code' => 'Invalid or expired verification code.']);
        }

        DB::table('login_drafts')
            ->where('id', $draft->id)
            ->update([
                'email_verified_at' => now(),
                'otp_code' => null,
                'otp_expires_at' => null,
                'updated_at' => now(),
            ]);

        return redirect()->route('login.role', ['token' => $token])->with('status', 'Email verified. Choose your role to continue.');
    }

    public function resendOtp(string $token): RedirectResponse
    {
        $draft = $this->findDraft($token);

        if ($draft === null) {
            return redirect()->route('login')->with('status', 'That verification session is no longer available.');
        }

        if ($draft->email_verified_at !== null) {
            return redirect()->route('login.role', ['token' => $token]);
        }

        if ($draft->otp_sent_at !== null && now()->diffInSeconds($draft->otp_sent_at) < 30) {
            return back()->withErrors(['otp_code' => 'Please wait a few seconds before requesting another code.']);
        }

        if (! $this->sendOtpForDraft($draft)) {
            return back()->withErrors(['otp_code' => 'Failed to resend verification code. Please try again.']);
        }

        return back()->with('status', 'A new verification code has been sent to your email.');
    }

    public function createRole(string $token): RedirectResponse|View
    {
        $draft = $this->verifiedDraftOrRedirect($token);

        if ($draft instanceof RedirectResponse) {
            return $draft;
        }

        return view('auth.login', [
            'step' => 3,
            'draft' => $draft,
        ]);
    }

    public function storeRole(Request $request, string $token): RedirectResponse
    {
        $draft = $this->verifiedDraftOrRedirect($token);

        if ($draft instanceof RedirectResponse) {
            return $draft;
        }

        $validated = $request->validate([
            'role' => ['required', 'in:tourist,tour_guide'],
        ]);

        DB::table('login_drafts')
            ->where('id', $draft->id)
            ->update([
                'role' => $validated['role'],
                'role_selected_at' => now(),
                'updated_at' => now(),
            ]);

        return redirect()->route('login.password', ['token' => $token]);
    }

    public function createPassword(string $token): RedirectResponse|View
    {
        $draft = $this->readyDraftOrRedirect($token);

        if ($draft instanceof RedirectResponse) {
            return $draft;
        }

        return view('auth.login', [
            'step' => 4,
            'draft' => $draft,
        ]);
    }

    /**
     * Display the hidden admin login view.
     */
    public function createAdmin(Request $request): View|RedirectResponse
    {
        if ($request->user()?->role === 'admin') {
            return redirect()->route('dashboard.admin');
        }

        if ($request->user() !== null) {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return view('auth.admin-login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function storePassword(Request $request, string $token): RedirectResponse
    {
        $draft = $this->readyDraftOrRedirect($token);

        if ($draft instanceof RedirectResponse) {
            return $draft;
        }

        $validated = $request->validate([
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $user = $this->findUserForDraft($draft);

        if ($user === null || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'password' => trans('auth.failed'),
            ]);
        }

        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        DB::table('login_drafts')->where('id', $draft->id)->delete();

        return redirect()->route($user->dashboardRouteName());
    }

    private function findDraft(string $token): ?object
    {
        return DB::table('login_drafts')->where('token', $token)->first();
    }

    private function verifiedDraftOrRedirect(string $token): mixed
    {
        $draft = $this->findDraft($token);

        if ($draft === null || $draft->email_verified_at === null) {
            return redirect()->route('login')->with('status', 'Please verify your OTP code before selecting a role.');
        }

        return $draft;
    }

    private function readyDraftOrRedirect(string $token): mixed
    {
        $draft = $this->verifiedDraftOrRedirect($token);

        if ($draft instanceof RedirectResponse) {
            return $draft;
        }

        if (($draft->role ?? null) === null) {
            return redirect()->route('login.role', ['token' => $token])->with('status', 'Choose your role first.');
        }

        return $draft;
    }

    private function findUserForDraft(object $draft): ?User
    {
        $query = User::query()->where('email', (string) $draft->email);

        if ($draft->role === 'tour_guide') {
            $query->whereIn('role', ['guide', 'tour_guide']);
        } else {
            $query->where('role', 'tourist');
        }

        return $query->first();
    }

    private function sendOtpForDraft(object $draft): bool
    {
        $otpCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('login_drafts')
            ->where('id', $draft->id)
            ->update([
                'otp_code' => $otpCode,
                'otp_expires_at' => now()->addMinutes(15),
                'otp_sent_at' => now(),
                'updated_at' => now(),
            ]);

        $mailService = app(GmailMailService::class);

        return $mailService->sendOTP((string) $draft->email, $otpCode, 'Traveler');
    }

    /**
     * Handle an incoming admin authentication request.
     *
     * @throws ValidationException
     */
    public function storeAdmin(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        if ($request->user()?->role !== 'admin') {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Admin access is restricted.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->route('dashboard.admin');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
