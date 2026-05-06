<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Trbltours') }} | Explore Tours</title>

    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Asimovian:wght@400;700&family=Cal+Sans&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])

    <style>
        :root {
            --sand-100: #fff8eb;
            --sand-200: #f5e8cc;
            --sand-300: #e7d2a9;
            --gold: #d4a563;
            --olive: #8f9d59;
            --brown-700: #6f5d52;
            --brown-800: #5a4a42;
            --brown-900: #3f2d22;
            --accent: #6f3e2c;
            --white: #ffffff;
            --shadow-lg: 0 14px 36px rgba(63, 45, 34, 0.18);
            --shadow-md: 0 10px 22px rgba(63, 45, 34, 0.12);
            --radius-lg: 16px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Instrument Sans', sans-serif;
            color: var(--brown-800);
            background: linear-gradient(180deg, #fffbf3 0%, #f7f0df 58%, #f2e7cf 100%);
            min-height: 100vh;
        }

        .brand-font {
            font-family: 'Asimovian', sans-serif;
            letter-spacing: 0.02em;
        }

        .hero-font {
            font-family: 'Cal Sans', 'Instrument Sans', sans-serif;
            letter-spacing: 0.02em;
            word-spacing: 0.08em;
        }

        .container {
            width: min(1200px, 92vw);
            margin: 0 auto;
        }

        .site-header {
            position: sticky;
            top: 0;
            z-index: 40;
            background: rgba(255, 248, 235, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(111, 62, 44, 0.12);
            transition: background-color 0.24s ease, border-color 0.24s ease, box-shadow 0.24s ease;
        }

        .site-header.scrolled {
            background: rgba(212, 165, 99, 0.82);
            border-bottom-color: rgba(111, 62, 44, 0.28);
            box-shadow: 0 10px 24px rgba(63, 45, 34, 0.2);
        }

        .header-inner {
            min-height: 82px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            position: relative;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .brand-logo {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            object-fit: cover;
            box-shadow: var(--shadow-md);
        }

        .brand-name {
            font-size: clamp(18px, 2.4vw, 24px);
            color: var(--brown-900);
        }

        .header-nav {
            display: inline-flex;
            align-items: center;
            gap: 38px;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: max-content;
        }

        .header-nav a {
            text-decoration: none;
            color: var(--brown-800);
            font-size: 16px;
            font-weight: 700;
        }

        .header-nav a.active {
            color: var(--accent);
        }

        .page {
            padding: 32px 0 72px;
        }

        .page-head {
            display: grid;
            gap: 8px;
            margin-bottom: 20px;
        }

        .page-head h1 {
            color: var(--brown-900);
            font-size: clamp(34px, 5vw, 48px);
            line-height: 1.08;
        }

        .page-head p {
            color: var(--brown-700);
            max-width: 68ch;
            font-size: 15px;
            line-height: 1.65;
        }

        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .filter-chip {
            min-height: 40px;
            border-radius: 999px;
            border: 1px solid rgba(111, 62, 44, 0.24);
            background: rgba(255, 248, 235, 0.92);
            color: var(--brown-800);
            font-size: 14px;
            font-weight: 700;
            padding: 0 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .filter-chip.active {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
        }

        .tours-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .tour-card {
            border-radius: 16px;
            border: 1px solid rgba(111, 62, 44, 0.12);
            background: #f7f5f1;
            box-shadow: 0 16px 30px rgba(63, 45, 34, 0.15);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
            position: relative;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .tour-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 34px rgba(63, 45, 34, 0.2);
        }

        .tour-card::after {
            content: '♡';
            position: absolute;
            top: 12px;
            right: 12px;
            width: 38px;
            height: 38px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.95);
            color: #3f2d22;
            font-size: 22px;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(111, 62, 44, 0.16);
            z-index: 2;
            box-shadow: 0 6px 18px rgba(63, 45, 34, 0.18);
        }

        .tour-card-image {
            height: 250px;
            background-size: cover;
            background-position: center;
            border-bottom: 1px solid rgba(139, 69, 19, 0.14);
            filter: saturate(0.88) contrast(1.02);
        }

        .tour-card-body {
            padding: 50px 20px 20px;
            display: grid;
            gap: 8px;
            align-content: start;
            flex: 1;
            position: relative;
            background: #f3f2f0;
        }

        .tour-card-body .tour-line:first-child {
            color: #221f1d;
            font-size: 1.25rem;
            line-height: 1.3;
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        .tour-card-body .tour-line:nth-child(5) {
            position: absolute;
            left: 14px;
            right: 14px;
            top: -20px;
            background: rgba(255, 255, 255, 0.86);
            border: 1px solid rgba(111, 62, 44, 0.16);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 999px;
            padding: 8px 14px 8px 56px;
            font-size: 14px;
            color: #2f2b29;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .tour-card-body .tour-line:nth-child(5)::before {
            content: '';
            position: absolute;
            left: 8px;
            top: 50%;
            transform: translateY(-50%);
            width: 36px;
            height: 36px;
            border-radius: 999px;
            border: 2px solid rgba(255, 255, 255, 0.95);
            box-shadow: 0 4px 10px rgba(63, 45, 34, 0.2);
            background: radial-gradient(circle at 30% 30%, #fff8eb, #8f9d59 70%);
        }

        .tour-card-body .tour-line:nth-child(6) {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
            color: #6f5d52;
            font-size: 13px;
        }

        .tour-card-body .tour-line:nth-child(3) {
            margin-top: auto;
            font-size: 1.125rem;
            line-height: 1.3;
            color: #242220;
            font-weight: 700;
        }

        .tour-line {
            font-size: 14px;
            color: var(--brown-700);
            line-height: 1.55;
        }

        .tour-line strong {
            color: var(--brown-900);
        }

        @media (max-width: 980px) {
            .header-nav {
                display: none;
            }

            .tours-grid {
                grid-template-columns: 1fr;
            }
        }

        @include('partials.tour-request-modal-styles')
    </style>
    @livewireStyles
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
    <header class="site-header">
        <div class="container header-inner">
            <a class="brand" href="{{ url('/') }}">
                <img class="brand-logo" src="{{ asset('images/tribaltours_icon.png') }}" alt="Trbltours">
                <span class="brand-name brand-font">Trbltours</span>
            </a>

            <nav class="header-nav" aria-label="Primary navigation">
                <a class="active" href="{{ route('explore-tours') }}">Explore Tours</a>
                <a href="{{ url('/#tourist-tips') }}">Become a Guide</a>
                <a href="{{ route('how-it-works') }}">How It Works</a>
                <a href="{{ url('/#why-choose-us') }}">Safety Tips</a>
            </nav>
        </div>
    </header>

    <main class="page">
        <section class="container">

            <livewire:explore-tours-feed context="explore" />
        </section>
    </main>

    @include('partials.tour-request-modal')

    <script>
        (() => {
            const siteHeader = document.querySelector('.site-header');

            if (!siteHeader) {
                return;
            }

            const updateHeaderOnScroll = () => {
                siteHeader.classList.toggle('scrolled', window.scrollY > 8);
            };

            window.addEventListener('scroll', updateHeaderOnScroll, { passive: true });
            updateHeaderOnScroll();
        })();
    </script>

    @include('partials.tour-request-modal-script')
    @livewireScripts
</body>

</html>
