<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TrblTours') }} | {{ $tour->title ?? $tour->name ?? 'Tour Details' }}</title>

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
            --radius-xl: 22px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Instrument Sans', sans-serif;
            color: var(--brown-800);
            background: var(--sand-100);
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
        }

        .header-inner {
            min-height: 82px;
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

        .page {
            padding: 26px 0 72px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 40px;
            border-radius: 999px;
            border: 1px solid rgba(111, 62, 44, 0.2);
            background: var(--white);
            color: var(--brown-900);
            padding: 0 14px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            box-shadow: var(--shadow-md);
            margin-bottom: 16px;
        }

        .back-link:hover {
            border-color: var(--olive);
            color: var(--olive);
        }

        .gallery-wrap {
            position: relative;
            border-radius: var(--radius-xl);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .gallery-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: minmax(0, 1.65fr) minmax(0, 1fr);
            background: #f2e7cf;
            padding: 12px;
            border-radius: var(--radius-xl);
        }

        .gallery-featured,
        .gallery-small {
            border-radius: var(--radius-lg);
            overflow: hidden;
            position: relative;
        }

        .gallery-featured img,
        .gallery-small img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .gallery-featured {
            min-height: 360px;
        }

        .gallery-side {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .gallery-small {
            min-height: 174px;
        }

        .gallery-actions {
            position: absolute;
            top: 20px;
            right: 20px;
            display: inline-flex;
            gap: 8px;
            z-index: 3;
        }

        .gallery-icon-btn {
            height: 40px;
            width: 40px;
            border-radius: 999px;
            border: 1px solid rgba(111, 62, 44, 0.24);
            background: rgba(255, 255, 255, 0.9);
            color: var(--brown-900);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            box-shadow: var(--shadow-md);
        }

        .tour-header h1 {
            color: var(--brown-900);
            font-size: clamp(32px, 4.5vw, 44px);
            line-height: 1.1;
            margin-bottom: 8px;
        }

        .tour-subheader {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--brown-700);
            font-size: 15px;
            margin-bottom: 14px;
        }

        .tour-subheader a {
            color: var(--accent);
            font-weight: 600;
            text-decoration: none;
        }

        .guide-bar {
            border-radius: var(--radius-xl);
            background: var(--white);
            border: 1px solid rgba(111, 62, 44, 0.16);
            box-shadow: var(--shadow-md);
            padding: 10px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 20px;
        }

        .guide-ident {
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .guide-avatar {
            width: 42px;
            height: 42px;
            border-radius: 999px;
            object-fit: cover;
            border: 2px solid var(--sand-200);
        }

        .guide-name {
            color: var(--brown-900);
            font-weight: 700;
            line-height: 1.2;
        }

        .guide-role {
            color: var(--brown-700);
            font-size: 13px;
        }

        .message-btn {
            border-radius: 999px;
            border: 1px solid var(--olive);
            background: var(--olive);
            color: var(--white);
            font-size: 14px;
            font-weight: 700;
            padding: 10px 14px;
            text-decoration: none;
        }

        .layout {
            display: grid;
            gap: 22px;
            grid-template-columns: minmax(0, 1.85fr) minmax(300px, 1fr);
            align-items: start;
        }

        .panel {
            border-radius: var(--radius-xl);
            background: var(--white);
            border: 1px solid rgba(111, 62, 44, 0.14);
            box-shadow: var(--shadow-md);
            padding: 20px;
            margin-bottom: 16px;
        }

        .panel h2 {
            color: var(--brown-900);
            font-size: 24px;
            margin-bottom: 10px;
        }

        .overview-text {
            color: var(--brown-700);
            font-size: 15px;
            line-height: 1.7;
        }

        .timeline {
            display: grid;
            gap: 14px;
        }

        .timeline-item {
            display: grid;
            grid-template-columns: 20px 1fr;
            gap: 10px;
            align-items: start;
        }

        .timeline-track {
            position: relative;
            display: flex;
            justify-content: center;
            min-height: 100%;
        }

        .timeline-dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: var(--sand-300);
            border: 2px solid #fff;
            box-shadow: 0 0 0 1px rgba(111, 62, 44, 0.15);
            margin-top: 7px;
        }

        .timeline-line {
            position: absolute;
            top: 20px;
            bottom: -15px;
            width: 2px;
            background: var(--sand-300);
        }

        .timeline-item:last-child .timeline-line {
            display: none;
        }

        .timeline-text {
            color: var(--brown-700);
            font-size: 14px;
            line-height: 1.6;
            padding-bottom: 6px;
        }

        .included-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .included-item {
            border-radius: 14px;
            background: #faf5e8;
            border: 1px solid rgba(111, 62, 44, 0.12);
            padding: 10px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--brown-700);
        }

        .included-item i {
            color: var(--accent);
            width: 16px;
            text-align: center;
        }

        .booking-col {
            position: sticky;
            top: 98px;
        }

        .booking-card {
            border-radius: var(--radius-xl);
            background: var(--white);
            box-shadow: var(--shadow-lg);
            border: 1px solid rgba(111, 62, 44, 0.14);
            padding: 18px;
        }

        .booking-price {
            color: var(--brown-900);
            font-size: 30px;
            font-weight: 800;
            margin-bottom: 12px;
        }

        .booking-field {
            display: grid;
            gap: 6px;
            margin-bottom: 12px;
        }

        .booking-field label {
            color: var(--brown-700);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .booking-field select {
            min-height: 44px;
            border-radius: 12px;
            border: 1px solid rgba(111, 62, 44, 0.24);
            background: #fffcf5;
            color: var(--brown-800);
            font-size: 14px;
            padding: 10px 12px;
        }

        .book-btn {
            width: 100%;
            min-height: 48px;
            border-radius: 14px;
            border: 1px solid var(--olive);
            background: var(--olive);
            color: var(--white);
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
        }

        @media (max-width: 980px) {
            .gallery-grid {
                grid-template-columns: 1fr;
            }

            .gallery-featured {
                min-height: 280px;
            }

            .layout {
                grid-template-columns: 1fr;
            }

            .booking-col {
                position: static;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
    @php
        $guideName = $tour->marketplaceGuide->full_name ?? $tour->marketplaceGuide->name ?? 'Local Guide';
        $guideAvatar = $tour->marketplaceGuide->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($guideName).'&background=f5e8cc&color=3f2d22';

        $candidateImages = array_filter([
            $tour->image_url ?? null,
            $tour->featured_image ?? null,
            $tour->image_path ?? null,
            ...((array) ($tour->gallery_images ?? [])),
            'hero/elnido.jpg',
            'hero/palawan.jpg',
            'hero/batad.jpg',
            'hero/davao.jpg',
            'hero/pangasinan.jpg',
        ]);

        $normalizeImage = function (string $path): string {
            return str_starts_with($path, 'http://') || str_starts_with($path, 'https://')
                ? $path
                : asset(ltrim($path, '/'));
        };

        $images = collect($candidateImages)->map(fn ($img) => $normalizeImage((string) $img))->unique()->values();
        $featuredImage = $images->first();
        $supportingImages = $images->slice(1, 4)->pad(4, $featuredImage)->values();

        $itineraryItems = collect(preg_split('/\r\n|\r|\n/', (string) ($tour->full_itinerary ?? '')))
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->take(5);

        if ($itineraryItems->isEmpty()) {
            $itineraryItems = collect([
                'Meet your guide and quick orientation at the agreed pick-up point.',
                'Travel to the first destination with local context and insider stories.',
                'Guided activity and flexible free-time for photos and exploration.',
                'Local lunch break and hidden-spot recommendations from your guide.',
                'Sunset wrap-up and return transfer.',
            ]);
        }

        $inclusions = collect((array) ($tour->inclusions ?? []))->filter()->values()->take(6);

        if ($inclusions->isEmpty()) {
            $inclusions = collect([
                'Guide fee',
                'Local transport',
                'Entrance tickets',
                'Safety support',
                'Water refill',
                'Photo assistance',
            ]);
        }

        $iconSet = ['fa-map-location-dot', 'fa-van-shuttle', 'fa-ticket', 'fa-shield-heart', 'fa-bottle-water', 'fa-camera-retro'];

        $price = (float) ($tour->price_per_person ?? $tour->price ?? $tour->base_price ?? 0);
        $rating = number_format((float) ($tour->rating ?? 4.9), 1);
        $description = trim((string) ($tour->description ?? $tour->summary ?? 'No description available yet.'));
        $title = $tour->title ?? $tour->name ?? 'Tour Details';

        $sourceContext = in_array(($source ?? 'explore'), ['home', 'explore', 'dashboard'], true)
            ? $source
            : 'explore';

        $backHref = match ($sourceContext) {
            'dashboard' => route('dashboard.tourist'),
            'home' => url('/'),
            default => route('explore-tours'),
        };

        $backLabel = match ($sourceContext) {
            'dashboard' => 'Back to Tourist Dashboard',
            'home' => 'Back to Home',
            default => 'Back to Explore Tours',
        };
    @endphp

    <header class="site-header">
        <div class="container header-inner">
            <a class="brand" href="{{ route('explore-tours') }}">
                <img class="brand-logo" src="{{ asset('images/tribaltours_icon.png') }}" alt="Trbltours">
                <span class="brand-name brand-font">TrblTours</span>
            </a>
        </div>
    </header>

    <main class="page">
        <section class="container">
            <a href="{{ $backHref }}" class="back-link" aria-label="{{ $backLabel }}">
                <i class="fa-solid fa-arrow-left"></i>
                <span>{{ $backLabel }}</span>
            </a>

            @if (session('status'))
                <div style="margin-bottom: 16px; border: 1px solid rgba(122, 135, 48, 0.35); background: #eef4e6; color: #3f5b2f; border-radius: 14px; padding: 12px 14px; font-size: 14px; font-weight: 600;">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors instanceof \Illuminate\Support\ViewErrorBag && $errors->any())
                <div style="margin-bottom: 16px; border: 1px solid rgba(183, 80, 91, 0.35); background: #fff0f2; color: #7f2734; border-radius: 14px; padding: 12px 14px; font-size: 14px;">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="gallery-wrap">
                <div class="gallery-grid">
                    <div class="gallery-featured">
                        <img src="{{ $featuredImage }}" alt="{{ $title }} featured image">
                    </div>
                    <div class="gallery-side">
                        @foreach($supportingImages as $image)
                            <div class="gallery-small">
                                <img src="{{ $image }}" alt="{{ $title }} gallery image {{ $loop->iteration }}">
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="gallery-actions">
                    @auth
                        <form method="POST" action="{{ route('dashboard.likes.toggle', $tour) }}" data-like-form="true" data-like-ajax="true" data-liked="{{ ($isTourLiked ?? false) ? 'true' : 'false' }}">
                            @csrf
                            <button type="submit" class="gallery-icon-btn" data-like-button="true" aria-label="{{ ($isTourLiked ?? false) ? 'Remove tour from likes' : 'Save tour to likes' }}" aria-pressed="{{ ($isTourLiked ?? false) ? 'true' : 'false' }}">
                                <i data-like-icon="true" class="{{ ($isTourLiked ?? false) ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="gallery-icon-btn" aria-label="Login to like this tour">
                            <i class="fa-regular fa-heart"></i>
                        </a>
                    @endauth
                    <button type="button" class="gallery-icon-btn" aria-label="Share tour">
                        <i class="fa-solid fa-share-nodes"></i>
                    </button>
                </div>
            </div>

            <header class="tour-header">
                <h1 class="hero-font">{{ $title }}</h1>
                <div class="tour-subheader">
                    <span>⭐ {{ $rating }}</span>
                    <a href="#reviews">(24 reviews)</a>
                </div>

                <div class="guide-bar">
                    <div class="guide-ident">
                        <img class="guide-avatar" src="{{ $guideAvatar }}" alt="{{ $guideName }}">
                        <div>
                            <p class="guide-name">{{ $guideName }}</p>
                            <p class="guide-role">Local Tour Guide</p>
                        </div>
                    </div>

                    <a href="{{ auth()->check() ? route('dashboard.messages') : route('login') }}" class="message-btn">Send a message</a>
                </div>
            </header>

            <div class="layout">
                <section>
                    <article class="panel">
                        <h2>Overview</h2>
                        <p class="overview-text">{{ $description }}</p>
                    </article>

                    <article class="panel">
                        <h2>Itinerary</h2>
                        <div class="timeline">
                            @foreach($itineraryItems as $item)
                                <div class="timeline-item">
                                    <div class="timeline-track">
                                        <span class="timeline-dot"></span>
                                        <span class="timeline-line"></span>
                                    </div>
                                    <p class="timeline-text">{{ $item }}</p>
                                </div>
                            @endforeach
                        </div>
                    </article>

                    <article class="panel" id="reviews">
                        <h2>What's Included</h2>
                        <div class="included-grid">
                            @foreach($inclusions as $inclusion)
                                <div class="included-item">
                                    <i class="fa-solid {{ $iconSet[($loop->index % count($iconSet))] }}"></i>
                                    <span>{{ $inclusion }}</span>
                                </div>
                            @endforeach
                        </div>
                    </article>
                </section>

                <aside class="booking-col">
                    @if (auth()->check() && auth()->user()?->role === 'tourist')
                        <form method="POST" action="{{ route('bookings.store') }}" class="booking-card">
                            @csrf
                            <input type="hidden" name="tour_id" value="{{ $tour->id }}">

                            <p class="booking-price">₱ {{ number_format($price, 2) }}</p>

                            <div class="booking-field">
                                <label for="bookingDate">Tour Date</label>
                                <select id="bookingDate" name="booking_date" required>
                                    @php
                                        $availableDate = \Illuminate\Support\Carbon::parse($tour->available_on ?? now()->addDays(3))->toDateString();
                                        $fallbackDates = [
                                            now()->addDays(3)->toDateString(),
                                            now()->addDays(7)->toDateString(),
                                            now()->addDays(14)->toDateString(),
                                        ];
                                        $dateOptions = collect([$availableDate, ...$fallbackDates])->unique()->values();
                                    @endphp
                                    @foreach($dateOptions as $dateOption)
                                        <option value="{{ $dateOption }}" @selected(old('booking_date') === $dateOption)>
                                            {{ \Illuminate\Support\Carbon::parse($dateOption)->format('M d, Y') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="booking-field">
                                <label for="groupSize">Party Size</label>
                                <select id="groupSize" name="group_size" required>
                                    @for($size = 1; $size <= 10; $size++)
                                        <option value="{{ $size }}" @selected((int) old('group_size', 1) === $size)>{{ $size }} {{ $size === 1 ? 'Guest' : 'Guests' }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="booking-field">
                                <label for="specialRequests">Special Requests (Optional)</label>
                                <select id="specialRequests" name="special_requests">
                                    <option value="" @selected(old('special_requests') === null || old('special_requests') === '')>None</option>
                                    <option value="Need child-friendly stops" @selected(old('special_requests') === 'Need child-friendly stops')>Need child-friendly stops</option>
                                    <option value="Prefer a slower pace" @selected(old('special_requests') === 'Prefer a slower pace')>Prefer a slower pace</option>
                                    <option value="Need wheelchair-friendly route" @selected(old('special_requests') === 'Need wheelchair-friendly route')>Need wheelchair-friendly route</option>
                                </select>
                            </div>

                            <button type="submit" class="book-btn">Book Now</button>
                        </form>
                    @elseif (auth()->check())
                        <div class="booking-card">
                            <p class="booking-price">₱ {{ number_format($price, 2) }}</p>
                            <p style="margin-top: 8px; font-size: 13px; color: #6f5d52;">Booking is available for tourist accounts.</p>
                        </div>
                    @else
                        <div class="booking-card">
                            <p class="booking-price">₱ {{ number_format($price, 2) }}</p>
                            <a href="{{ route('login') }}" class="book-btn" style="text-decoration: none; display: inline-flex; justify-content: center; align-items: center;">Book Now</a>
                        </div>
                    @endif
                </aside>
            </div>
        </section>
    </main>
    <script>
        (() => {
            document.addEventListener('submit', async (event) => {
                const form = event.target;

                if (!(form instanceof HTMLFormElement)) {
                    return;
                }

                if (form.dataset.likeForm !== 'true' || form.dataset.likeAjax !== 'true') {
                    return;
                }

                event.preventDefault();

                if (form.dataset.loading === 'true') {
                    return;
                }

                form.dataset.loading = 'true';

                const button = form.querySelector('[data-like-button="true"]');
                const icon = form.querySelector('[data-like-icon="true"]');

                if (button instanceof HTMLButtonElement) {
                    button.disabled = true;
                }

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: new FormData(form),
                        credentials: 'same-origin',
                    });

                    if (!response.ok) {
                        throw new Error('Like toggle failed.');
                    }

                    const payload = await response.json();
                    const liked = Boolean(payload.liked);

                    form.dataset.liked = liked ? 'true' : 'false';

                    if (button instanceof HTMLButtonElement) {
                        button.setAttribute('aria-pressed', liked ? 'true' : 'false');
                        button.setAttribute('aria-label', liked ? 'Remove tour from likes' : 'Save tour to likes');
                    }

                    if (icon instanceof HTMLElement) {
                        icon.classList.toggle('fa-solid', liked);
                        icon.classList.toggle('fa-regular', !liked);
                    }
                } catch (error) {
                    form.submit();
                } finally {
                    form.dataset.loading = 'false';

                    if (button instanceof HTMLButtonElement) {
                        button.disabled = false;
                    }
                }
            });
        })();
    </script>
</body>

</html>