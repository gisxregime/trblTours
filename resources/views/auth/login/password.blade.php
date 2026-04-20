<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'TrblTours') }} - Login Password</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Asimovian:wght@400;700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .brand-font {
            font-family: 'Asimovian', 'Instrument Sans', sans-serif;
            letter-spacing: 0.02em;
        }

        body {
            font-family: 'Instrument Sans', sans-serif;
            background: linear-gradient(rgba(212, 165, 99, 0.72), rgba(184, 195, 136, 0.56)), url('/images/signup_login_bg.jpg') center/cover no-repeat fixed;
            color: #6f5d52;
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
    </style>
</head>
<body>
    <div class="brand-header">
        <img src="{{ asset('images/tribaltours_icon.png') }}" alt="TrblTours" class="brand-icon">
        <div class="brand-text brand-font">TrblTours</div>
    </div>

    <main class="min-h-screen flex items-center justify-center px-4 py-8">
        <section class="w-full max-w-xl rounded-2xl border border-[#d4a563]/30 bg-[#fffbf4] p-8 shadow-[0_18px_40px_-20px_rgba(122,85,50,0.45)]">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 rounded-full border border-[#d8c9b5] bg-[#f4efe6] px-3 py-1.5 text-[13px] font-medium text-[#4b3828] shadow-sm transition hover:bg-[#ece1d0]">
                <i class="fas fa-arrow-left text-[10px]" aria-hidden="true"></i>
                <span>Back to Home</span>
            </a>

            <h1 class="mt-4 text-3xl font-semibold text-[#23170f]">Enter Your Password</h1>
            <p class="mt-2 text-sm text-[#6f5d52]">Step 3 of 3 for {{ $draft->email }} ({{ $draft->role === 'tour_guide' ? 'Tour Guide' : 'Tourist' }}).</p>

            @if (session('errors')?->any())
                <div class="mt-4 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
                    {{ session('errors')->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.password.store', ['token' => $draft->token]) }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <label for="password" class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-[#5c4a3a]">Password</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="w-full rounded-lg border border-[#d8c9b5] bg-[#f4efe6] px-3 py-2.5 text-sm text-[#2f241a] outline-none transition focus:border-[#9a5f2a] focus:ring-0"
                    >
                </div>

                <label for="remember" class="inline-flex items-center gap-2 text-sm text-[#6f5d52]">
                    <input id="remember" type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-[#c8b49b] text-[#8b4e1c] focus:ring-[#8b4e1c]/30">
                    Remember me
                </label>

                <button type="submit" class="w-full rounded-full bg-[#8b4e1c] px-4 py-2.5 text-sm font-semibold text-[#f8efe3] transition hover:bg-[#764116]">
                    Log in
                </button>
            </form>
        </section>
    </main>
</body>
</html>