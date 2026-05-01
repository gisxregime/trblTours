<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'role' => ['nullable', 'string', 'in:tourist,tour_guide'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $email = (string) $this->string('email');
        $requestedRole = $this->requestedRole();
        $matchingRoles = $this->matchingRolesForRequestedRole($requestedRole);

        $matchingUser = User::query()
            ->where('email', $email)
            ->whereIn('role', $matchingRoles)
            ->first();

        $conflictingUser = User::query()
            ->where('email', $email)
            ->whereNotIn('role', $matchingRoles)
            ->first();

        if ($matchingUser === null) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => $conflictingUser instanceof User
                    ? $this->roleMismatchMessage($conflictingUser->role, $requestedRole)
                    : $this->noAccountMessage($requestedRole),
            ]);
        }

        if (! Hash::check((string) $this->string('password'), $matchingUser->password)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => $this->invalidCredentialsMessage(),
            ]);
        }

        Auth::login($matchingUser, $this->boolean('remember'));

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * @return array<int, string>
     */
    private function matchingRolesForRequestedRole(string $requestedRole): array
    {
        if ($requestedRole === 'tour_guide') {
            return ['guide', 'tour_guide'];
        }

        return ['tourist'];
    }

    private function requestedRole(): string
    {
        if ($this->isGuideLoginRoute()) {
            return 'tour_guide';
        }

        $role = (string) $this->string('role', 'tourist');

        return in_array($role, ['guide', 'tour_guide'], true) ? 'tour_guide' : 'tourist';
    }

    private function isGuideLoginRoute(): bool
    {
        return $this->routeIs('guide.login.store');
    }

    private function displayRole(string $role): string
    {
        return in_array($role, ['guide', 'tour_guide'], true) ? 'Tour Guide' : 'Tourist';
    }

    private function noAccountMessage(string $requestedRole): string
    {
        if ($requestedRole === 'tour_guide') {
            return 'No account found. Please sign up as a Tour Guide.';
        }

        return 'No account found. Please sign up to continue.';
    }

    private function roleMismatchMessage(string $existingRole, string $requestedRole): string
    {
        $isExistingGuide = in_array($existingRole, ['guide', 'tour_guide'], true);

        if ($requestedRole === 'tour_guide' && ! $isExistingGuide) {
            return 'This email is registered as a Tourist. Please log in as a Tourist or apply to be a Guide.';
        }

        if ($requestedRole === 'tourist' && $isExistingGuide) {
            return 'This email is registered as a Tour Guide. Please log in as a Tour Guide.';
        }

        $currentRole = $this->displayRole($existingRole);
        $desiredRole = $requestedRole === 'tour_guide' ? 'Tour Guide' : 'Tourist';

        return "This email is registered as a $currentRole account. Please log in as $desiredRole.";
    }

    private function invalidCredentialsMessage(): string
    {
        return 'Incorrect email or password. Please check your details and try again.';
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
