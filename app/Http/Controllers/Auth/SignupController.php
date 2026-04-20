<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\GmailMailService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class SignupController extends Controller
{
    public function create(): View
    {
        return view('auth.signup.flow', [
            'step' => 1,
            'draft' => null,
            'isGuide' => false,
        ]);
    }

    public function storeEmail(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:'.User::class.',email'],
        ]);

        $token = (string) Str::uuid();
        $now = now();

        DB::table('signup_drafts')->updateOrInsert(
            ['email' => $validated['email']],
            [
                'token' => $token,
                'otp_code' => null,
                'otp_expires_at' => null,
                'otp_sent_at' => null,
                'email_verified_at' => null,
                'role' => null,
                'role_selected_at' => null,
                'completed_at' => null,
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        $draft = $this->findDraft($token);

        if ($draft === null || ! $this->sendOtpForDraft($draft)) {
            return back()->withErrors(['email' => 'Failed to send verification code. Please try again.']);
        }

        return redirect()->route('signup.otp', ['token' => $token])
            ->with('status', 'We sent a verification code to your email address.');
    }

    public function createOtp(string $token): RedirectResponse|View
    {
        $draft = $this->findDraft($token);

        if ($draft === null) {
            return redirect()->route('signup.start')->with('status', 'That verification session is no longer available.');
        }

        if ($draft->email_verified_at !== null) {
            return redirect()->route('signup.role', ['token' => $token]);
        }

        return view('auth.signup.flow', [
            'step' => 2,
            'draft' => $draft,
            'isGuide' => false,
        ]);
    }

    public function storeOtp(Request $request, string $token): RedirectResponse
    {
        $draft = $this->findDraft($token);

        if ($draft === null) {
            return redirect()->route('signup.start')->with('status', 'That verification session is no longer available.');
        }

        $validated = $request->validate([
            'otp_code' => ['required', 'digits:6'],
        ]);

        if (($draft->otp_expires_at === null) || now()->greaterThan($draft->otp_expires_at) || (string) $draft->otp_code !== $validated['otp_code']) {
            return back()->withErrors(['otp_code' => 'Invalid or expired verification code.']);
        }

        DB::table('signup_drafts')
            ->where('id', $draft->id)
            ->update([
                'email_verified_at' => now(),
                'otp_code' => null,
                'otp_expires_at' => null,
                'updated_at' => now(),
            ]);

        return redirect()->route('signup.role', ['token' => $token])->with('status', 'Email verified. Choose your role to continue.');
    }

    public function resendOtp(string $token): RedirectResponse
    {
        $draft = $this->findDraft($token);

        if ($draft === null) {
            return redirect()->route('signup.start')->with('status', 'That verification session is no longer available.');
        }

        if ($draft->email_verified_at !== null) {
            return redirect()->route('signup.role', ['token' => $token]);
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

        return view('auth.signup.flow', [
            'step' => 3,
            'draft' => $draft,
            'isGuide' => false,
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

        DB::table('signup_drafts')
            ->where('id', $draft->id)
            ->update([
                'role' => $validated['role'],
                'role_selected_at' => now(),
                'updated_at' => now(),
            ]);

        return redirect()->route('signup.details', ['token' => $token]);
    }

    public function createDetails(string $token): RedirectResponse|View
    {
        $draft = $this->readyDraftOrRedirect($token);

        if ($draft instanceof RedirectResponse) {
            return $draft;
        }

        return view('auth.signup.flow', [
            'step' => 4,
            'draft' => $draft,
            'isGuide' => $draft->role === 'tour_guide',
        ]);
    }

    public function storeDetails(Request $request, string $token): RedirectResponse
    {
        $draft = $this->readyDraftOrRedirect($token);

        if ($draft instanceof RedirectResponse) {
            return $draft;
        }

        $role = (string) $draft->role;

        $commonRules = [
            'full_name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'phone_number' => ['required', 'string', 'max:32'],
            'nationality' => ['required', 'string', 'max:100'],
            'terms_agreed' => ['accepted'],
            'identity_consent' => ['accepted'],
        ];

        $roleRules = $role === 'tour_guide'
            ? [
                'government_id_type' => ['required', 'in:national_id,passport,drivers_license,other'],
                'government_id_number' => ['required', 'string', 'max:100'],
                'years_of_experience' => ['required', 'integer', 'min:0', 'max:80'],
                'bio' => ['required', 'string', 'max:2000'],
                'nbi_clearance_number' => ['required', 'string', 'max:100'],
                'barangay_clearance_number' => ['nullable', 'string', 'max:100'],
                'tour_guide_cert_number' => ['nullable', 'string', 'max:100'],
                'pending_understood' => ['accepted'],
            ]
            : [];

        $validated = $request->validate(array_merge($commonRules, $roleRules));

        DB::transaction(function () use ($draft, $role, $validated): void {
            $status = $role === 'tour_guide' ? 'pending' : 'active';

            $user = User::create([
                'name' => $validated['full_name'],
                'full_name' => $validated['full_name'],
                'email' => $draft->email,
                'role' => $role,
                'status' => $status,
                'email_verified_at' => now(),
                'password' => Hash::make($validated['password']),
            ]);

            if ($role === 'tour_guide') {
                DB::table('tour_guides_profile')->insert([
                    'user_id' => $user->id,
                    'phone_number' => $validated['phone_number'],
                    'nationality' => $validated['nationality'],
                    'date_of_birth' => $validated['date_of_birth'],
                    'years_of_experience' => $validated['years_of_experience'],
                    'bio' => $validated['bio'],
                    'government_id_type' => $validated['government_id_type'],
                    'government_id_number' => $validated['government_id_number'],
                    'tour_guide_cert_number' => $validated['tour_guide_cert_number'] ?? null,
                    'nbi_clearance_number' => $validated['nbi_clearance_number'],
                    'barangay_clearance_number' => $validated['barangay_clearance_number'] ?? null,
                    'terms_agreed' => true,
                    'identity_consent' => true,
                    'pending_understood' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('tourists_profile')->insert([
                    'user_id' => $user->id,
                    'phone_number' => $validated['phone_number'],
                    'nationality' => $validated['nationality'],
                    'date_of_birth' => $validated['date_of_birth'],
                    'tourist_id_type' => 'passport',
                    'tourist_id_number' => null,
                    'terms_agreed' => true,
                    'identity_consent' => true,
                    'pending_understood' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('signup_drafts')->where('id', $draft->id)->delete();

            event(new Registered($user));

            Auth::login($user);
        });

        $user = Auth::user();

        if ($user === null) {
            return redirect()->route('signup.start')->with('status', 'Your account could not be created. Please try again.');
        }

        return redirect()->route($user->dashboardRouteName());
    }

    private function findDraft(string $token): ?object
    {
        return DB::table('signup_drafts')->where('token', $token)->first();
    }

    private function verifiedDraftOrRedirect(string $token): mixed
    {
        $draft = $this->findDraft($token);

        if ($draft === null || $draft->email_verified_at === null) {
            return redirect()->route('signup.start')->with('status', 'Please verify your OTP code before choosing a role.');
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
            return redirect()->route('signup.role', ['token' => $token])->with('status', 'Choose your role first.');
        }

        return $draft;
    }

    private function sendOtpForDraft(object $draft): bool
    {
        $otpCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('signup_drafts')
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
}
