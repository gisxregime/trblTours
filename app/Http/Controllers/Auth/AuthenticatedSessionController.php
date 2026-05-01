<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
            'defaultRole' => 'tourist',
            'formActionRoute' => route('login'),
        ]);
    }

    /**
     * Display the guide login view.
     */
    public function createGuide(): View
    {
        return view('auth.login', [
            'defaultRole' => 'tour_guide',
            'formActionRoute' => route('guide.login.store'),
        ]);
    }

    /**
     * Display the hidden admin login view.
     */
    public function createAdmin(Request $request): View
    {
        $currentUser = Auth::user();

        if ($currentUser instanceof User && $currentUser->role !== 'admin') {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return view('auth.admin-login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        return $this->redirectAfterAuthentication($request);
    }

    /**
     * Handle an incoming guide authentication request.
     */
    public function storeGuide(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        return $this->redirectAfterAuthentication($request);
    }

    /**
     * Handle an incoming admin authentication request.
     *
     * @throws ValidationException
     */
    public function storeAdmin(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $admin = User::query()
            ->where('email', (string) $validated['email'])
            ->where('role', 'admin')
            ->first();

        if (! $admin instanceof User || ! Hash::check((string) $validated['password'], $admin->password)) {
            throw ValidationException::withMessages([
                'email' => 'Invalid admin credentials.',
            ]);
        }

        Auth::login($admin, false);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard.admin', absolute: false));
    }

    private function redirectAfterAuthentication(LoginRequest $request): RedirectResponse
    {
        $request->session()->regenerate();

        /** @var User|null $user */
        $user = Auth::user();

        abort_unless($user instanceof User, 500);

        $dashboardRoute = $user->dashboardRouteName();

        return redirect()->intended(route($dashboardRoute, absolute: false));
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
