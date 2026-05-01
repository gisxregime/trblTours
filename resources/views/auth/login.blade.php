<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'TrblTours') }} - Log In</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Asimovian:wght@400;700&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @php
        $selectedRole = old('role', $defaultRole ?? 'tourist');
        $isGuideLogin = in_array($selectedRole, ['guide', 'tour_guide'], true);
        $errorBag = session('errors');
        $firstError = $errorBag?->first();
        $hasErrors = filled($firstError);
    @endphp

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Instrument Sans', sans-serif;
            min-height: 100vh;
            background: linear-gradient(rgba(212, 165, 99, 0.72), rgba(184, 195, 136, 0.56)), url('/images/signup_login_bg.jpg') center/cover no-repeat fixed;
            color: #6f5d52;
        }

        .brand-font {
            font-family: 'Asimovian', 'Instrument Sans', sans-serif;
            letter-spacing: 0.02em;
        }

        .brand-header {
            position: fixed;
            top: clamp(10px, 2.2vw, 24px);
            left: clamp(10px, 2.2vw, 24px);
            display: flex;
            align-items: center;
            gap: clamp(8px, 1vw, 12px);
            z-index: 100;
        }

        .brand-icon {
            width: clamp(34px, 4.2vw, 50px);
            height: clamp(34px, 4.2vw, 50px);
            border-radius: 10px;
            object-fit: cover;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
        }

        .brand-text {
            font-size: clamp(14px, 1.8vw, 20px);
            font-weight: 700;
            color: #fffaf0;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5), 0 4px 12px rgba(0, 0, 0, 0.4);
        }

        .container {
            width: min(100%, 560px);
            background: rgba(255, 251, 244, 0.98);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(101, 67, 33, 0.22);
            padding: 34px;
            border: 1px solid rgba(139, 69, 19, 0.08);
            backdrop-filter: blur(10px);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid #d8c9b5;
            background: #f4efe6;
            border-radius: 9999px;
            padding: 6px 12px;
            color: #3f2d1f;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.06);
            transition: background-color 0.2s ease;
        }

        .back-link:hover {
            background: #ece1d0;
        }

        .title {
            margin-top: 14px;
            font-size: 28px;
            font-weight: 600;
            letter-spacing: -0.02em;
            color: #23170f;
        }

        .subtitle {
            margin-top: 6px;
            font-size: 14px;
            color: #584637;
        }

        .role-toggle {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-top: 16px;
            margin-bottom: 20px;
        }

        .role-btn {
            border: 1.5px solid #d8c9b5;
            border-radius: 12px;
            padding: 12px 16px;
            background: #f7efe1;
            color: #6f5d52;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 600;
        }

        .role-btn.active {
            border-color: #8b4513;
            background: #fff7eb;
            color: #8b4513;
            box-shadow: 0 6px 14px rgba(139, 69, 19, 0.12);
        }

        .role-icon {
            display: inline-block;
            margin-right: 8px;
            font-size: 16px;
        }

        .section-title {
            margin: 18px 0 14px;
            padding-bottom: 8px;
            border-bottom: 2px solid #d4c5b2;
            color: #604c3a;
            text-transform: uppercase;
            letter-spacing: 0.09em;
            font-size: 11px;
            font-weight: 700;
        }

        .alert {
            margin-bottom: 16px;
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 14px;
        }

        .alert.error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert.success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .form-grid {
            display: grid;
            gap: 16px;
        }

        .field {
            display: grid;
            gap: 6px;
        }

        label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            color: #5c4a3a;
        }

        .required::after {
            content: ' *';
            color: #9a4f1d;
        }

        input,
        select {
            width: 100%;
            border: 1px solid #d8c9b5;
            border-radius: 8px;
            padding: 10px 12px;
            background: #f4efe6;
            color: #2f241a;
            font: inherit;
            font-size: 14px;
            outline: none;
        }

        input::placeholder {
            color: #958067;
        }

        input:focus,
        select:focus {
            border-color: #9a5f2a;
        }

        .input-row {
            display: grid;
            gap: 16px;
        }

        @media (min-width: 640px) {
            .input-row.two {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        .password-wrap {
            position: relative;
        }

        .show-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            border: 0;
            background: transparent;
            color: #6f5d52;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            padding: 0;
        }

        .password-meta {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            margin-top: 8px;
        }

        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #6f5d52;
        }

        .submit-btn {
            width: 100%;
            border: 0;
            border-radius: 9999px;
            padding: 12px 16px;
            background: #8b4e1c;
            color: #f8efe3;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.2s ease;
        }

        .submit-btn:hover {
            background: #764116;
            transform: translateY(-1px);
        }

        .footer-links {
            margin-top: 14px;
            text-align: center;
            font-size: 14px;
            color: #6f5b46;
        }

        .footer-links a {
            color: #4b3828;
            font-weight: 600;
            text-decoration: none;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="brand-header">
        <img src="{{ asset('images/tribaltours_icon.png') }}" alt="TrblTours" class="brand-icon">
        <div class="brand-text brand-font">TrblTours</div>
    </div>

    <main class="min-h-screen flex items-center justify-center px-4 py-8">
        <section class="container">
            <a href="{{ url('/') }}" class="back-link">
                <i class="fas fa-arrow-left text-[10px]" aria-hidden="true"></i>
                <span>Back to Home</span>
            </a>

            <h1 class="title">Log in to Your Account</h1>

            <div class="role-toggle" data-role-toggle>
                <button type="button" class="role-btn {{ $isGuideLogin ? '' : 'active' }}" data-role-button="tourist">
                    <span class="role-icon"><i class="fas fa-user"></i></span>
                    <span class="text-sm font-medium">Login as a Tourist</span>
                </button>

                <button type="button" class="role-btn {{ $isGuideLogin ? 'active' : '' }}" data-role-button="tour_guide">
                    <span class="role-icon"><i class="fas fa-map"></i></span>
                    <span class="text-sm font-medium">Login as a Tour Guide</span>
                </button>
            </div>

            @if (session('status'))
                <div class="alert success">{{ session('status') }}</div>
            @endif

            @if ($hasErrors)
                <div class="alert error">
                    <div>{{ $firstError }}</div>
                    @if ($firstError && str_contains($firstError, 'sign up'))
                        <div style="margin-top: 8px;">
                            <a href="{{ route('signup.start') }}" style="color: #991b1b; text-decoration: underline; font-weight: 600;">→ Go to Sign Up</a>
                        </div>
                    @endif
                </div>
            @endif

            <form method="POST" action="{{ $formActionRoute ?? route('login') }}" class="form-grid" data-auth-form>
                @csrf
                <input type="hidden" name="role" value="{{ $selectedRole }}" data-role-input>

                <div>
                    <div class="section-title">Account Information</div>

                    <div class="field">
                        <label for="email" class="required">Email Address</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="catayas@gmail.com"
                        >
                        @error('email')
                            <p class="text-sm text-rose-700">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="field">
                    <label for="password" class="required">Password</label>
                    <div class="password-wrap">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            data-password-input
                        >
                        <button type="button" class="show-toggle" data-password-toggle>Show</button>
                    </div>
                    @error('password')
                        <p class="text-sm text-rose-700">{{ $message }}</p>
                    @enderror

                    @if (Route::has('password.request'))
                        <div class="password-meta">
                            <a href="{{ route('password.request') }}" class="text-sm text-[#6f5b46] hover:underline">Forgot Password?</a>
                        </div>
                    @endif
                </div>

                <label class="checkbox-row">
                    <input id="remember_me" type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-[#c8b49b] text-[#8b4e1c] focus:ring-[#8b4e1c]/30">
                    <span>Remember me</span>
                </label>

                <button type="submit" class="submit-btn">Log in</button>

                <p class="footer-links">
                    New here? <a href="{{ route('signup.start') }}">Create an account.</a>
                </p>
            </form>
        </section>
    </main>

    <script>
        const authForm = document.querySelector('[data-auth-form]');
        const roleInput = authForm?.querySelector('[data-role-input]');
        const topRoleButtons = Array.from(document.querySelectorAll('[data-role-toggle] [data-role-button]'));
        const formRoleButtons = authForm ? Array.from(authForm.querySelectorAll('[data-role-button]')) : [];
        const allRoleButtons = [...topRoleButtons, ...formRoleButtons];
        const passwordInput = authForm?.querySelector('[data-password-input]');
        const passwordToggle = authForm?.querySelector('[data-password-toggle]');

        function updateRoleSelection(selectedRole) {
            if (roleInput) {
                roleInput.value = selectedRole;
            }

            allRoleButtons.forEach((button) => {
                const buttonRole = button.getAttribute('data-role-button');
                button.classList.toggle('active', buttonRole === selectedRole);
            });
        }

        allRoleButtons.forEach((button) => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const selectedRole = button.getAttribute('data-role-button') || 'tourist';
                updateRoleSelection(selectedRole);
            });
        });

        passwordToggle?.addEventListener('click', () => {
            if (! passwordInput) {
                return;
            }

            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            passwordToggle.textContent = isPassword ? 'Hide' : 'Show';
        });
    </script>
</body>
</html>
