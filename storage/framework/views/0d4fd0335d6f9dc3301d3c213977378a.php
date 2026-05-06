<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'TrblTours')); ?> | <?php echo e($tour->title ?? $tour->name ?? 'Tour Details'); ?></title>

    <link rel="icon" type="image/png" sizes="64x64" href="<?php echo e(asset('favicon.png')); ?>">
    <link rel="shortcut icon" href="<?php echo e(asset('favicon.ico')); ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Asimovian:wght@400;700&family=Cal+Sans&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?>

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

        .booking-field select,
        .booking-field input[type='date'] {
            min-height: 44px;
            border-radius: 12px;
            border: 1px solid rgba(111, 62, 44, 0.24);
            background: #fffcf5;
            color: var(--brown-800);
            font-size: 14px;
            padding: 10px 12px;
        }

        .booking-field input[type='date'][readonly] {
            cursor: pointer;
        }

        .booking-calendar {
            border: 1px solid rgba(111, 62, 44, 0.2);
            border-radius: 14px;
            background: #fffdf8;
            padding: 10px;
        }

        .booking-calendar-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .booking-calendar-head button {
            min-height: 32px;
            min-width: 32px;
            border-radius: 999px;
            border: 1px solid rgba(111, 62, 44, 0.2);
            background: #ffffff;
            color: var(--brown-800);
            font-weight: 700;
            cursor: pointer;
        }

        .booking-calendar-month {
            color: var(--brown-900);
            font-size: 14px;
            font-weight: 700;
        }

        .booking-calendar-weekdays {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 6px;
            margin-bottom: 6px;
        }

        .booking-calendar-weekday {
            text-align: center;
            color: #8a7561;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .booking-calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 6px;
        }

        .booking-calendar-blank {
            min-height: 54px;
        }

        .booking-calendar-day {
            min-height: 58px;
            border-radius: 10px;
            border: 1px solid rgba(111, 62, 44, 0.12);
            background: #ffffff;
            color: var(--brown-800);
            padding: 6px;
            text-align: left;
            cursor: pointer;
            display: grid;
            gap: 2px;
            align-content: space-between;
            overflow: hidden;
        }

        .booking-calendar-day-number {
            font-size: 13px;
            font-weight: 700;
            line-height: 1;
        }

        .booking-calendar-day-meta {
            font-size: 9px;
            line-height: 1.1;
            font-weight: 600;
            display: block;
            max-width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .booking-calendar-day.available {
            border-color: #b8d7a6;
            background: #f5fbf1;
        }

        .booking-calendar-day.limited_slots {
            border-color: #efd49d;
            background: #fff8eb;
        }

        .booking-calendar-day.fiesta {
            border-color: #e8b5aa;
            background: #fff2ef;
        }

        .booking-calendar-day.unavailable {
            border-color: #d7d7de;
            background: #f3f4f7;
            color: #9a9eab;
            cursor: not-allowed;
        }

        .booking-calendar-day.active {
            outline: 2px solid #8f9d59;
            outline-offset: 1px;
        }

        .availability-status {
            border-radius: 999px;
            padding: 1px 6px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .availability-status.available {
            background: #e9f4de;
            color: #3f6a2f;
        }

        .availability-status.limited_slots {
            background: #fff1d6;
            color: #92640f;
        }

        .availability-status.fiesta {
            background: #ffe3df;
            color: #9b3e2d;
        }

        .availability-status.fully_booked {
            background: #f1f2f6;
            color: #687188;
        }

        .availability-empty {
            margin-top: 6px;
            color: #8a7561;
            font-size: 12px;
        }

        .availability-empty.available {
            color: #3f6a2f;
        }

        .availability-empty.unavailable {
            color: #8d2d21;
        }

        .availability-help {
            margin-top: -4px;
            margin-bottom: 6px;
            color: #8a7561;
            font-size: 12px;
        }

        @media (max-width: 560px) {
            .booking-calendar-weekday {
                font-size: 10px;
            }

            .booking-calendar-blank,
            .booking-calendar-day {
                min-height: 44px;
            }

            .booking-calendar-day {
                padding: 4px;
            }

            .booking-calendar-day-number {
                font-size: 12px;
            }

            .booking-calendar-day-meta {
                display: none;
            }
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
    <?php
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

        $availabilityDates = collect($availabilityOptions ?? [])
            ->map(function (mixed $option): ?array {
                $dateValue = data_get($option, 'date');

                if ($dateValue instanceof \DateTimeInterface) {
                    $date = $dateValue->format('Y-m-d');
                } else {
                    $date = trim((string) $dateValue);
                }

                if ($date === '') {
                    return null;
                }

                $status = (string) data_get($option, 'status', 'available');

                $label = match ($status) {
                    'fully_booked' => 'Fully Booked',
                    'limited_slots' => 'Limited Slots',
                    'fiesta' => 'Fiesta',
                    default => 'Available',
                };

                $dateCarbon = \Illuminate\Support\Carbon::parse($date);

                return [
                    'date' => $date,
                    'display_date' => $dateCarbon->format('M d, Y'),
                    'status' => $status,
                    'status_label' => $label,
                    'special_price' => data_get($option, 'special_price'),
                ];
            })
            ->filter()
            ->values();

        $defaultBookingDate = old('booking_date', $availabilityDates->first()['date'] ?? now()->addDay()->toDateString());

        $sourceContext = in_array(($source ?? 'explore'), ['home', 'explore', 'dashboard'], true)
            ? $source
            : 'explore';

        $backHref = match ($sourceContext) {
            'dashboard' => route('dashboard.tourist'),
            'home' => url('/'),
            default => route('explore-tours'),
        };

        $backLabel = match ($sourceContext) {
            'dashboard' => 'Back',
            'home' => 'Back to Home',
            default => 'Back to Explore Tours',
        };
    ?>

    <header class="site-header">
        <div class="container header-inner">
            <a class="brand" href="<?php echo e(route('explore-tours')); ?>">
                <img class="brand-logo" src="<?php echo e(asset('images/tribaltours_icon.png')); ?>" alt="Trbltours">
                <span class="brand-name brand-font">TrblTours</span>
            </a>
        </div>
    </header>

    <main class="page">
        <section class="container">
            <a href="<?php echo e($backHref); ?>" class="back-link" aria-label="<?php echo e($backLabel); ?>">
                <i class="fa-solid fa-arrow-left"></i>
                <span><?php echo e($backLabel); ?></span>
            </a>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status')): ?>
                <div style="margin-bottom: 16px; border: 1px solid rgba(122, 135, 48, 0.35); background: #eef4e6; color: #3f5b2f; border-radius: 14px; padding: 12px 14px; font-size: 14px; font-weight: 600;">
                    <?php echo e(session('status')); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors instanceof \Illuminate\Support\ViewErrorBag && $errors->any()): ?>
                <div style="margin-bottom: 16px; border: 1px solid rgba(183, 80, 91, 0.35); background: #fff0f2; color: #7f2734; border-radius: 14px; padding: 12px 14px; font-size: 14px;">
                    <?php echo e($errors->first()); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="gallery-wrap">
                <div class="gallery-grid">
                    <div class="gallery-featured">
                        <img src="<?php echo e($featuredImage); ?>" alt="<?php echo e($title); ?> featured image">
                    </div>
                    <div class="gallery-side">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $supportingImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="gallery-small">
                                <img src="<?php echo e($image); ?>" alt="<?php echo e($title); ?> gallery image <?php echo e($loop->iteration); ?>">
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <div class="gallery-actions">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                        <form method="POST" action="<?php echo e(route('dashboard.likes.toggle', $tour)); ?>" data-like-form="true" data-like-ajax="true" data-liked="<?php echo e(($isTourLiked ?? false) ? 'true' : 'false'); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="gallery-icon-btn" data-like-button="true" aria-label="<?php echo e(($isTourLiked ?? false) ? 'Remove tour from likes' : 'Save tour to likes'); ?>" aria-pressed="<?php echo e(($isTourLiked ?? false) ? 'true' : 'false'); ?>">
                                <i data-like-icon="true" class="<?php echo e(($isTourLiked ?? false) ? 'fa-solid' : 'fa-regular'); ?> fa-heart"></i>
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="gallery-icon-btn" aria-label="Login to like this tour">
                            <i class="fa-regular fa-heart"></i>
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <button type="button" class="gallery-icon-btn" aria-label="Share tour">
                        <i class="fa-solid fa-share-nodes"></i>
                    </button>
                </div>
            </div>

            <header class="tour-header">
                <h1 class="hero-font"><?php echo e($title); ?></h1>
                <div class="tour-subheader">
                    <span>⭐ <?php echo e($rating); ?></span>
                    <a href="#reviews">(24 reviews)</a>
                </div>

                <div class="guide-bar">
                    <div class="guide-ident">
                        <img class="guide-avatar" src="<?php echo e($guideAvatar); ?>" alt="<?php echo e($guideName); ?>">
                        <div>
                            <p class="guide-name"><?php echo e($guideName); ?></p>
                            <p class="guide-role">Local Tour Guide</p>
                        </div>
                    </div>

                    <a href="<?php echo e(auth()->check() ? route('dashboard.messages') : route('login')); ?>" class="message-btn">Send a message</a>
                </div>
            </header>

            <div class="layout">
                <section>
                    <article class="panel">
                        <h2>Overview</h2>
                        <p class="overview-text"><?php echo e($description); ?></p>
                    </article>

                    <article class="panel">
                        <h2>Itinerary</h2>
                        <div class="timeline">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $itineraryItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="timeline-item">
                                    <div class="timeline-track">
                                        <span class="timeline-dot"></span>
                                        <span class="timeline-line"></span>
                                    </div>
                                    <p class="timeline-text"><?php echo e($item); ?></p>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </article>

                    <article class="panel" id="reviews">
                        <h2>What's Included</h2>
                        <div class="included-grid">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $inclusions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inclusion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="included-item">
                                    <i class="fa-solid <?php echo e($iconSet[($loop->index % count($iconSet))]); ?>"></i>
                                    <span><?php echo e($inclusion); ?></span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </article>
                </section>

                <aside class="booking-col">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->check() && auth()->user()?->role === 'tourist'): ?>
                        <form method="POST" action="<?php echo e(route('bookings.store')); ?>" class="booking-card">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="tour_id" value="<?php echo e($tour->id); ?>">

                            <p class="booking-price">₱ <?php echo e(number_format($price, 2)); ?></p>

                            <div class="booking-field">
                                <label for="bookingDate">Preferred Date</label>
                                <input id="bookingDate" name="booking_date" type="date" min="<?php echo e(now()->toDateString()); ?>"
                                    value="<?php echo e($defaultBookingDate); ?>" readonly required>
                                <p class="availability-help">Pick a day from the booking calendar. Enabled dates are bookable.</p>

                                <div class="booking-calendar" data-booking-calendar>
                                    <div class="booking-calendar-head">
                                        <button type="button" aria-label="Previous month" data-calendar-prev>&larr;</button>
                                        <p class="booking-calendar-month" data-calendar-month></p>
                                        <button type="button" aria-label="Next month" data-calendar-next>&rarr;</button>
                                    </div>

                                    <div class="booking-calendar-weekdays">
                                        <span class="booking-calendar-weekday">Sun</span>
                                        <span class="booking-calendar-weekday">Mon</span>
                                        <span class="booking-calendar-weekday">Tue</span>
                                        <span class="booking-calendar-weekday">Wed</span>
                                        <span class="booking-calendar-weekday">Thu</span>
                                        <span class="booking-calendar-weekday">Fri</span>
                                        <span class="booking-calendar-weekday">Sat</span>
                                    </div>

                                    <div class="booking-calendar-grid" data-calendar-grid></div>
                                </div>

                                <p class="availability-empty" data-calendar-note></p>

                                <div class="mt-1 flex flex-wrap gap-0.5 text-[8px]">
                                    <span class="availability-status available">Available</span>
                                    <span class="availability-status limited_slots">Limited Slots</span>
                                    <span class="availability-status fiesta">Fiesta</span>
                                    <span class="availability-status fully_booked">Unavailable</span>
                                </div>
                            </div>

                            <div class="booking-field">
                                <label for="groupSize">Party Size</label>
                                <select id="groupSize" name="group_size" required>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($size = 1; $size <= 10; $size++): ?>
                                        <option value="<?php echo e($size); ?>" <?php if((int) old('group_size', 1) === $size): echo 'selected'; endif; ?>><?php echo e($size); ?> <?php echo e($size === 1 ? 'Guest' : 'Guests'); ?></option>
                                    <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
                            </div>

                            <div class="booking-field">
                                <label for="specialRequests">Special Requests (Optional)</label>
                                <select id="specialRequests" name="special_requests">
                                    <option value="" <?php if(old('special_requests') === null || old('special_requests') === ''): echo 'selected'; endif; ?>>None</option>
                                    <option value="Need child-friendly stops" <?php if(old('special_requests') === 'Need child-friendly stops'): echo 'selected'; endif; ?>>Need child-friendly stops</option>
                                    <option value="Prefer a slower pace" <?php if(old('special_requests') === 'Prefer a slower pace'): echo 'selected'; endif; ?>>Prefer a slower pace</option>
                                    <option value="Need wheelchair-friendly route" <?php if(old('special_requests') === 'Need wheelchair-friendly route'): echo 'selected'; endif; ?>>Need wheelchair-friendly route</option>
                                </select>
                            </div>

                            <button type="submit" class="book-btn">Book Now</button>
                        </form>
                    <?php elseif(auth()->check()): ?>
                        <div class="booking-card">
                            <p class="booking-price">₱ <?php echo e(number_format($price, 2)); ?></p>
                            <p style="margin-top: 8px; font-size: 13px; color: #6f5d52;">Booking is available for tourist accounts.</p>
                        </div>
                    <?php else: ?>
                        <div class="booking-card">
                            <p class="booking-price">₱ <?php echo e(number_format($price, 2)); ?></p>
                            <a href="<?php echo e(route('login')); ?>" class="book-btn" style="text-decoration: none; display: inline-flex; justify-content: center; align-items: center;">Book Now</a>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </aside>
            </div>
        </section>
    </main>
    <script>
        (() => {
            const availabilityOptions = <?php echo json_encode($availabilityDates->all(), 15, 512) ?>;
            const hasGuideAvailabilityRules = <?php echo json_encode((bool) ($hasGuideAvailabilityRules ?? false), 15, 512) ?>;

            const bookingDateInput = document.getElementById('bookingDate');
            const calendarShell = document.querySelector('[data-booking-calendar]');
            const calendarGrid = document.querySelector('[data-calendar-grid]');
            const calendarMonthLabel = document.querySelector('[data-calendar-month]');
            const prevMonthButton = document.querySelector('[data-calendar-prev]');
            const nextMonthButton = document.querySelector('[data-calendar-next]');
            const calendarNote = document.querySelector('[data-calendar-note]');

            const dateFormatter = new Intl.DateTimeFormat('en-US', { month: 'long', year: 'numeric' });
            const availabilityMap = new Map(availabilityOptions.map((option) => [option.date, option]));
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            const toIsoDate = (date) => {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');

                return `${year}-${month}-${day}`;
            };

            const parseIsoDate = (isoDate) => {
                const [year, month, day] = isoDate.split('-').map(Number);

                return new Date(year, month - 1, day);
            };

            const describeStatus = (status) => {
                switch (status) {
                    case 'limited_slots':
                        return 'Limited Slots';
                    case 'fiesta':
                        return 'Fiesta';
                    case 'fully_booked':
                        return 'Unavailable';
                    default:
                        return 'Available';
                }
            };

            const compactStatusLabel = (status) => {
                switch (status) {
                    case 'limited_slots':
                        return 'Slots';
                    case 'fiesta':
                        return 'Fiesta';
                    case 'fully_booked':
                        return 'Full';
                    default:
                        return 'Avail';
                }
            };

            const getDayState = (date) => {
                const dateIso = toIsoDate(date);
                const option = availabilityMap.get(dateIso);
                const isPastDate = date < today;

                if (isPastDate) {
                    return {
                        enabled: false,
                        variant: 'unavailable',
                        label: 'Past',
                        status: 'fully_booked',
                        option: null,
                    };
                }

                if (option) {
                    const isUnavailable = option.status === 'fully_booked';

                    return {
                        enabled: !isUnavailable,
                        variant: isUnavailable ? 'unavailable' : option.status,
                        label: describeStatus(option.status),
                        status: option.status,
                        option,
                    };
                }

                if (hasGuideAvailabilityRules) {
                    return {
                        enabled: false,
                        variant: 'unavailable',
                        label: 'Unavailable',
                        status: 'fully_booked',
                        option: null,
                    };
                }

                return {
                    enabled: true,
                    variant: 'available',
                    label: 'Available',
                    status: 'available',
                    option: null,
                };
            };

            const updateCalendarNote = () => {
                if (!(bookingDateInput instanceof HTMLInputElement) || !(calendarNote instanceof HTMLElement)) {
                    return;
                }

                const selectedDate = bookingDateInput.value;
                const selectedOption = availabilityMap.get(selectedDate);

                calendarNote.classList.remove('available', 'unavailable');
                bookingDateInput.setCustomValidity('');

                if (selectedDate === '') {
                    calendarNote.textContent = 'Select an available date to continue booking.';

                    return;
                }

                if (selectedOption) {
                    const selectedStatus = selectedOption.status;
                    const statusLabel = describeStatus(selectedStatus);
                    const statusClass = selectedStatus === 'fully_booked' ? 'unavailable' : 'available';
                    const pricingText = selectedOption.special_price
                        ? ` Special rate: PHP ${Number(selectedOption.special_price).toFixed(2)} per guest.`
                        : '';

                    calendarNote.classList.add(statusClass);
                    calendarNote.textContent = `${selectedOption.display_date}: ${statusLabel}.${pricingText}`;

                    if (selectedStatus === 'fully_booked') {
                        bookingDateInput.setCustomValidity('Selected date is unavailable. Please choose a different date.');
                    }

                    return;
                }

                if (hasGuideAvailabilityRules) {
                    calendarNote.classList.add('unavailable');
                    calendarNote.textContent = 'Selected date is unavailable for this tour package.';
                    bookingDateInput.setCustomValidity('Selected date is unavailable for this tour package.');

                    return;
                }

                calendarNote.classList.add('available');
                calendarNote.textContent = 'Date is available for booking.';
            };

            let activeMonthDate = bookingDateInput instanceof HTMLInputElement && bookingDateInput.value
                ? parseIsoDate(bookingDateInput.value)
                : new Date(today);

            activeMonthDate = new Date(activeMonthDate.getFullYear(), activeMonthDate.getMonth(), 1);

            const renderCalendar = () => {
                if (!(calendarGrid instanceof HTMLElement) || !(calendarMonthLabel instanceof HTMLElement) || !(bookingDateInput instanceof HTMLInputElement)) {
                    return;
                }

                calendarMonthLabel.textContent = dateFormatter.format(activeMonthDate);
                calendarGrid.innerHTML = '';

                const monthStart = new Date(activeMonthDate.getFullYear(), activeMonthDate.getMonth(), 1);
                const monthEnd = new Date(activeMonthDate.getFullYear(), activeMonthDate.getMonth() + 1, 0);
                const daysInMonth = monthEnd.getDate();
                const firstWeekday = monthStart.getDay();

                for (let blankIndex = 0; blankIndex < firstWeekday; blankIndex++) {
                    const blankCell = document.createElement('div');
                    blankCell.className = 'booking-calendar-blank';
                    calendarGrid.appendChild(blankCell);
                }

                for (let dayNumber = 1; dayNumber <= daysInMonth; dayNumber++) {
                    const currentDate = new Date(activeMonthDate.getFullYear(), activeMonthDate.getMonth(), dayNumber);
                    const dateIso = toIsoDate(currentDate);
                    const dayState = getDayState(currentDate);

                    const dayButton = document.createElement('button');
                    dayButton.type = 'button';
                    dayButton.className = `booking-calendar-day ${dayState.variant}`;

                    if (bookingDateInput.value === dateIso) {
                        dayButton.classList.add('active');
                    }

                    if (!dayState.enabled) {
                        dayButton.disabled = true;
                    }

                    let metaText = dayState.label;
                    if (dayState.option?.special_price) {
                        metaText = `P${Number(dayState.option.special_price).toLocaleString('en-US', { maximumFractionDigits: 0 })}`;
                    } else {
                        metaText = compactStatusLabel(dayState.status);
                    }

                    dayButton.innerHTML = `<span class="booking-calendar-day-number">${dayNumber}</span><span class="booking-calendar-day-meta">${metaText}</span>`;
                    dayButton.title = `${dateIso} - ${dayState.label}`;

                    if (dayState.enabled) {
                        dayButton.addEventListener('click', () => {
                            bookingDateInput.value = dateIso;
                            updateCalendarNote();
                            renderCalendar();
                        });
                    }

                    calendarGrid.appendChild(dayButton);
                }
            };

            if (prevMonthButton instanceof HTMLButtonElement) {
                prevMonthButton.addEventListener('click', () => {
                    activeMonthDate = new Date(activeMonthDate.getFullYear(), activeMonthDate.getMonth() - 1, 1);
                    renderCalendar();
                });
            }

            if (nextMonthButton instanceof HTMLButtonElement) {
                nextMonthButton.addEventListener('click', () => {
                    activeMonthDate = new Date(activeMonthDate.getFullYear(), activeMonthDate.getMonth() + 1, 1);
                    renderCalendar();
                });
            }

            if (bookingDateInput instanceof HTMLInputElement) {
                bookingDateInput.addEventListener('change', () => {
                    if (bookingDateInput.value) {
                        const parsedDate = parseIsoDate(bookingDateInput.value);
                        activeMonthDate = new Date(parsedDate.getFullYear(), parsedDate.getMonth(), 1);
                    }

                    updateCalendarNote();
                    renderCalendar();
                });
            }

            if (calendarShell instanceof HTMLElement) {
                updateCalendarNote();
                renderCalendar();
            }

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

</html><?php /**PATH /home/mistah-regime/tribaltours/resources/views/tours/show.blade.php ENDPATH**/ ?>