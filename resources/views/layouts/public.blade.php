<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'Trbltours'))</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Asimovian:wght@400;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <!-- Public Navigation Header -->
            <nav class="sticky top-0 z-50 bg-white shadow-sm border-b border-emerald-100">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <a href="{{ url('/') }}" class="flex items-center gap-3">
                                <img src="{{ asset('favicon.png') }}" alt="Trbltours logo" class="h-9 w-9 rounded-full object-cover shadow-sm" />
                                <span class="hidden sm:block text-2xl font-bold text-emerald-700" style="font-family: 'Asimovian', sans-serif;">TrblTours</span>
                            </a>
                        </div>
                        <div class="flex items-center gap-4">
                            @auth
                                <a href="{{ route('dashboard') }}" class="px-4 py-2 text-emerald-600 hover:text-emerald-700 font-medium">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="px-4 py-2 text-emerald-600 hover:text-emerald-700 font-medium">Login</a>
                                <a href="{{ route('signup.start') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-medium">Sign Up</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            @yield('content')
        </div>
    </body>
</html>
