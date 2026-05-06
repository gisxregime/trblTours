<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrblTours - Guide Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600&family=Asimovian:wght@400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#fffaf3] font-inter text-[#3e2a1f]">
    <header class="sticky top-0 z-40 border-b border-[#d4a563]/25 bg-[rgba(92,64,51,0.94)] backdrop-blur-sm">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('dashboard.guide.dashboard') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/tribaltours_icon.png') }}" alt="TrblTours Icon" class="h-10 w-10 rounded-full border border-[#d4a563]/50 bg-[#f6ecd8] object-cover shadow-md">
                <span class="font-[Asimovian] text-2xl font-bold tracking-wide text-[#f8eed8]">TrblTours</span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="rounded-full border border-[#e8d2ab] bg-[#fff6e8] px-4 py-2 text-sm font-semibold text-[#5a3c2a] transition hover:bg-[#f7e8cf]">
                    Logout
                </button>
            </form>
        </div>
    </header>

    <main class="mx-auto flex min-h-[calc(100vh-84px)] max-w-7xl items-center justify-center px-4 py-10 sm:px-6 lg:px-8">
        <section class="w-full max-w-2xl rounded-3xl border border-[#dcc39d] bg-white p-8 text-center shadow-[0_18px_44px_-24px_rgba(58,39,26,0.65)] sm:p-10">
            <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-2xl border border-[#d4a563]/60 bg-[#fff3de] text-[#8f9d59] shadow-sm">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 8v5m0 4h.01M4.93 19h14.14c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.2 16c-.77 1.33.19 3 1.73 3z"/>
                </svg>
            </div>

            <h1 class="font-[Asimovian] text-3xl font-bold tracking-wide text-[#5a3c2a] sm:text-4xl">
                Guide Dashboard Not Available Yet
            </h1>
            <p class="mx-auto mt-4 max-w-xl text-sm leading-relaxed text-[#73543e] sm:text-base">
                The guide dashboard is currently under improvement and will be available soon.
                Please check back later.
            </p>

            <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-xl border border-[#c9a26a] bg-[#d4a563] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#bf9155]">
                    Back to Dashboard
                </a>
                <a href="{{ route('profile.edit') }}" class="inline-flex items-center justify-center rounded-xl border border-[#cbb08a] bg-[#fff8ea] px-5 py-2.5 text-sm font-semibold text-[#6b4a34] transition hover:bg-[#f5e9d4]">
                    Edit Profile
                </a>
            </div>
        </section>
    </main>
</body>
</html>
