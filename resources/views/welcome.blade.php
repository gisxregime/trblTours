<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Trbltours') }} | Philippine Local Tours</title>

    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Asimovian:wght@400;700&family=Cal+Sans&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet">

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
            --accent: #8b4513;
            --white: #ffffff;
            --shadow-lg: 0 14px 36px rgba(63, 45, 34, 0.18);
            --shadow-md: 0 10px 22px rgba(63, 45, 34, 0.12);
            --radius-xl: 22px;
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
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            z-index: 40;
            background: transparent;
        }

        .header-inner {
            min-height: 92px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .brand-logo {
            width: clamp(38px, 4.6vw, 54px);
            height: clamp(38px, 4.6vw, 54px);
            border-radius: 12px;
            object-fit: cover;
            box-shadow: var(--shadow-md);
        }

        .brand-name {
            font-size: clamp(18px, 2.4vw, 24px);
            color: #fff8eb;
            text-shadow: 0 2px 8px rgba(43, 30, 22, 0.35);
        }

        .header-nav {
            display: inline-flex;
            align-items: center;
            gap: 22px;
        }

        .header-nav a {
            text-decoration: none;
            color: #fff8eb;
            font-size: 15px;
            font-weight: 600;
            text-shadow: 0 2px 6px rgba(43, 30, 22, 0.3);
        }

        .language-pill {
            min-width: 160px;
            min-height: 40px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.45);
            background: rgba(255, 248, 235, 0.28);
            color: #fff8eb;
            font-weight: 700;
            padding: 0 14px;
            outline: none;
        }

        .header-actions {
            display: inline-flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .btn {
            border: none;
            border-radius: 12px;
            min-height: 44px;
            padding: 10px 16px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .btn-primary {
            background: #fff8eb;
            color: #9f732f;
            box-shadow: var(--shadow-md);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            background: #fff1d7;
        }

        .btn-ghost {
            background: rgba(255, 248, 235, 0.08);
            color: #fff8eb;
            border: 1px solid rgba(255, 255, 255, 0.6);
        }

        .btn-ghost:hover {
            transform: translateY(-1px);
            background: rgba(255, 248, 235, 0.18);
        }

        .hero {
            position: relative;
            overflow: hidden;
            padding: 92px 0 24px;
            min-height: 100svh;
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 15% 18%, rgba(212, 165, 99, 0.28), transparent 40%), radial-gradient(circle at 85% 10%, rgba(143, 157, 89, 0.24), transparent 45%);
            pointer-events: none;
            z-index: 1;
        }

        .hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(178, 145, 80, 0.58) 0%, rgba(178, 145, 80, 0.68) 100%);
            z-index: 1;
            pointer-events: none;
        }

        .hero-background {
            position: absolute;
            inset: 0;
            z-index: 0;
        }

        .hero-carousel {
            height: 100%;
            position: relative;
        }

        .hero-slide {
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity 0.6s ease;
            pointer-events: none;
        }

        .hero-slide.active {
            opacity: 1;
            pointer-events: auto;
        }

        .hero-slide::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg, rgba(139, 69, 19, 0.46), rgba(111, 93, 82, 0.42)), linear-gradient(180deg, rgba(43, 30, 22, 0.48), rgba(43, 30, 22, 0.58));
        }

        .hero-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .hero-inner {
            position: relative;
            z-index: 2;
            display: grid;
            min-height: calc(100svh - 122px);
            align-content: center;
            gap: 22px;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.15fr 1.15fr;
            align-items: center;
            gap: 46px;
            max-width: 1120px;
            margin: 0 auto;
            width: 100%;
        }

        .hero-grid>div {
            display: grid;
            justify-items: start;
            text-align: left;
            gap: 46px;
            width: 100%;
            max-width: 520px;
            margin: 0 auto;
        }

        .hero-kicker {
            display: inline-flex;
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(255, 248, 235, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.4);
            font-weight: 700;
            color: #fff8eb;
            margin-bottom: 6px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .hero h1 {
            color: #fff8eb;
            font-size: clamp(34px, 5.6vw, 56px);
            line-height: 1.08;
            margin-bottom: 4px;
            text-shadow: 0 10px 24px rgba(43, 30, 22, 0.55);
            max-width: 14ch;
        }

        .hero-small-note {
            max-width: 60ch;
            color: #fff7e8;
            font-size: clamp(13px, 1.6vw, 15px);
            line-height: 1.00;
            font-weight: 600;
            text-shadow: 0 2px 8px rgba(43, 30, 22, 0.4);
        }

        .hero-favorites-label {
            color: #fff8eb;
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 8px;
            text-align: left;
            text-shadow: 0 2px 8px rgba(43, 30, 22, 0.35);
        }

        .favorites-grid {
            position: relative;
            width: min(560px, 100%);
            min-height: 372px;
        }

        .favorite-card {
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.18);
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 10px 24px rgba(43, 30, 22, 0.28);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(50px);
            opacity: 0;
            transform: translateX(20px) scale(0.97);
            transition: opacity 1.10s ease, transform 0.55s ease;
            pointer-events: none;
        }

        .favorite-card.active {
            opacity: 2;
            transform: translateX(0) scale(1);
            pointer-events: auto;
        }

        .favorite-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.22) 100%);
            z-index: 0;
        }

        .favorite-card>* {
            position: relative;
            z-index: 1;
        }

        .favorite-card-image {
            height: 292px;
            background-size: cover;
            background-position: center;
        }

        .favorite-card-body {
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
        }

        .favorite-card h6 {
            font-size: clamp(14px, 1.8vw, 20px);
            color: var(--brown-900);
            line-height: 1.2;
            margin: 0;
        }

        .hero-copy {
            max-width: 58ch;
            color: #fff4df;
            font-size: clamp(14px, 1.8vw, 16px);
            line-height: 1.6;
            margin-top: 4px;
            text-shadow: 0 3px 10px rgba(43, 30, 22, 0.45);
        }

        .hero-search {
            margin-top: 30px;
            position: relative;
            background: rgba(255, 248, 235, 0.96);
            border-radius: 999px;
            padding: 7px;
            border: 1px solid rgba(212, 165, 99, 0.28);
            box-shadow: 0 12px 30px rgba(43, 30, 22, 0.24);
            overflow: visible;
        }

        .hero-search-grid {
            display: grid;
            grid-template-columns: 2.4fr 1.6fr auto;
            gap: 0;
            align-items: center;
        }

        .search-segment {
            min-height: 72px;
            padding: 0 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
        }

        .search-segment + .search-segment {
            border-left: 1px solid #d4d0cb;
        }

        .search-segment-trigger {
            cursor: pointer;
            user-select: none;
            position: relative;
        }

        .search-icon {
            color: #4b5055;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }

        .search-icon svg {
            width: 22px;
            height: 22px;
        }

        .search-input,
        .search-select {
            width: 100%;
            border: none;
            min-height: 42px;
            background: transparent;
            padding: 0;
            color: #41484f;
            font-size: 16px;
            font-weight: 500;
            outline: none;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            cursor: text;
        }

        .search-select {
            cursor: pointer;
        }

        .search-display {
            color: #41484f;
            font-size: 16px;
            font-weight: 500;
            line-height: 1.2;
            display: inline-flex;
            align-items: center;
            min-height: 22px;
        }

        .search-native-date {
            position: absolute;
            width: 1px;
            height: 1px;
            opacity: 0;
            pointer-events: none;
        }

        .search-chevron {
            margin-left: auto;
            color: #5f534b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .search-chevron svg {
            width: 16px;
            height: 16px;
        }

        .place-suggestions {
            position: absolute;
            top: auto;
            bottom: calc(100% + 12px);
            left: 0;
            width: min(360px, 88vw);
            border-radius: 16px;
            background: #fff8eb;
            border: 1px solid rgba(212, 165, 99, 0.32);
            box-shadow: 0 14px 30px rgba(63, 45, 34, 0.2);
            padding: 8px;
            display: none;
            z-index: 70;
            max-height: 280px;
            overflow: auto;
        }

        .place-suggestions.open {
            display: grid;
            gap: 4px;
        }

        .place-suggestion-item {
            border: none;
            background: transparent;
            color: var(--brown-900);
            font-size: 15px;
            font-weight: 500;
            text-align: left;
            border-radius: 10px;
            min-height: 38px;
            padding: 0 12px;
            cursor: pointer;
        }

        .place-suggestion-item:hover,
        .place-suggestion-item:focus-visible {
            background: #f3e5c8;
            outline: none;
        }

        .date-toggle {
            width: 100%;
            border: none;
            background: transparent;
            padding: 0;
            display: inline-flex;
            align-items: center;
            text-align: left;
            color: inherit;
            font: inherit;
            cursor: pointer;
        }

        .date-toggle:focus-visible {
            outline: 2px solid rgba(139, 69, 19, 0.45);
            outline-offset: 2px;
            border-radius: 8px;
        }

        .date-dropdown {
            position: absolute;
            top: auto;
            bottom: calc(100% + 12px);
            left: 0;
            width: min(360px, 88vw);
            border-radius: 20px;
            background: #fff8eb;
            border: 1px solid rgba(212, 165, 99, 0.32);
            box-shadow: 0 18px 36px rgba(63, 45, 34, 0.2);
            padding: 16px;
            z-index: 70;
            display: none;
        }

        .date-dropdown.open {
            display: block;
        }

        .date-dropdown-title {
            color: var(--brown-900);
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .date-fields {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .date-field {
            display: grid;
            gap: 6px;
        }

        .date-field label {
            color: var(--brown-700);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .date-field input {
            width: 100%;
            min-height: 38px;
            border: 1px solid #d8d2c7;
            border-radius: 10px;
            background: #fffefb;
            color: var(--brown-900);
            font-size: 14px;
            padding: 0 10px;
        }

        .date-duration {
            margin-top: 12px;
            color: var(--brown-800);
            font-size: 14px;
            font-weight: 600;
        }

        .guests-toggle {
            width: 100%;
            border: none;
            background: transparent;
            padding: 0;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            text-align: left;
            color: inherit;
            font: inherit;
            cursor: pointer;
        }

        .guests-toggle:focus-visible {
            outline: 2px solid rgba(139, 69, 19, 0.45);
            outline-offset: 2px;
            border-radius: 8px;
        }

        .guests-dropdown {
            position: absolute;
            top: auto;
            bottom: calc(100% + 12px);
            left: 0;
            width: min(360px, 86vw);
            border-radius: 20px;
            background: #fff8eb;
            border: 1px solid rgba(212, 165, 99, 0.32);
            box-shadow: 0 18px 36px rgba(63, 45, 34, 0.2);
            padding: 20px;
            z-index: 60;
            display: none;
        }

        .guests-dropdown.open {
            display: block;
        }

        .guests-title {
            color: var(--brown-900);
            font-size: 18px;
            font-weight: 800;
            margin-bottom: 16px;
        }

        .guest-row {
            display: grid;
            grid-template-columns: 1fr auto;
            align-items: center;
            gap: 14px;
            margin-bottom: 14px;
        }

        .guest-row:last-of-type {
            margin-bottom: 18px;
        }

        .guest-label {
            color: var(--brown-900);
            font-size: 16px;
            font-weight: 600;
            line-height: 1.2;
        }

        .guest-subtext {
            color: var(--brown-700);
            font-size: 14px;
            margin-top: 2px;
        }

        .guest-counter {
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .counter-button {
            width: 42px;
            height: 42px;
            border-radius: 999px;
            border: 1px solid #d8d2c7;
            background: #f5efe1;
            color: #5f534b;
            font-size: 28px;
            line-height: 1;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .counter-button:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        .counter-value {
            min-width: 22px;
            text-align: center;
            color: var(--brown-900);
            font-size: 16px;
            font-weight: 700;
        }

        .guests-apply {
            width: 122px;
            min-height: 42px;
            border: none;
            border-radius: 999px;
            background: linear-gradient(180deg, #d4a563 0%, #bb8547 100%);
            color: #fff;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            margin-left: auto;
            display: block;
            box-shadow: 0 10px 20px rgba(111, 93, 82, 0.25);
        }

        .search-input::placeholder {
            color: #41484f;
            opacity: 1;
        }

        .search-action {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 7px;
            padding-left: 12px;
            min-height: 72px;
            border-left: 1px solid #d4d0cb;
        }

        .search-button {
            width: auto;
            min-height: 56px;
            padding: 0 24px;
            border: none;
            border-radius: 999px;
            background: linear-gradient(180deg, #d4a563 0%, #bc8747 100%);
            color: #fff;
            font-weight: 800;
            font-size: 16px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 8px 20px rgba(111, 93, 82, 0.35);
        }

        .search-button .search-icon {
            color: #fff;
        }

        .why,
        .tours,
        .tips,
        .roles-cta {
            padding: 0 0 clamp(58px, 8vw, 96px);
        }

        .section-head {
            text-align: center;
            margin-bottom: clamp(20px, 4vw, 32px);
        }

        .section-head h2 {
            color: var(--brown-900);
            font-size: clamp(28px, 4.5vw, 42px);
            margin-top: 80px;
            margin-bottom: 10px;
        }

        .section-head p {
            color: var(--brown-700);
            max-width: 64ch;
            margin: 0 auto;
            line-height: 1.65;
            font-size: clamp(14px, 2vw, 17px);
        }

        .why-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        .why-card {
            border-radius: var(--radius-lg);
            background: linear-gradient(180deg, #fffdf9 0%, #f8f1df 100%);
            border: 1px solid rgba(139, 69, 19, 0.14);
            box-shadow: var(--shadow-md);
            padding: 22px 18px;
        }

        .why-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: rgba(212, 165, 99, 0.2);
            color: var(--accent);
            display: grid;
            place-items: center;
            font-weight: 800;
            font-size: 18px;
            margin-bottom: 12px;
        }

        .why-card h3 {
            color: var(--brown-900);
            font-size: clamp(19px, 2.4vw, 24px);
            margin-bottom: 8px;
        }

        .why-card p {
            color: var(--brown-700);
            line-height: 1.65;
            font-size: 14px;
        }

        .tours-grid,
        .tips-grid,
        .roles-cta-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-top: 18px;
        }

        .tour-card,
        .tips-card,
        .roles-cta-card {
            border-radius: var(--radius-lg);
            border: 1px solid rgba(139, 69, 19, 0.14);
            background: linear-gradient(180deg, #fffefb 0%, #f8f1df 100%);
            box-shadow: var(--shadow-md);
        }

        .tour-card {
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .tour-card-image {
            height: 160px;
            background-size: cover;
            background-position: center;
            border-bottom: 1px solid rgba(139, 69, 19, 0.14);
        }

        .tour-card-body {
            padding: 16px;
            display: grid;
            gap: 7px;
        }

        .tour-line {
            font-size: 14px;
            color: var(--brown-700);
            line-height: 1.55;
        }

        .tour-line strong {
            color: var(--brown-900);
        }

        .tips-card {
            padding: 18px;
            display: grid;
            gap: 12px;
        }

        .tips-card h3 {
            color: var(--brown-900);
            font-size: clamp(19px, 2.2vw, 24px);
            line-height: 1.25;
        }

        .tips-card p {
            color: var(--brown-700);
            font-size: 14px;
            line-height: 1.65;
        }

        .tips-meta {
            color: var(--brown-700);
            font-weight: 700;
            font-size: 13px;
        }

        .tips-link {
            color: var(--accent);
            font-weight: 800;
            text-decoration: none;
            width: fit-content;
        }

        .tips-footer {
            margin-top: 22px;
            display: flex;
            justify-content: center;
        }

        .tips-button,
        .roles-cta-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            min-height: 44px;
            padding: 10px 16px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 800;
            transition: transform 0.2s ease, background 0.2s ease;
        }

        .tips-button,
        .roles-cta-button.primary {
            background: var(--accent);
            color: var(--white);
            box-shadow: var(--shadow-md);
        }

        .tips-button:hover,
        .roles-cta-button.primary:hover {
            transform: translateY(-1px);
            background: #71370f;
        }

        .roles-cta-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .roles-cta-card {
            padding: clamp(18px, 3vw, 28px);
            display: grid;
            gap: 14px;
        }

        .roles-cta-media {
            width: 100%;
            height: clamp(180px, 24vw, 230px);
            border-radius: 12px;
            object-fit: cover;
            border: 1px solid rgba(139, 69, 19, 0.12);
            box-shadow: 0 10px 20px rgba(63, 45, 34, 0.12);
        }

        .roles-cta-card h3 {
            color: var(--brown-900);
            font-size: clamp(24px, 3.2vw, 34px);
            line-height: 1.08;
        }

        .roles-cta-card p {
            color: var(--brown-700);
            font-size: 15px;
            line-height: 1.68;
            max-width: 50ch;
        }

        .roles-cta-button.ghost {
            background: transparent;
            color: var(--brown-800);
            border: 1px solid rgba(139, 69, 19, 0.3);
        }

        .site-footer {
            background: radial-gradient(circle at 88% 8%, rgba(212, 165, 99, 0.18), transparent 30%), radial-gradient(circle at 10% 100%, rgba(143, 157, 89, 0.12), transparent 36%), linear-gradient(180deg, #23160f 0%, #1a110c 100%);
            border-top: 1px solid rgba(212, 165, 99, 0.24);
            color: #f4e9d6;
            padding: clamp(34px, 5vw, 52px) 0 18px;
        }

        .footer-top {
            display: grid;
            grid-template-columns: 1.3fr 1fr 1fr 1fr 1fr 0.95fr;
            gap: 20px;
            padding-bottom: 22px;
            border-bottom: 1px solid rgba(212, 165, 99, 0.22);
        }

        .footer-brand-head {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .footer-brand-logo {
            width: 38px;
            height: 38px;
            border-radius: 999px;
            object-fit: cover;
            border: 1px solid rgba(255, 255, 255, 0.35);
        }

        .footer-brand-name {
            color: #fff4df;
            font-size: clamp(24px, 2.4vw, 32px);
            line-height: 1;
        }

        .footer-col h3 {
            color: #f6e9d2;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .footer-about {
            color: rgba(244, 233, 214, 0.9);
            font-size: 14px;
            line-height: 1.65;
            margin-bottom: 10px;
        }

        .footer-social-icons {
            display: flex;
            gap: 8px;
            margin-top: 10px;
        }

        .footer-social-icons a {
            width: 28px;
            height: 28px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(244, 233, 214, 0.34);
            color: #f4e9d6;
            text-decoration: none;
            font-size: 12px;
            font-weight: 800;
        }

        .footer-list {
            list-style: none;
            display: grid;
            gap: 8px;
            font-size: 14px;
            line-height: 1.45;
        }

        .footer-list a {
            color: rgba(244, 233, 214, 0.92);
            text-decoration: none;
        }

        .footer-list a:hover {
            text-decoration: underline;
            color: #fff9ee;
        }

        .footer-contact-item {
            display: flex;
            gap: 8px;
            align-items: flex-start;
            color: rgba(244, 233, 214, 0.92);
            font-size: 14px;
            line-height: 1.45;
            margin-bottom: 10px;
        }

        .footer-contact-item a {
            color: rgba(244, 233, 214, 0.92);
            text-decoration: none;
        }

        .footer-contact-item a:hover {
            text-decoration: underline;
            color: #fff9ee;
        }

        .footer-contact-item svg {
            width: 14px;
            height: 14px;
            margin-top: 3px;
            flex: 0 0 14px;
        }

        .footer-purpose {
            border: 1px solid rgba(244, 233, 214, 0.24);
            border-radius: 12px;
            padding: 14px;
            background: rgba(255, 255, 255, 0.04);
        }

        .footer-purpose h4 {
            color: #f6e9d2;
            font-size: 18px;
            margin-bottom: 8px;
        }

        .footer-purpose p {
            color: rgba(244, 233, 214, 0.9);
            font-size: 13px;
            line-height: 1.55;
        }

        .footer-row {
            padding: 14px 0;
            border-bottom: 1px solid rgba(212, 165, 99, 0.2);
            color: rgba(244, 233, 214, 0.92);
            font-size: 14px;
            line-height: 1.6;
        }

        .footer-bottom-links {
            padding-top: 12px;
            text-align: center;
            font-size: 12px;
            line-height: 1.4;
        }

        .footer-bottom-links a {
            color: rgba(244, 233, 214, 0.92);
            text-decoration: none;
        }

        .footer-bottom-links a:hover {
            text-decoration: underline;
        }

        @media (max-width: 980px) {
            .hero {
                min-height: 100svh;
                padding: 88px 0 20px;
            }

            .hero-inner {
                min-height: calc(100svh - 114px);
                gap: 18px;
            }

            .header-nav {
                display: none;
            }

            .hero-grid,
            .tips-grid {
                grid-template-columns: 2fr;
            }

            .hero-search-grid {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }

            .hero-search {
                border-radius: 24px;
                padding: 14px;
            }

            .search-segment {
                min-height: 56px;
                border-radius: 14px;
                border: 1px solid #d4d0cb;
                background: #fffefb;
            }

            .search-segment + .search-segment {
                border-left: none;
            }

            .search-action {
                grid-column: 1 / -1;
                border-left: none;
                padding: 0;
                min-height: 56px;
            }

            .guests-dropdown {
                top: calc(100% + 12px);
                bottom: auto;
                right: 0;
                left: auto;
            }

            .date-dropdown {
                top: calc(100% + 12px);
                bottom: auto;
                right: 0;
                left: auto;
            }

            .place-suggestions {
                top: calc(100% + 12px);
                bottom: auto;
                right: 0;
                left: auto;
            }

            .why-grid,
            .tours-grid,
            .roles-cta-grid {
                grid-template-columns: 1fr;
            }

            .footer-top {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .header-inner {
                min-height: 70px;
                gap: 12px;
            }

            .header-actions {
                width: 100%;
                justify-content: flex-start;
            }

            .hero {
                padding: 82px 0 16px;
            }

            .hero-inner {
                min-height: calc(100svh - 102px);
            }

            .favorites-grid {
                min-height: 332px;
            }

            .hero-search-grid {
                grid-template-columns: 1fr;
            }

            .search-action {
                justify-content: stretch;
            }

            .search-button {
                width: 100%;
                min-height: 52px;
            }

            .guests-dropdown {
                left: 0;
                right: 0;
                width: 100%;
            }

            .date-dropdown {
                left: 0;
                right: 0;
                width: 100%;
            }

            .place-suggestions {
                left: 0;
                right: 0;
                width: 100%;
            }

            .date-fields {
                grid-template-columns: 1fr;
            }

            .search-segment + .search-segment {
                border-left: none;
            }

            .footer-top {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <header class="site-header">
        <div class="container header-inner">
            <a class="brand" href="{{ url('/') }}">
                <img class="brand-logo" src="{{ asset('images/tribaltours_icon.png') }}" alt="Trbltours">
                <span class="brand-name brand-font">Trbltours</span>
            </a>

            <nav class="header-nav" aria-label="Primary navigation">
                <a href="#">Home</a>
                <a href="#tourist-tips">Blogs</a>
                <a href="#why-choose-us">About</a>
                <select class="language-pill" aria-label="Language">
                    <option selected>English</option>
                    <option>Filipino</option>
                </select>
            </nav>

            <div class="header-actions">
                @auth
                    <a class="btn btn-ghost" href="{{ route('dashboard') }}">Dashboard</a>
                @else
                    <a class="btn btn-ghost" href="{{ route('login') }}">Log In</a>
                    @if (Route::has('register'))
                        <a class="btn btn-primary" href="{{ route('signup.start') }}">Sign Up</a>
                    @endif
                @endauth
            </div>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="hero-background">
                <div class="hero-carousel" id="heroCarousel">
                    <div class="hero-slide active"><img src="{{ asset('hero/caoursel1.webp') }}" alt="Philippine island landscape"></div>
                    <div class="hero-slide"><img src="{{ asset('hero/carousel2.jpg') }}" alt="Philippine local destination"></div>
                    <div class="hero-slide"><img src="{{ asset('hero/carousel3.jpg') }}" alt="Philippine scenic spot"></div>
                    <div class="hero-slide"><img src="{{ asset('hero/carousel4.jpg') }}" alt="Philippine coastline"></div>
                    <div class="hero-slide"><img src="{{ asset('hero/carousel5.jpg') }}" alt="Philippine island tour"></div>
                </div>
            </div>

            <div class="container hero-inner">
                <div class="hero-grid">
                    <div>
                        <span class="hero-kicker">SUMMIT ESCAPES | TROPICAL PARADISES | HERITAGE SITES</span>
                        <h1 class="hero-font">Confused Where to Start in the Philippines? Let a Local Guide Show You.</h1>
                        <p class="hero-small-note">For tourists who want real experiences. For local guides who want to share their hometown.</p>
                    </div>

                    <div>
                        <p class="hero-favorites-label">Guest Favorites</p>
                        <div class="favorites-grid" id="favoritesCarousel">
                            <article class="favorite-card active">
                                <div class="favorite-card-image" style="background-image: url('{{ asset('hero/elnido.jpg') }}');"></div>
                                <div class="favorite-card-body"><h6>Trip to El Nido</h6></div>
                            </article>
                            <article class="favorite-card">
                                <div class="favorite-card-image" style="background-image: url('{{ asset('hero/palawan.jpg') }}');"></div>
                                <div class="favorite-card-body"><h6>Trip to Palawan</h6></div>
                            </article>
                            <article class="favorite-card">
                                <div class="favorite-card-image" style="background-image: url('{{ asset('hero/batad.jpg') }}');"></div>
                                <div class="favorite-card-body"><h6>Trip to Batad</h6></div>
                            </article>
                        </div>
                        <p class="hero-copy">7,641 islands. Countless hidden spots. Skip the tourist traps — explore through the eyes of someone who actually lives there.</p>
                    </div>
                </div>

                <div class="hero-search">
                    <div class="hero-search-grid">
                        <div class="search-segment" id="locationTrigger">
                            <span class="search-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 21s7-4.35 7-11a7 7 0 1 0-14 0c0 6.65 7 11 7 11Z" />
                                    <circle cx="12" cy="10" r="2.5" />
                                </svg>
                            </span>
                            <input id="accommodation" class="search-input" type="text" placeholder="Where to?" autocomplete="off" aria-label="Where to" aria-autocomplete="list" aria-controls="placeSuggestions">
                            <div id="placeSuggestions" class="place-suggestions" role="listbox" aria-label="Place suggestions"></div>
                        </div>
                        <div class="search-segment" id="anytimeTrigger">
                            <span class="search-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="5" width="18" height="16" rx="2" />
                                    <path d="M16 3v4M8 3v4M3 10h18" />
                                </svg>
                            </span>
                            <button class="date-toggle" id="anytimeToggle" type="button" aria-expanded="false" aria-controls="dateDropdown" aria-label="Select dates">
                                <span id="anytimeLabel" class="search-display">Anytime</span>
                            </button>
                            <div class="date-dropdown" id="dateDropdown" role="dialog" aria-label="Select stay dates">
                                <p class="date-dropdown-title">Select your stay</p>
                                <div class="date-fields">
                                    <div class="date-field">
                                        <label for="checkinDate">Check-in</label>
                                        <input id="checkinDate" type="date">
                                    </div>
                                    <div class="date-field">
                                        <label for="checkoutDate">Check-out</label>
                                        <input id="checkoutDate" type="date">
                                    </div>
                                </div>
                                <p class="date-duration" id="stayDuration">Duration: Select both dates</p>
                            </div>
                        </div>
                        <div class="search-action">
                            <button class="search-button" type="button">
                                <span class="search-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="7" />
                                        <path d="m20 20-3.5-3.5" />
                                    </svg>
                                </span>
                                Search
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="why-choose-us" class="why">
            <div class="container">
                <div class="section-head">
                    <h2 class="hero-font">Why Choose Us</h2>
                    <p>Built for curious travelers who want meaningful local experiences, with reliable support and flexible options from planning to booking.</p>
                </div>
                <div class="why-grid">
                    <article class="why-card"><div class="why-icon">01</div><h3>Handpicked Local Guides</h3><p>We vet and curate every guide for local expertise, hospitality, and safety standards so each tour feels personal and trustworthy.</p></article>
                    <article class="why-card"><div class="why-icon">02</div><h3>Eco-Friendly Travel</h3><p>Our platform promotes responsible tourism by supporting low-impact itineraries, local communities, and conservation-minded tour partners.</p></article>
                    <article class="why-card"><div class="why-icon">03</div><h3>Flexible Booking</h3><p>Choose schedules that fit your trip, adjust plans with ease, and confirm your adventure through convenient and secure payment options.</p></article>
                </div>
            </div>
        </section>

        <section id="tours" class="tours">
            <div class="container">
                <div class="section-head"><h2 class="hero-font">Featured Local Tours</h2><p>Choose from immersive, community-led experiences designed by people who call these places home.</p></div>
                <div class="tours-grid">
                    <article class="tour-card"><div class="tour-card-image" style="background-image:url('{{ asset('hero/batad.jpg') }}')"></div><div class="tour-card-body"><h1 class="tour-line">Batad Rice Terraces Heritage Walk</h1><p class="tour-line"><strong>Duration:</strong> 3 days</p><p class="tour-line"><strong>Price:</strong> PHP 3,200 per person</p><p class="tour-line"><strong>Location:</strong> Ifugao, Philippines</p><p class="tour-line"><strong>Led by:</strong> Mang Ramon from Batad</p><p class="tour-line"><strong>Description:</strong> Walk the 2,000-year-old terraces with Mang Ramon, a native guide who grew up here.</p></div></article>
                    <article class="tour-card"><div class="tour-card-image" style="background-image:url('{{ asset('hero/davao.jpg') }}')"></div><div class="tour-card-body"><h1 class="tour-line">Davao Hidden Falls and Tribal Encounter</h1><p class="tour-line"><strong>Duration:</strong> 3 days</p><p class="tour-line"><strong>Price:</strong> PHP 3,500 per person</p><p class="tour-line"><strong>Location:</strong> Davao, Philippines</p><p class="tour-line"><strong>Led by:</strong> Ate Ligaya from Davao</p><p class="tour-line"><strong>Description:</strong> Visit waterfalls even locals keep secret. Share a meal with an indigenous guide family.</p></div></article>
                    <article class="tour-card"><div class="tour-card-image" style="background-image:url('{{ asset('hero/elnido.jpg') }}')"></div><div class="tour-card-body"><h1 class="tour-line">El Nido Secret Lagoon Tour</h1><p class="tour-line"><strong>Duration:</strong> 2 days</p><p class="tour-line"><strong>Price:</strong> PHP 4,200 per person</p><p class="tour-line"><strong>Location:</strong> Palawan, Philippines</p><p class="tour-line"><strong>Led by:</strong> Kuya Jun from El Nido</p><p class="tour-line"><strong>Description:</strong> Skip crowded boats. Your guide knows the quiet lagoons and best snorkel spots.</p></div></article>
                    <article class="tour-card"><div class="tour-card-image" style="background-image:url('{{ asset('hero/palawan.jpg') }}')"></div><div class="tour-card-body"><h1 class="tour-line">Palawan Coastal Village Experience</h1><p class="tour-line"><strong>Duration:</strong> 3 days</p><p class="tour-line"><strong>Price:</strong> PHP 3,800 per person</p><p class="tour-line"><strong>Location:</strong> Palawan, Philippines</p><p class="tour-line"><strong>Led by:</strong> Nanay Rosa from San Vicente</p><p class="tour-line"><strong>Description:</strong> Stay in a small fishing village and share home-cooked meals with locals.</p></div></article>
                    <article class="tour-card"><div class="tour-card-image" style="background-image:url('{{ asset('hero/pangasinan.jpg') }}')"></div><div class="tour-card-body"><h1 class="tour-line">Pangasinan Hundred Islands Local Boat Tour</h1><p class="tour-line"><strong>Duration:</strong> 2 days</p><p class="tour-line"><strong>Price:</strong> PHP 2,900 per person</p><p class="tour-line"><strong>Location:</strong> Pangasinan, Philippines</p><p class="tour-line"><strong>Led by:</strong> Kuya Ben from Alaminos</p><p class="tour-line"><strong>Description:</strong> Explore caves and sandbars tourists often miss with a hometown boatman.</p></div></article>
                    <article class="tour-card"><div class="tour-card-image" style="background-image:url('{{ asset('hero/puertoprincessa.jpg') }}')"></div><div class="tour-card-body"><h1 class="tour-line">Puerto Princesa Underground River and Local Lunch</h1><p class="tour-line"><strong>Duration:</strong> 2 days</p><p class="tour-line"><strong>Price:</strong> PHP 3,500 per person</p><p class="tour-line"><strong>Location:</strong> Palawan, Philippines</p><p class="tour-line"><strong>Led by:</strong> Tatay Rico from Puerto Princesa</p><p class="tour-line"><strong>Description:</strong> Beat the crowds and enjoy lunch prepared by a local family.</p></div></article>
                </div>
            </div>
        </section>

        <section id="tourist-tips" class="tips">
            <div class="container">
                <div class="section-head"><h2 class="hero-font">Tourist Tips</h2><p>Short, honest reads. No fluff. Just what you actually need to know.</p></div>
                <div class="tips-grid">
                    <article class="tips-card"><h3>How to NOT Get Scammed as a Tourist in the Philippines</h3><p>Red flags to watch out for. What prices should actually look like. And why booking a local guide is safer than going alone.</p><p class="tips-meta">4 min read</p><a class="tips-link" href="#">Read -></a></article>
                    <article class="tips-card"><h3>How to Choose a Tour Package When You Have No Idea Where to Go</h3><p>Step 1: Admit you are confused. Step 2: Pick a vibe (beach, mountain, food). Step 3: Let a local guide take it from there.</p><p class="tips-meta">3 min read</p><a class="tips-link" href="#">Read -></a></article>
                    <article class="tips-card"><h3>Why Booking With a Local Guide is Better Than a Big Tour Company</h3><p>Money stays in the community. You get real stories, not scripts. The food is better when a local picks the spot.</p><p class="tips-meta">5 min read</p><a class="tips-link" href="#">Read -></a></article>
                </div>
                <div class="tips-footer"><a class="tips-button" href="#">View all tips -></a></div>
            </div>
        </section>

        <section class="roles-cta">
            <div class="container roles-cta-grid">
                <article class="roles-cta-card">
                    <img class="roles-cta-media" src="{{ asset('images/tourist.png') }}" alt="Tourist looking for local guidance">
                    <h3 class="hero-font">I am a confused tourist</h3>
                    <p>I want to explore the Philippines but do not know where to start. I want real spots, real food, and a local who knows the way.</p>
                    <a class="roles-cta-button primary" href="{{ route('signup.start') }}">Match me with a local guide -></a>
                </article>
                <article class="roles-cta-card">
                    <img class="roles-cta-media" src="{{ asset('images/tourguide.jpg') }}" alt="Local guide showcasing hometown culture">
                    <h3 class="hero-font">I am a local guide</h3>
                    <p>I know my hometown like the back of my hand. I want to share my stories and hidden spots and earn from what I love.</p>
                    <a class="roles-cta-button ghost" href="{{ route('signup.start') }}">Apply to showcase my hometown -></a>
                </article>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="container">
            <div class="footer-top">
                <section class="footer-col">
                    <div class="footer-brand-head">
                        <img class="footer-brand-logo" src="{{ asset('images/tribaltours_icon.png') }}" alt="Trbltours">
                        <h3 class="footer-brand-name hero-font">trbTours</h3>
                    </div>
                    <p class="footer-about">"Helping confused tourists find real locals. Helping locals earn from their hometown pride."</p>
                    <p class="footer-about">Trbltours is not a big corporation. We are a small team based in the Philippines, connecting travelers to local guides who deserve to be seen.</p>
                    <p class="footer-about">Made with love and pride for every barrio, province, and island.</p>
                    <div class="footer-social-icons">
                        <a href="{{ url('/social/facebook') }}" aria-label="Facebook">f</a>
                        <a href="{{ url('/social/instagram') }}" aria-label="Instagram">ig</a>
                        <a href="{{ url('/social/tiktok') }}" aria-label="TikTok">tt</a>
                        <a href="{{ url('/social/youtube') }}" aria-label="YouTube">yt</a>
                    </div>
                </section>
                <section class="footer-col">
                    <h3>For Tourists</h3>
                    <ul class="footer-list">
                        <li><a href="{{ url('/tours') }}">Browse all tours</a></li>
                        <li><a href="{{ url('/tips/choose-a-package') }}">How to choose a package (tips)</a></li>
                        <li><a href="{{ url('/tips/travel-tips-for-beginners') }}">Travel tips for beginners</a></li>
                        <li><a href="{{ url('/faq/first-timers') }}">FAQ for first-timers</a></li>
                        <li><a href="{{ url('/support/human') }}">Talk to a human (we reply within hours)</a></li>
                    </ul>
                </section>
                <section class="footer-col">
                    <h3>For Local Guides</h3>
                    <ul class="footer-list">
                        <li><a href="{{ url('/guides/apply') }}">Apply to showcase your hometown</a></li>
                        <li><a href="{{ url('/guides/requirements') }}">Guide requirements (just be local, really)</a></li>
                        <li><a href="{{ url('/guides/payouts') }}">How payouts work</a></li>
                        <li><a href="{{ url('/guides/success-stories') }}">Guide success stories</a></li>
                        <li><a href="{{ url('/guides/resources') }}">Free guide resources (photo tips, etc.)</a></li>
                    </ul>
                </section>
                <section class="footer-col">
                    <h3>Learn and Blog</h3>
                    <ul class="footer-list">
                        <li><a href="{{ url('/blog/why-book-with-locals') }}">Why book with locals?</a></li>
                        <li><a href="{{ url('/blog/how-to-spot-tourist-traps') }}">How to spot tourist traps</a></li>
                        <li><a href="{{ url('/blog/first-tour-expectations') }}">What to expect on your first tour</a></li>
                        <li><a href="{{ url('/blog/responsible-tourism-guide') }}">Responsible tourism guide</a></li>
                        <li><a href="{{ url('/blog') }}">All blog posts</a></li>
                    </ul>
                </section>
                <section class="footer-col">
                    <h3>Get in Touch</h3>
                    <div class="footer-contact-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4z"></path><path d="m22 6-10 7L2 6"></path></svg><div><div>Email</div><a href="mailto:support@trbltours.com">support@trbltours.com</a></div></div>
                    <div class="footer-contact-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.33 1.78.62 2.62a2 2 0 0 1-.45 2.11L8 9.89a16 16 0 0 0 6 6l1.44-1.28a2 2 0 0 1 2.11-.45c.84.29 1.72.5 2.62.62A2 2 0 0 1 22 16.92z"></path></svg><div><div>Phone</div><a href="tel:+639123456789">+63 912 345 6789</a></div></div>
                    <div class="footer-contact-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg><div><div>Location</div><a href="{{ url('/contact') }}">Cebu, Philippines</a></div></div>
                </section>
                <section class="footer-col footer-purpose">
                    <h4>Travel with Purpose</h4>
                    <p>We support local communities and promote sustainable tourism across the Philippines.</p>
                </section>
            </div>

            <div class="footer-row">
                <p>Follow us: Facebook | Instagram | TikTok | YouTube</p>
                <p>Badges: DOT Accredited Platform | GCash and Maya Pay | Member: Philippine Tourism Board</p>
            </div>

            <div class="footer-row">
                <p>© 2025 Trbltours. All rights reserved.</p>
                <p>Trbltours is not a big corporation. We are a small team based in the Philippines, connecting travelers to local guides who deserve to be seen.</p>
                <p>Made with love and pride for every barrio, province, and island.</p>
            </div>

            <div class="footer-bottom-links">
                <a href="{{ url('/privacy-policy') }}">Privacy Policy</a> |
                <a href="{{ url('/terms-of-use') }}">Terms of Use</a> |
                <a href="{{ url('/report-a-guide') }}">Report a Guide</a> |
                <a href="{{ url('/contact-us') }}">Contact Us</a>
            </div>
        </div>
    </footer>

    <script>
        (() => {
            const initFadeCarousel = (containerId, itemSelector, intervalMs = 4000) => {
                const container = document.getElementById(containerId);
                if (!container) {
                    return;
                }

                const items = Array.from(container.querySelectorAll(itemSelector));
                if (!items.length) {
                    return;
                }

                let currentIndex = 0;
                let autoPlay = null;

                const setItem = (index) => {
                    currentIndex = (index + items.length) % items.length;
                    items.forEach((item, i) => {
                        item.classList.toggle('active', i === currentIndex);
                    });
                };

                const startAutoPlay = () => {
                    if (autoPlay !== null) {
                        window.clearInterval(autoPlay);
                    }
                    autoPlay = window.setInterval(() => {
                        setItem(currentIndex + 1);
                    }, intervalMs);
                };

                container.addEventListener('mouseenter', () => {
                    if (autoPlay !== null) {
                        window.clearInterval(autoPlay);
                    }
                });

                container.addEventListener('mouseleave', () => {
                    startAutoPlay();
                });

                setItem(0);
                startAutoPlay();
            };

            initFadeCarousel('heroCarousel', '.hero-slide', 4000);
            initFadeCarousel('favoritesCarousel', '.favorite-card', 1800);

            const locationTrigger = document.getElementById('locationTrigger');
            const accommodationInput = document.getElementById('accommodation');
            const placeSuggestions = document.getElementById('placeSuggestions');

            if (locationTrigger && accommodationInput && placeSuggestions) {
                const destinations = [
                    'El Nido, Palawan',
                    'Coron, Palawan',
                    'Siargao',
                    'Bohol',
                    'Cebu City',
                    'Banaue',
                    'Vigan',
                    'Boracay',
                    'Baguio',
                    'Davao City',
                    'Camiguin',
                    'Siquijor'
                ];

                const renderSuggestions = (query = '') => {
                    const normalizedQuery = query.trim().toLowerCase();
                    const results = destinations
                        .filter((destination) => destination.toLowerCase().includes(normalizedQuery))
                        .slice(0, 8);

                    placeSuggestions.innerHTML = '';

                    results.forEach((destination) => {
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.className = 'place-suggestion-item';
                        item.setAttribute('role', 'option');
                        item.setAttribute('data-place', destination);
                        item.textContent = destination;
                        placeSuggestions.appendChild(item);
                    });
                };

                const openSuggestions = () => {
                    placeSuggestions.classList.add('open');
                };

                const closeSuggestions = () => {
                    placeSuggestions.classList.remove('open');
                };

                accommodationInput.addEventListener('focus', () => {
                    renderSuggestions(accommodationInput.value);
                    openSuggestions();
                });

                accommodationInput.addEventListener('input', () => {
                    renderSuggestions(accommodationInput.value);
                    openSuggestions();
                });

                placeSuggestions.addEventListener('click', (event) => {
                    if (!(event.target instanceof Element)) {
                        return;
                    }

                    const suggestion = event.target.closest('[data-place]');
                    if (!suggestion) {
                        return;
                    }

                    const selectedPlace = suggestion.getAttribute('data-place');
                    if (!selectedPlace) {
                        return;
                    }

                    accommodationInput.value = selectedPlace;
                    closeSuggestions();
                    accommodationInput.focus();
                });

                document.addEventListener('click', (event) => {
                    if (!(event.target instanceof Element)) {
                        return;
                    }

                    if (!locationTrigger.contains(event.target)) {
                        closeSuggestions();
                    }
                });
            }

            const anytimeTrigger = document.getElementById('anytimeTrigger');
            const anytimeToggle = document.getElementById('anytimeToggle');
            const anytimeLabel = document.getElementById('anytimeLabel');
            const dateDropdown = document.getElementById('dateDropdown');
            const checkinDate = document.getElementById('checkinDate');
            const checkoutDate = document.getElementById('checkoutDate');
            const stayDuration = document.getElementById('stayDuration');

            if (anytimeTrigger && anytimeToggle && anytimeLabel && dateDropdown && checkinDate && checkoutDate && stayDuration) {
                const setDateOpenState = (isOpen) => {
                    dateDropdown.classList.toggle('open', isOpen);
                    anytimeToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                };

                const formatDate = (dateString) => {
                    const date = new Date(`${dateString}T00:00:00`);
                    return date.toLocaleDateString('en-US', {
                        month: 'short',
                        day: 'numeric',
                    });
                };

                const getNights = (startDate, endDate) => {
                    const start = new Date(`${startDate}T00:00:00`).getTime();
                    const end = new Date(`${endDate}T00:00:00`).getTime();
                    const millisecondsPerDay = 1000 * 60 * 60 * 24;
                    return Math.round((end - start) / millisecondsPerDay);
                };

                const updateDateSummary = () => {
                    const start = checkinDate.value;
                    const end = checkoutDate.value;

                    if (!start && !end) {
                        anytimeLabel.textContent = 'Anytime';
                        stayDuration.textContent = 'Duration: Select both dates';
                        return;
                    }

                    if (start && !end) {
                        anytimeLabel.textContent = formatDate(start);
                        stayDuration.textContent = 'Duration: Select check-out date';
                        return;
                    }

                    if (!start && end) {
                        anytimeLabel.textContent = formatDate(end);
                        stayDuration.textContent = 'Duration: Select check-in date';
                        return;
                    }

                    if (!start || !end) {
                        return;
                    }

                    const nights = getNights(start, end);
                    if (nights <= 0) {
                        stayDuration.textContent = 'Duration: Check-out must be after check-in';
                        anytimeLabel.textContent = `${formatDate(start)} - ${formatDate(end)}`;
                        return;
                    }

                    stayDuration.textContent = `Duration: ${nights} ${nights === 1 ? 'night' : 'nights'}`;
                    anytimeLabel.textContent = `${formatDate(start)} - ${formatDate(end)}`;
                };

                anytimeToggle.addEventListener('click', (event) => {
                    event.stopPropagation();
                    setDateOpenState(!dateDropdown.classList.contains('open'));
                });

                dateDropdown.addEventListener('click', (event) => {
                    event.stopPropagation();
                });

                checkinDate.addEventListener('change', () => {
                    if (checkoutDate.value && checkinDate.value && checkoutDate.value <= checkinDate.value) {
                        checkoutDate.value = '';
                    }
                    checkoutDate.min = checkinDate.value || '';
                    updateDateSummary();
                });

                checkoutDate.addEventListener('change', updateDateSummary);

                document.addEventListener('click', (event) => {
                    if (!(event.target instanceof Element)) {
                        return;
                    }

                    if (!anytimeTrigger.contains(event.target)) {
                        setDateOpenState(false);
                    }
                });
            }

            const guestsTrigger = document.getElementById('guestsTrigger');
            const guestsToggle = document.getElementById('guestsToggle');
            const guestsDropdown = document.getElementById('guestsDropdown');
            const guestsSummary = document.getElementById('guestsSummary');
            const guestsApply = document.getElementById('guestsApply');
            const adultsValueElement = document.getElementById('adultsValue');
            const childrenValueElement = document.getElementById('childrenValue');
            const adultsDecrement = document.getElementById('adultsDecrement');
            const adultsIncrement = document.getElementById('adultsIncrement');
            const childrenDecrement = document.getElementById('childrenDecrement');
            const childrenIncrement = document.getElementById('childrenIncrement');

            if (
                guestsTrigger &&
                guestsToggle &&
                guestsDropdown &&
                guestsSummary &&
                guestsApply &&
                adultsValueElement &&
                childrenValueElement &&
                adultsDecrement &&
                adultsIncrement &&
                childrenDecrement &&
                childrenIncrement
            ) {
                let adults = Number.parseInt(adultsValueElement.textContent ?? '2', 10) || 2;
                let children = Number.parseInt(childrenValueElement.textContent ?? '0', 10) || 0;

                const setOpenState = (isOpen) => {
                    guestsDropdown.classList.toggle('open', isOpen);
                    guestsToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                };

                const updateGuestSummary = () => {
                    const adultLabel = adults === 1 ? 'Adult' : 'Adults';
                    const childrenLabel = children === 1 ? 'Child' : 'Children';

                    if (children > 0) {
                        guestsSummary.textContent = `${adults} ${adultLabel}, ${children} ${childrenLabel}`;
                    } else {
                        guestsSummary.textContent = `${adults} ${adultLabel}`;
                    }
                };

                const syncGuestCounters = () => {
                    adultsValueElement.textContent = String(adults);
                    childrenValueElement.textContent = String(children);
                    adultsDecrement.disabled = adults <= 1;
                    childrenDecrement.disabled = children <= 0;
                };

                guestsToggle.addEventListener('click', (event) => {
                    event.stopPropagation();
                    setOpenState(!guestsDropdown.classList.contains('open'));
                });

                guestsDropdown.addEventListener('click', (event) => {
                    event.stopPropagation();
                });

                adultsIncrement.addEventListener('click', () => {
                    adults += 1;
                    syncGuestCounters();
                });

                adultsDecrement.addEventListener('click', () => {
                    if (adults > 1) {
                        adults -= 1;
                        syncGuestCounters();
                    }
                });

                childrenIncrement.addEventListener('click', () => {
                    children += 1;
                    syncGuestCounters();
                });

                childrenDecrement.addEventListener('click', () => {
                    if (children > 0) {
                        children -= 1;
                        syncGuestCounters();
                    }
                });

                guestsApply.addEventListener('click', () => {
                    updateGuestSummary();
                    setOpenState(false);
                });

                document.addEventListener('click', (event) => {
                    if (!(event.target instanceof Element)) {
                        return;
                    }

                    if (!guestsTrigger.contains(event.target)) {
                        setOpenState(false);
                    }
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') {
                        setOpenState(false);
                    }
                });

                syncGuestCounters();
                updateGuestSummary();
            }
        })();
    </script>
</body>

</html>
