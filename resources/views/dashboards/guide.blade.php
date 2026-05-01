<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TrblTours - Guide Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600;700;800&family=Asimovian:wght@400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <style>
        :root {
            --cream-100: #f8f2e6;
            --cream-200: #f5efe2;
            --cream-300: #efe4d0;
            --brown-700: #6f5d52;
            --brown-800: #5a4a39;
            --brown-900: #3f2d22;
            --forest-500: #5f8a46;
            --sand-500: #d4a563;
            --radius-lg: 16px;
            --radius-xl: 24px;
        }

        [x-cloak] {
            display: none !important;
        }

        .nav-shell {
            background: rgba(247, 241, 228, 0.93);
            backdrop-filter: blur(10px);
            transition: all 0.2s ease;
        }

        .nav-shell.scrolled {
            background: rgba(212, 165, 99, 0.8);
            box-shadow: 0 10px 26px -18px rgba(61, 47, 31, 0.4);
        }

        .top-nav-link {
            color: #3f2d22;
            font-size: 15px;
            font-weight: 700;
            line-height: 1;
            letter-spacing: 0.2px;
            transition: color 0.2s ease;
        }

        .top-nav-link:hover {
            color: var(--forest-500);
        }

        .top-nav-link.active {
            color: #35593d;
        }

        .icon-button {
            height: 30px;
            width: 35px;
            border-radius: 9999px;
            border: 1px solid #e3dbc9;
            background: #fffdf8;
            color: #746a5b;
            font-size: 11px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .icon-button:hover {
            border-color: var(--sand-500);
            color: var(--forest-500);
            background: #ffffff;
        }

        .request-shell {
            border-radius: 18px;
            border: 1px solid #ddceb3;
            background: #fffdf8;
            box-shadow: 0 16px 35px -30px rgba(63, 45, 34, 0.75);
        }

        .listing-card {
            border-radius: 18px;
            border: 1px solid #e7dcc8;
            background: #f8f5ef;
            box-shadow: 0 8px 22px rgba(52, 41, 26, 0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .listing-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(52, 41, 26, 0.12);
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 0.2rem 0.65rem;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.04em;
        }
    </style>
</head>

<body class="min-h-screen bg-[#f5efe2] font-inter text-[#3e2a1f]">
    @php
        $activeTab = request()->string('tab')->toString() === 'listings' ? 'listings' : 'requests';
        $currentGuide = auth()->user();
        $guideDisplayName = $currentGuide?->full_name ?: $currentGuide?->name ?: 'Certified Local Guide';
        $guideBioDefault = $currentGuide?->bio ? \Illuminate\Support\Str::limit((string) $currentGuide->bio, 120) : 'Certified Local Guide';
        $guideAvatarUrl = $currentGuide?->profile_photo_path ? asset('storage/'.$currentGuide->profile_photo_path) : null;
        $regionOptions = [
            'NCR',
            'CAR',
            'Region I - Ilocos',
            'Region II - Cagayan Valley',
            'Region III - Central Luzon',
            'Region IV-A - Calabarzon',
            'Region IV-B - Mimaropa',
            'Region V - Bicol',
            'Region VI - Western Visayas',
            'Region VII - Central Visayas',
            'Region VIII - Eastern Visayas',
            'Region IX - Zamboanga Peninsula',
            'Region X - Northern Mindanao',
            'Region XI - Davao Region',
            'Region XII - Soccsksargen',
            'Region XIII - Caraga',
            'BARMM',
        ];
        $cityOptions = [
            'El Nido, Palawan',
            'Coron, Palawan',
            'Puerto Princesa, Palawan',
            'Cebu City, Cebu',
            'Bantayan, Cebu',
            'Moalboal, Cebu',
            'Siargao, Surigao del Norte',
            'General Luna, Siargao',
            'Bohol, Central Visayas',
            'Panglao, Bohol',
            'Davao City, Davao del Sur',
            'Samal Island, Davao del Norte',
            'Baguio City, Benguet',
            'Sagada, Mountain Province',
            'Vigan, Ilocos Sur',
            'Tagaytay, Cavite',
            'Batangas, Calabarzon',
            'Camiguin, Northern Mindanao',
        ];
    @endphp

    <div
        x-data="guideListingBuilder({
            initialTab: @js($activeTab),
            guideName: @js($guideDisplayName),
            guideBio: @js($guideBioDefault),
            guideAvatarUrl: @js($guideAvatarUrl),
            regionOptions: @js($regionOptions),
            cityOptions: @js($cityOptions)
        })"
        x-init="init()"
    >
        <header :class="scrolled ? 'nav-shell scrolled' : 'nav-shell'" class="fixed inset-x-0 top-0 z-50 border-b border-[#ddd2bd]">
            <div class="mx-auto flex max-w-[1260px] items-center justify-between px-6 py-4">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                    <img src="{{ asset('images/tribaltours_icon.png') }}" alt="TrblTours Icon" class="h-9 w-9 rounded-full border border-[#d3c5aa]/70 bg-[#f7efdd] object-cover">
                    <span class="font-[Asimovian] text-[26px] font-bold leading-none tracking-wide text-[#5a4a39]">TrblTours</span>
                </a>

                <nav class="hidden items-center gap-10 md:flex">
                    <button type="button" @click="activeTab = 'requests'" :class="activeTab === 'requests' ? 'top-nav-link active' : 'top-nav-link'">Request Posts</button>
                    <button type="button" @click="activeTab = 'listings'" :class="activeTab === 'listings' ? 'top-nav-link active' : 'top-nav-link'">My Listings</button>
                </nav>

                <div class="relative flex items-center gap-2" @click.outside="menuOpen = false">

                    <livewire:tourist-notifications />

                    <button type="button" @click="menuOpen = !menuOpen" class="inline-flex items-center gap-1 rounded-full border border-[#e3dbc9] bg-[#fffdf8] px-1.5 py-1.5 text-[#746a5b] transition hover:border-[#d4a563]">
                        <span class="icon-button border-5 bg-[#f5efe1] text-[16px]">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <i class="fa-solid fa-chevron-down text-[12px]"></i>
                    </button>

                    <div x-cloak x-show="menuOpen" x-transition class="absolute right-0 top-full mt-2 w-56 overflow-hidden rounded-2xl border border-[#d8c7a9] bg-[#fffaf2] py-2 shadow-2xl">
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-[#5a3c2a] transition hover:bg-[#f7ecd7]">
                            <i class="fa-solid fa-user w-4 text-center"></i>
                            <span>Profile</span>
                        </a>
                        <a href="{{ route('dashboard.guide') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-[#5a3c2a] transition hover:bg-[#f7ecd7]">
                            <i class="fa-solid fa-compass w-4 text-center"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('dashboard.messages') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-[#5a3c2a] transition hover:bg-[#f7ecd7]">
                            <i class="fa-solid fa-envelope w-4 text-center"></i>
                            <span>Messages</span>
                        </a>
                        <a href="{{ route('settings.edit') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-[#5a3c2a] transition hover:bg-[#f7ecd7]">
                            <i class="fa-solid fa-gear w-4 text-center"></i>
                            <span>Settings</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-2 px-4 py-2.5 text-left text-sm text-[#5a3c2a] transition hover:bg-[#f7ecd7]">
                                <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-[1260px] px-4 pb-28 pt-[118px] sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-5 rounded-2xl border border-[#bcd9b8] bg-[#edf8ec] px-4 py-3 text-sm font-semibold text-[#2e5b2a]">
                    {{ session('status') }}
                </div>
            @endif

            @php
                $firstValidationError = null;
                $sessionErrors = session('errors');

                if (is_object($sessionErrors) && method_exists($sessionErrors, 'first')) {
                    $firstValidationError = $sessionErrors->first();
                }
            @endphp

            @if (is_string($firstValidationError) && $firstValidationError !== '')
                <div class="mb-5 rounded-2xl border border-[#f2b8b1] bg-[#fdeeed] px-4 py-3 text-sm font-semibold text-[#8d2d21]">
                    {{ $firstValidationError }}
                </div>
            @endif

            <section x-cloak x-show="activeTab === 'requests'" class="space-y-4">

                @if ($requestPosts->isEmpty())
                    <div class="rounded-2xl border border-dashed border-[#d8c9af] bg-[#fff8ec] p-8 text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-[#efe2cb] text-[#7a654f]">
                            <i class="fa-solid fa-map-location-dot text-xl"></i>
                        </div>
                        <h2 class="mt-4 text-xl font-bold text-[#3f2d22]">No request posts yet</h2>
                        <p class="mx-auto mt-2 max-w-xl text-sm text-[#6f5d52]">Tourist requests will appear here in real-time once posted.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($requestPosts as $post)
                            @php
                                $rawInterests = (string) ($post->interests ?? '');
                                $decodedInterests = json_decode($rawInterests, true);

                                $interestList = is_array($decodedInterests)
                                    ? collect($decodedInterests)
                                        ->map(fn ($item) => trim((string) $item))
                                        ->filter(fn ($item) => $item !== '')
                                        ->values()
                                    : collect(explode(',', $rawInterests))
                                        ->map(fn ($item) => trim((string) $item))
                                        ->filter(fn ($item) => $item !== '')
                                        ->values();

                                $touristName = $post->tourist?->full_name ?: $post->tourist?->name ?: 'Tourist';
                                $touristAvatar = $post->tourist?->profile_photo_path ? asset('storage/'.$post->tourist->profile_photo_path) : null;
                                $preferredDate = $post->preferred_date?->format('M d, Y') ?: 'Flexible';
                            @endphp

                            <article id="request-{{ $post->id }}" class="request-shell overflow-hidden" x-data="{ openComments: false }">
                                <div class="p-5 sm:p-6">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div class="flex items-start gap-3">
                                            @if ($touristAvatar)
                                                <img src="{{ $touristAvatar }}" alt="{{ $touristName }}" class="h-10 w-10 rounded-full border border-[#d7c8ad] object-cover">
                                            @else
                                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-[#d7c8ad] bg-[#f4e7d1] text-xs font-bold text-[#5f4a36]">
                                                    {{ strtoupper(substr($touristName, 0, 1)) }}
                                                </span>
                                            @endif
                                            <div>
                                                <p class="text-sm font-semibold uppercase tracking-[0.08em] text-[#7f6b57]">{{ $touristName }}</p>
                                                <h2 class="mt-1 text-xl font-bold text-[#3f2d22]">{{ $post->title ?: 'Untitled Request' }}</h2>
                                                <p class="mt-1 text-sm text-[#726051]">{{ $post->location ?: 'Philippines' }} @if($post->region) • {{ $post->region }} @endif</p>
                                            </div>
                                        </div>

                                        <span class="status-pill {{ strtolower((string) $post->status) === 'open' ? 'bg-[#edf4e6] text-[#385032]' : 'bg-[#f1e6d4] text-[#6a5644]' }}">
                                            {{ ucfirst(str_replace('_', ' ', (string) ($post->status ?: 'open'))) }}
                                        </span>
                                    </div>

                                    <dl class="mt-4 grid grid-cols-2 gap-3 text-sm sm:grid-cols-4">
                                        <div class="rounded-xl border border-[#e8dbc3] bg-[#fff8ec] p-2.5">
                                            <dt class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[#7f6b57]">Duration</dt>
                                            <dd class="mt-1 font-semibold text-[#4f3d31]">{{ $post->duration ?: 'Flexible' }}</dd>
                                        </div>
                                        <div class="rounded-xl border border-[#e8dbc3] bg-[#fff8ec] p-2.5">
                                            <dt class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[#7f6b57]">Budget</dt>
                                            <dd class="mt-1 font-semibold text-[#4f3d31]">₱ {{ number_format((float) ($post->budget_max ?? $post->budget_min ?? 0), 0) }}</dd>
                                        </div>
                                        <div class="rounded-xl border border-[#e8dbc3] bg-[#fff8ec] p-2.5">
                                            <dt class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[#7f6b57]">Travelers</dt>
                                            <dd class="mt-1 font-semibold text-[#4f3d31]">{{ (int) ($post->adults ?? 0) }} Adults / {{ (int) ($post->children ?? 0) }} Children</dd>
                                        </div>
                                        <div class="rounded-xl border border-[#e8dbc3] bg-[#fff8ec] p-2.5">
                                            <dt class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[#7f6b57]">Preferred Date</dt>
                                            <dd class="mt-1 font-semibold text-[#4f3d31]">{{ $preferredDate }}</dd>
                                        </div>
                                    </dl>

                                    <div class="mt-4">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[#7f6b57]">Interests</p>
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            @forelse ($interestList as $interest)
                                                <span class="rounded-full border border-[#d8c8ac] bg-[#f8eedc] px-2.5 py-1 text-xs font-semibold text-[#5f4d3f]">{{ $interest }}</span>
                                            @empty
                                                <span class="text-xs text-[#8b7763]">No tags</span>
                                            @endforelse
                                        </div>
                                    </div>

                                    <p class="mt-4 text-sm text-[#6f5d52]">{{ $post->description ?: $post->message }}</p>

                                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                                        <p class="text-xs text-[#8a7662]">Posted: {{ $post->created_at?->format('M j, Y, g:i A') }}</p>
                                        <button type="button" @click="openComments = !openComments" class="inline-flex items-center rounded-full border border-[#d8c7a9] bg-[#fff4df] px-3 py-1.5 text-xs font-semibold text-[#5f4a36] transition hover:bg-[#f4e4ca]">
                                            <i class="fa-regular fa-comments mr-1"></i>
                                            View Comments ({{ (int) $post->comments_count }})
                                        </button>
                                    </div>
                                </div>

                                <section x-cloak x-show="openComments" x-transition class="border-t border-[#ecdfc8] bg-[#fffaf1] p-4 sm:p-5">
                                    <div class="space-y-3">
                                        @forelse ($post->comments as $comment)
                                            <article class="rounded-xl border border-[#e3d4bc] bg-white p-3">
                                                <div class="flex items-start gap-3">
                                                    @php
                                                        $commenterName = $comment->user?->full_name ?: $comment->user?->name ?: 'User';
                                                        $commenterAvatar = $comment->user?->profile_photo_path ? asset('storage/'.$comment->user->profile_photo_path) : null;
                                                    @endphp
                                                    @if ($commenterAvatar)
                                                        <img src="{{ $commenterAvatar }}" alt="{{ $commenterName }}" class="h-9 w-9 rounded-full border border-[#d7c8ad] object-cover">
                                                    @else
                                                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-[#d7c8ad] bg-[#f4e7d1] text-xs font-bold text-[#5f4a36]">{{ strtoupper(substr($commenterName, 0, 1)) }}</span>
                                                    @endif
                                                    <div class="min-w-0 flex-1">
                                                        <div class="flex flex-wrap items-center gap-2">
                                                            <p class="text-sm font-bold text-[#3f2d22]">{{ $commenterName }}</p>
                                                            <span class="text-[11px] text-[#8a7764]">{{ $comment->created_at?->diffForHumans() }}</span>
                                                        </div>
                                                        <p class="mt-1 text-sm text-[#654f3f]">{{ $comment->body }}</p>
                                                        @if ($comment->offer_price !== null)
                                                            <p class="mt-1 text-xs font-bold text-[#3f2d22]">Offered Price: ₱{{ number_format((float) $comment->offer_price, 0) }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </article>
                                        @empty
                                            <p class="rounded-xl border border-dashed border-[#dac9af] bg-[#fff7eb] p-3 text-sm text-[#7b6756]">No comments yet. Be the first guide to send an offer.</p>
                                        @endforelse
                                    </div>

                                    <form action="{{ route('dashboard.guide.request-posts.comments.store', $post) }}" method="POST" class="mt-4 rounded-xl border border-[#dfceb3] bg-[#fffdf8] p-3">
                                        @csrf
                                        <label class="block text-xs font-semibold uppercase tracking-[0.08em] text-[#6f5d52]">Send Offer / Comment</label>
                                        <textarea name="body" rows="3" maxlength="1000" required placeholder="Share your itinerary, inclusions, or schedule..." class="mt-2 w-full rounded-lg border border-[#dcc9ad] bg-white px-3 py-2 text-sm text-[#4d3d30] placeholder:text-[#9b8a79] focus:border-[#a98b64] focus:outline-none focus:ring-2 focus:ring-[#d8c3a2]"></textarea>

                                        <div class="mt-2 grid gap-2 sm:grid-cols-[minmax(120px,200px)_1fr] sm:items-center">
                                            <input name="offer_price" type="number" min="0" step="0.01" placeholder="Offer price (optional)" class="w-full rounded-lg border border-[#dcc9ad] bg-white px-3 py-2 text-sm text-[#4d3d30] placeholder:text-[#9b8a79] focus:border-[#a98b64] focus:outline-none focus:ring-2 focus:ring-[#d8c3a2]">
                                            <button type="submit" class="inline-flex items-center justify-center rounded-full border border-[#4f7740] bg-[#5f8a46] px-3 py-2 text-xs font-semibold text-[#f7fff4] transition hover:bg-[#4f7740]">Comment</button>
                                        </div>
                                    </form>
                                </section>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>

            <section x-cloak x-show="activeTab === 'listings'" class="space-y-4">

                @if ($guideListings->isEmpty())
                    <div class="rounded-2xl border border-dashed border-[#d8c9af] bg-[#fff8ec] p-8 text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-[#efe2cb] text-[#7a654f]">
                            <i class="fa-solid fa-map-signs text-xl"></i>
                        </div>
                        <h2 class="mt-4 text-xl font-bold text-[#3f2d22]">No listings yet</h2>
                        <p class="mx-auto mt-2 max-w-xl text-sm text-[#6f5d52]">Create your first premium tour listing using the floating button.</p>
                    </div>
                @else
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($guideListings as $listing)
                            @php
                                $galleryImages = is_array($listing->gallery_images) ? $listing->gallery_images : [];
                                $thumbnail = $galleryImages[0] ?? $listing->image_path ?? $listing->featured_image ?? $listing->image_url;

                                if (is_string($thumbnail) && $thumbnail !== '') {
                                    if (str_starts_with($thumbnail, 'http://') || str_starts_with($thumbnail, 'https://') || str_starts_with($thumbnail, 'data:image/')) {
                                        $thumbnailUrl = $thumbnail;
                                    } elseif (str_starts_with($thumbnail, 'hero/') || str_starts_with($thumbnail, 'images/')) {
                                        $thumbnailUrl = asset($thumbnail);
                                    } else {
                                        $thumbnailUrl = asset('storage/'.$thumbnail);
                                    }
                                } else {
                                    $thumbnailUrl = asset('hero/palawan.jpg');
                                }

                                $durationLabel = $listing->duration_label ?: $listing->duration ?: 'Flexible';
                                $price = (float) ($listing->price_per_person ?? $listing->price ?? $listing->base_price ?? 0);
                                $rating = number_format((float) ($listing->rating ?? 4.9), 1);
                                $maxPax = (int) ($listing->max_guests ?? $listing->max_people ?? 1);
                                $status = strtolower((string) ($listing->status ?? 'active'));
                            @endphp

                            <article class="listing-card flex flex-col overflow-hidden">
                                <div class="relative h-[176px] w-full">
                                    <img src="{{ $thumbnailUrl }}" class="h-full w-full object-cover" alt="{{ $listing->title ?: $listing->name }}">
                                    <span class="absolute right-3 top-3 status-pill {{ $status === 'active' ? 'bg-[#edf4e6] text-[#385032]' : 'bg-[#f1e6d4] text-[#6a5644]' }}">
                                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                                    </span>
                                </div>

                                <div class="flex flex-1 flex-col px-4 pb-4 pt-3">
                                    <h3 class="text-[20px] font-semibold leading-tight text-[#5f5447]">{{ $listing->title ?: $listing->name }}</h3>
                                    <p class="mt-1 text-[12px] font-semibold text-[#9b8f7c]">{{ $listing->region ?: 'Philippines' }}</p>

                                    <div class="mt-3 grid gap-2 text-[11px] text-[#8e8374]">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-regular fa-clock w-3 text-center text-[#8f8475]"></i>
                                            <span>{{ $durationLabel }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-user-group w-3 text-center text-[#8f8475]"></i>
                                            <span>{{ $maxPax }} pax</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-star w-3 text-center text-[#d4a563]"></i>
                                            <span>{{ $rating }}</span>
                                        </div>
                                    </div>

                                    <div class="mt-3 flex items-center justify-between border-t border-[#e5dcc9] pt-3">
                                        <span class="text-[11px] font-semibold text-[#877a6a]">Price <span class="text-[16px] font-bold text-[#5d5142]">₱ {{ number_format($price, 2) }}</span></span>
                                    </div>

                                    <a href="{{ route('tours.show', ['tour' => $listing, 'from' => 'dashboard']) }}" class="mt-3 inline-flex items-center justify-center rounded-lg border border-[#4f7740] bg-[#5f8a46] px-2 py-2 text-xs font-semibold text-[#f7fff4] transition hover:bg-[#4f7740]">
                                        Open Full Listing Page
                                    </a>

                                    <div class="mt-3 grid grid-cols-3 gap-2">
                                        <a href="{{ route('dashboard.guide.tours', ['edit' => $listing->id]) }}" class="inline-flex items-center justify-center rounded-lg border border-[#d4a563]/45 bg-[#fff7ec] px-2 py-2 text-xs font-semibold text-[#7a5532] transition hover:bg-[#f7ead7]">Edit</a>
                                        <form method="POST" action="{{ route('dashboard.guide.listings.destroy', $listing) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg border border-[#d4a563]/45 bg-white px-2 py-2 text-xs font-semibold text-[#7a5532] transition hover:bg-[#fff7ec]">Delete</button>
                                        </form>
                                        <a href="{{ route('dashboard.guide.requests') }}" class="inline-flex items-center justify-center rounded-lg border border-[#4f7740] bg-[#5f8a46] px-2 py-2 text-xs font-semibold text-[#f7fff4] transition hover:bg-[#4f7740]">View Bookings</a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>
        </main>

        <button
            type="button"
            @click="openCreateListingModal()"
            class="fixed bottom-6 right-6 z-40 inline-flex min-h-[50px] items-center justify-center rounded-full border-2 bg-[#8f9d59] px-6 text-[16px] font-semibold text-[#ffffff] shadow-[0_10px_24px_rgba(61,47,31,0.16)] transition hover:bg-[#624f27] sm:min-h-[50px] sm:px-10 sm:text-[16px]"
        >
            <span class="mr-2 text-xl leading-none">+</span>
            Create Tour Listing
        </button>

        <div
            x-cloak
            x-show="showCreateModal"
            x-transition
            @keydown.escape.window="closeCreateListingModal()"
            class="fixed inset-0 z-[70] flex items-center justify-center bg-[#3f2d22]/60 p-4"
        >
            <section class="max-h-[94vh] w-full max-w-7xl overflow-y-auto rounded-3xl border border-[#eadfcd] bg-white p-5 shadow-[0_24px_56px_rgba(63,45,34,0.32)] sm:p-6" @click.outside="closeCreateListingModal()">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-2xl font-bold text-[#3f2d22]">Create Tour Listing</h2>
                        <p class="mt-1 text-sm text-[#7a6a58]">Build your listing from left to right. Every field updates the card and full page preview instantly.</p>
                    </div>
                    <button type="button" @click="closeCreateListingModal()" class="rounded-lg px-2 py-1 text-[#7a6a58] transition hover:bg-[#f7ecd7]">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1.2fr)_minmax(360px,0.8fr)]">
                    <form action="{{ route('dashboard.guide.listings.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <div class="space-y-4 rounded-2xl border border-[#deceb7] bg-[#fffcf6] p-4">

                            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-[#6b5a49]" for="listing_title">Tour Title *</label>
                                    <input id="listing_title" name="title" x-model="listingForm.title" type="text" maxlength="255" required class="w-full rounded-xl border border-[#deceb7] bg-white px-3 py-2.5 text-sm text-[#4f4134] focus:border-[#b9915f] focus:outline-none focus:ring-2 focus:ring-[#e8d6bc]">
                                </div>

                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-[#6b5a49]" for="listing_region">Region *</label>
                                    <select id="listing_region" name="region" x-model="listingForm.region" required class="w-full rounded-xl border border-[#deceb7] bg-white px-3 py-2.5 text-sm text-[#4f4134] focus:border-[#b9915f] focus:outline-none focus:ring-2 focus:ring-[#e8d6bc]">
                                        <option value="">Select region</option>
                                        <template x-for="region in regionOptions" :key="region">
                                            <option :value="region" x-text="region"></option>
                                        </template>
                                    </select>
                                </div>

                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-[#6b5a49]" for="listing_location">City / Province *</label>
                                    <input id="listing_location" name="location" type="text" x-model="listingForm.location" list="listing_city_options" required class="w-full rounded-xl border border-[#deceb7] bg-white px-3 py-2.5 text-sm text-[#4f4134] focus:border-[#b9915f] focus:outline-none focus:ring-2 focus:ring-[#e8d6bc]">
                                    <datalist id="listing_city_options">
                                        <template x-for="city in cityOptions" :key="city">
                                            <option :value="city"></option>
                                        </template>
                                    </datalist>
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-[#6b5a49]" for="listing_short_description">Description</label>
                                    <textarea id="listing_short_description" name="short_description" x-model="listingForm.shortDescription" maxlength="150" rows="3" required class="w-full rounded-xl border border-[#deceb7] bg-white px-3 py-2.5 text-sm text-[#4f4134] focus:border-[#b9915f] focus:outline-none focus:ring-2 focus:ring-[#e8d6bc]"></textarea>
                                    <p class="mt-1 text-[11px] text-[#7a6a58]" x-text="`${listingForm.shortDescription.length}/150`"></p>
                                </div>
                            </div>
                        <div class="border-t border-[#eadfcf] pt-4">

                            <div class="mt-3 grid gap-3">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-[#6b5a49]" for="listing_cover_image">Main Cover Photo *</label>
                                    <input x-ref="coverImageInput" id="listing_cover_image" name="cover_image" type="file" accept="image/*" @change="updateCoverImage($event)" class="sr-only" required>
                                    <label for="listing_cover_image" class="inline-flex cursor-pointer items-center rounded-lg bg-[#7a8730] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#697629]">Choose cover image</label>
                                    <p class="mt-2 text-[11px] text-[#7a6a58]" x-text="coverImagePreview ? 'Cover image selected.' : 'Required before publish.'"></p>

                                    <div class="mt-2" x-show="coverImagePreview" x-cloak>
                                        <div class="relative h-40 overflow-hidden rounded-xl border border-[#deceb7] bg-white">
                                            <img :src="coverImagePreview" alt="Cover preview" class="h-full w-full object-cover">
                                            <button type="button" @click="removeCoverImage()" class="absolute right-2 top-2 inline-flex h-7 w-7 items-center justify-center rounded-full bg-black/70 text-white transition hover:bg-black" aria-label="Remove cover image">
                                                <i class="fa-solid fa-xmark text-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-[#6b5a49]" for="listing_gallery_images">Gallery Photos *</label>
                                    <input x-ref="galleryImagesInput" id="listing_gallery_images" name="gallery_images[]" type="file" multiple accept="image/*" @change="updateGalleryImages($event)" class="sr-only" required>
                                    <label for="listing_gallery_images" class="inline-flex cursor-pointer items-center rounded-lg bg-[#7a8730] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#697629]">Choose 1 to 4 gallery photos</label>
                                    <p class="mt-2 text-[11px] text-[#7a6a58]" x-text="`${galleryImagePreviews.length} selected (minimum 3)`"></p>

                                    <div class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-3" x-show="galleryImagePreviews.length > 0" x-cloak>
                                        <template x-for="(previewUrl, index) in galleryImagePreviews" :key="`${previewUrl}-${index}`">
                                            <div class="relative aspect-square overflow-hidden rounded-xl border border-[#deceb7] bg-white">
                                                <img :src="previewUrl" alt="Listing gallery image preview" class="h-full w-full object-cover">
                                                <button type="button" @click="removeGalleryImage(index)" class="absolute right-1.5 top-1.5 inline-flex h-6 w-6 items-center justify-center rounded-full bg-black/70 text-white transition hover:bg-black" aria-label="Remove selected gallery image">
                                                    <i class="fa-solid fa-xmark text-xs" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-[#eadfcf] pt-4">
                            <h3 class="text-sm font-bold uppercase tracking-[0.08em] text-[#5f4a36]">Tour Details</h3>

                            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-[#6b5a49]" for="listing_duration_label">Duration</label>
                                    <select id="listing_duration_label" name="duration_label" x-model="listingForm.durationLabel" class="w-full rounded-xl border border-[#deceb7] bg-white px-3 py-2.5 text-sm text-[#4f4134] focus:border-[#b9915f] focus:outline-none focus:ring-2 focus:ring-[#e8d6bc]">
                                        <option value="2 hrs">2 hrs</option>
                                        <option value="Half-day">Half-day</option>
                                        <option value="Full-day">Full-day</option>
                                        <option value="Multi-day">Multi-day</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-[#6b5a49]" for="listing_difficulty">Difficulty / Flexibility</label>
                                    <select id="listing_difficulty" name="difficulty" x-model="listingForm.difficulty" class="w-full rounded-xl border border-[#deceb7] bg-white px-3 py-2.5 text-sm text-[#4f4134] focus:border-[#b9915f] focus:outline-none focus:ring-2 focus:ring-[#e8d6bc]">
                                        <option value="Easy">Easy</option>
                                        <option value="Moderate">Moderate</option>
                                        <option value="Flexible">Flexible</option>
                                        <option value="Custom Pace">Custom Pace</option>
                                    </select>
                                </div>

                                <div class="sm:col-span-2">
                                    <p class="mb-2 text-xs font-semibold uppercase tracking-[0.08em] text-[#6b5a49]">Activity Type</p>
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="activity in activityTypeOptions" :key="activity">
                                            <label class="inline-flex items-center gap-2 rounded-full border border-[#d8c7a9] bg-white px-3 py-1.5 text-xs font-semibold text-[#5f4a36]">
                                                <input type="checkbox" name="activity_types[]" :value="activity" x-model="listingForm.activityTypes" class="h-3.5 w-3.5 rounded border-[#bba88d] text-[#5f8a46] focus:ring-[#8fa866]">
                                                <span x-text="activity"></span>
                                            </label>
                                        </template>
                                    </div>
                                </div>

                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-[#6b5a49]" for="listing_min_guests">Minimum Guests</label>
                                    <input id="listing_min_guests" name="min_guests" type="number" min="1" max="100" x-model.number="listingForm.minGuests" class="w-full rounded-xl border border-[#deceb7] bg-white px-3 py-2.5 text-sm text-[#4f4134] focus:border-[#b9915f] focus:outline-none focus:ring-2 focus:ring-[#e8d6bc]">
                                </div>

                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-[#6b5a49]" for="listing_max_guests">Maximum Guests</label>
                                    <input id="listing_max_guests" name="max_guests" type="number" min="1" max="100" x-model.number="listingForm.maxGuests" class="w-full rounded-xl border border-[#deceb7] bg-white px-3 py-2.5 text-sm text-[#4f4134] focus:border-[#b9915f] focus:outline-none focus:ring-2 focus:ring-[#e8d6bc]">
                                </div>

                                <div class="sm:col-span-2">
                                    <p class="mb-2 text-xs font-semibold uppercase tracking-[0.08em] text-[#6b5a49]">Languages Spoken</p>
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="language in languageOptions" :key="language">
                                            <label class="inline-flex items-center gap-2 rounded-full border border-[#d8c7a9] bg-white px-3 py-1.5 text-xs font-semibold text-[#5f4a36]">
                                                <input type="checkbox" name="languages_spoken[]" :value="language" x-model="listingForm.languages" class="h-3.5 w-3.5 rounded border-[#bba88d] text-[#5f8a46] focus:ring-[#8fa866]">
                                                <span x-text="language"></span>
                                            </label>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-[#eadfcf] pt-4">

                            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-[#6b5a49]" for="listing_base_price">Base Price (PHP) *</label>
                                    <input id="listing_base_price" name="base_price" type="number" min="0" step="0.01" x-model.number="listingForm.basePrice" required class="w-full rounded-xl border border-[#deceb7] bg-white px-3 py-2.5 text-sm text-[#4f4134] focus:border-[#b9915f] focus:outline-none focus:ring-2 focus:ring-[#e8d6bc]">
                                </div>

                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-[#6b5a49]" for="listing_price_type">Price Type</label>
                                    <select id="listing_price_type" name="price_type" x-model="listingForm.priceType" class="w-full rounded-xl border border-[#deceb7] bg-white px-3 py-2.5 text-sm text-[#4f4134] focus:border-[#b9915f] focus:outline-none focus:ring-2 focus:ring-[#e8d6bc]">
                                        <option value="per_person">Per person</option>
                                        <option value="per_group">Per group</option>
                                        <option value="custom_quote">Custom quote</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-[#eadfcf] pt-4">

                            <div class="mt-3 grid gap-3">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-[#6b5a49]" for="listing_overview_description">Overview Description</label>
                                    <textarea id="listing_overview_description" name="overview_description" rows="5" x-model="listingForm.overviewDescription" class="w-full rounded-xl border border-[#deceb7] bg-white px-3 py-2.5 text-sm text-[#4f4134] focus:border-[#b9915f] focus:outline-none focus:ring-2 focus:ring-[#e8d6bc]"></textarea>
                                </div>

                                <div>
                                    <p class="mb-2 text-xs font-semibold uppercase tracking-[0.08em] text-[#6b5a49]">Itinerary Builder</p>
                                    <div class="grid gap-2 sm:grid-cols-2">
                                        <input name="itinerary_pickup" type="text" x-model="listingForm.itinerary.pickup" placeholder="Pickup Point" class="rounded-xl border border-[#deceb7] bg-white px-3 py-2 text-sm text-[#4f4134] focus:border-[#b9915f] focus:outline-none focus:ring-2 focus:ring-[#e8d6bc]">
                                        <input name="itinerary_stop_1" type="text" x-model="listingForm.itinerary.stop1" placeholder="Stop 1" class="rounded-xl border border-[#deceb7] bg-white px-3 py-2 text-sm text-[#4f4134] focus:border-[#b9915f] focus:outline-none focus:ring-2 focus:ring-[#e8d6bc]">
                                        <input name="itinerary_stop_2" type="text" x-model="listingForm.itinerary.stop2" placeholder="Stop 2" class="rounded-xl border border-[#deceb7] bg-white px-3 py-2 text-sm text-[#4f4134] focus:border-[#b9915f] focus:outline-none focus:ring-2 focus:ring-[#e8d6bc]">
                                        <input name="itinerary_lunch" type="text" x-model="listingForm.itinerary.lunch" placeholder="Lunch" class="rounded-xl border border-[#deceb7] bg-white px-3 py-2 text-sm text-[#4f4134] focus:border-[#b9915f] focus:outline-none focus:ring-2 focus:ring-[#e8d6bc]">
                                        <input name="itinerary_return" type="text" x-model="listingForm.itinerary.return" placeholder="Return" class="rounded-xl border border-[#deceb7] bg-white px-3 py-2 text-sm text-[#4f4134] focus:border-[#b9915f] focus:outline-none focus:ring-2 focus:ring-[#e8d6bc] sm:col-span-2">
                                    </div>
                                </div>

                                <div>
                                    <p class="mb-2 text-xs font-semibold uppercase tracking-[0.08em] text-[#6b5a49]">What's Included</p>
                                    <div class="grid grid-cols-2 gap-2">
                                        <template x-for="item in inclusionOptions" :key="item.value">
                                            <label class="inline-flex items-center gap-2 rounded-xl border border-[#d8c7a9] bg-white px-3 py-2 text-xs font-semibold text-[#5f4a36]">
                                                <input type="checkbox" name="included[]" :value="item.label" x-model="listingForm.included" class="h-3.5 w-3.5 rounded border-[#bba88d] text-[#5f8a46] focus:ring-[#8fa866]">
                                                <span x-text="item.label"></span>
                                            </label>
                                        </template>
                                    </div>
                                </div>

                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-[#6b5a49]" for="listing_exclusions_text">What's Not Included</label>
                                    <textarea id="listing_exclusions_text" name="exclusions_text" rows="3" x-model="listingForm.exclusionsText" class="w-full rounded-xl border border-[#deceb7] bg-white px-3 py-2.5 text-sm text-[#4f4134] focus:border-[#b9915f] focus:outline-none focus:ring-2 focus:ring-[#e8d6bc]"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-[#eadfcf] pt-4">

                            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <p class="mb-2 text-xs font-semibold uppercase tracking-[0.08em] text-[#6b5a49]">Available Days</p>
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="day in weekdayOptions" :key="day">
                                            <label class="inline-flex items-center gap-2 rounded-full border border-[#d8c7a9] bg-white px-3 py-1.5 text-xs font-semibold text-[#5f4a36]">
                                                <input type="checkbox" name="available_days[]" :value="day" x-model="listingForm.availableDays" class="h-3.5 w-3.5 rounded border-[#bba88d] text-[#5f8a46] focus:ring-[#8fa866]">
                                                <span x-text="day"></span>
                                            </label>
                                        </template>
                                    </div>
                                </div>

                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-[#6b5a49]" for="listing_start_time">Start Time</label>
                                    <input id="listing_start_time" name="start_time" type="time" x-model="listingForm.startTime" class="w-full rounded-xl border border-[#deceb7] bg-white px-3 py-2.5 text-sm text-[#4f4134] focus:border-[#b9915f] focus:outline-none focus:ring-2 focus:ring-[#e8d6bc]">
                                </div>

                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-[#6b5a49]" for="listing_end_time">End Time</label>
                                    <input id="listing_end_time" name="end_time" type="time" x-model="listingForm.endTime" class="w-full rounded-xl border border-[#deceb7] bg-white px-3 py-2.5 text-sm text-[#4f4134] focus:border-[#b9915f] focus:outline-none focus:ring-2 focus:ring-[#e8d6bc]">
                                </div>

                                <label class="inline-flex items-center gap-2 rounded-xl border border-[#d8c7a9] bg-white px-3 py-2 text-xs font-semibold text-[#5f4a36]">
                                    <input type="checkbox" name="instant_book" value="1" x-model="listingForm.instantBook" class="h-4 w-4 rounded border-[#bba88d] text-[#5f8a46] focus:ring-[#8fa866]">
                                    Instant Book
                                </label>

                                <label class="inline-flex items-center gap-2 rounded-xl border border-[#d8c7a9] bg-white px-3 py-2 text-xs font-semibold text-[#5f4a36]">
                                    <input type="checkbox" name="requires_approval" value="1" x-model="listingForm.requiresApproval" class="h-4 w-4 rounded border-[#bba88d] text-[#5f8a46] focus:ring-[#8fa866]">
                                    Requires Approval
                                </label>

                                <div class="sm:col-span-2">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-[#6b5a49]" for="listing_max_slots">Max Slots</label>
                                    <input id="listing_max_slots" name="max_slots" type="number" min="1" max="100" x-model.number="listingForm.maxSlots" class="w-full rounded-xl border border-[#deceb7] bg-white px-3 py-2.5 text-sm text-[#4f4134] focus:border-[#b9915f] focus:outline-none focus:ring-2 focus:ring-[#e8d6bc]">
                                </div>
                            </div>
                        </div>

                        </div>

                        <div class="mt-2 flex justify-end gap-2">
                            <button type="button" @click="closeCreateListingModal()" class="rounded-xl border border-[#d8c7a9] bg-[#fffaf2] px-4 py-2 text-sm font-semibold text-[#5e4a39] transition hover:bg-[#f6ecd9]">Cancel</button>
                            <button type="submit" :disabled="!canPublish()" class="rounded-xl border border-[#4f7740] bg-[#5f8a46] px-4 py-2 text-sm font-semibold text-[#f7fff4] transition hover:bg-[#4f7740] disabled:cursor-not-allowed disabled:opacity-60">Publish Listing</button>
                        </div>
                    </form>

                    <aside class="lg:sticky lg:top-4">
                        <div class="overflow-hidden rounded-2xl border border-[#deceb7] bg-[#fffdf8] shadow-[0_12px_28px_rgba(63,45,34,0.12)]">
                            <div class="flex items-center gap-1 border-b border-[#e9ddc9] bg-[#fff6e8] p-2">
                                <button type="button" @click="previewTab = 'card'" :class="previewTab === 'card' ? 'bg-white text-[#3f2d22]' : 'text-[#7d6b5b]'" class="rounded-lg px-3 py-1.5 text-xs font-semibold transition">Live Thumbnail Preview Card</button>
                                <button type="button" @click="previewTab = 'full'" :class="previewTab === 'full' ? 'bg-white text-[#3f2d22]' : 'text-[#7d6b5b]'" class="rounded-lg px-3 py-1.5 text-xs font-semibold transition">Full Listing Preview Tab</button>
                            </div>

                            <div class="p-3" x-show="previewTab === 'card'" x-cloak>
                                <article class="relative flex flex-col overflow-hidden rounded-[18px] border border-[#e7dcc8] bg-[#f8f5ef] shadow-[0_8px_22px_rgba(52,41,26,0.08)]">
                                    <div class="relative h-[176px] w-full">
                                        <img :src="primaryPreviewImage()" class="h-full w-full object-cover" alt="Card cover preview">
                                        <button type="button" class="absolute right-3 top-3 inline-flex h-8 w-8 items-center justify-center rounded-full border border-[#dfd7c8] bg-white/95 text-[#8b7f6e] shadow-sm">
                                            <i class="fa-regular fa-heart"></i>
                                        </button>

                                        <div class="absolute -bottom-4 left-3 right-3 flex items-center gap-1.5 overflow-hidden rounded-full border border-[#e3ddd0] bg-white/95 px-2 py-1 shadow-sm">
                                            <template x-if="guideAvatarUrl">
                                                <img :src="guideAvatarUrl" class="h-5 w-5 rounded-full object-cover border border-[#ede1cd]" alt="Guide avatar preview">
                                            </template>
                                            <template x-if="!guideAvatarUrl">
                                                <span class="inline-flex h-5 w-5 items-center justify-center rounded-full border border-[#ede1cd] bg-[#f4e7d1] text-[9px] font-bold text-[#5f4a36]" x-text="guideInitials()"></span>
                                            </template>
                                            <span class="truncate text-[10px] font-medium text-[#8a7f70]">Your Guide: <span x-text="listingForm.guideName || 'Guide Name'"></span></span>
                                        </div>
                                    </div>

                                    <div class="flex flex-grow flex-col px-4 pb-4 pt-7">
                                        <h3 class="mb-1 text-[21px] font-semibold leading-tight text-[#5f5447]" x-text="listingForm.title || 'Tour Title'"></h3>
                                        <p class="mb-1 text-[12px] font-medium text-[#7f7262]" x-text="formattedRegion()"></p>
                                        <p class="mb-2 line-clamp-2 text-[12px] text-[#887a69]" x-text="listingForm.shortDescription || 'Short description will appear here.'"></p>

                                        <div class="mb-3 flex items-center gap-1 text-[11px] font-semibold text-[#9b8f7c]">
                                            <i class="fa-solid fa-star text-[#d4a563] text-[10px]"></i>
                                            <span>5.0</span>
                                        </div>

                                        <div class="mt-auto grid gap-2.5 text-[11px] text-[#8e8374]">
                                            <div class="flex items-center gap-2">
                                                <i class="fa-regular fa-clock w-3 text-center text-[#8f8475]"></i>
                                                <span x-text="listingForm.durationLabel || 'Flexible'"></span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <i class="fa-solid fa-person-walking w-3 text-center text-[#8f8475]"></i>
                                                <span x-text="activityLabelForCard()"></span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <i class="fa-solid fa-user-group w-3 text-center text-[#8f8475]"></i>
                                                <span x-text="`${listingForm.minGuests} - ${listingForm.maxGuests} pax`"></span>
                                            </div>
                                        </div>

                                        <div class="mt-3 flex items-center justify-between border-t border-[#e5dcc9] pt-3">
                                            <span class="text-[11px] font-semibold text-[#877a6a]">Total <span class="text-[16px] font-bold text-[#5d5142]" x-text="displayPrice()"></span></span>
                                        </div>
                                    </div>
                                </article>
                            </div>

                            <div class="p-3" x-show="previewTab === 'full'" x-cloak>
                                <article class="overflow-hidden rounded-2xl border border-[#e2d4bf] bg-white">
                                    <div class="grid gap-1 sm:grid-cols-[2fr_1fr]">
                                        <div class="h-44 overflow-hidden bg-[#f3e6d2]">
                                            <img :src="primaryPreviewImage()" alt="Full page hero preview" class="h-full w-full object-cover">
                                        </div>
                                        <div class="grid grid-cols-2 gap-1">
                                            <template x-for="(image, index) in secondaryPreviewImages()" :key="`gallery-preview-${index}-${image}`">
                                                <div class="h-[5.4rem] overflow-hidden bg-[#f3e6d2]">
                                                    <img :src="image" alt="Gallery preview" class="h-full w-full object-cover">
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="space-y-4 p-4">
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <h4 class="text-lg font-bold text-[#3f2d22]" x-text="listingForm.title || 'Tour Title'"></h4>
                                                <p class="mt-1 text-xs font-semibold text-[#7b6756]" x-text="formattedRegion()"></p>
                                            </div>
                                            <span class="rounded-full bg-[#edf4e6] px-2.5 py-1 text-[11px] font-semibold text-[#385032]">Preview</span>
                                        </div>

                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-[0.08em] text-[#7f6b57]">Overview</p>
                                            <p class="mt-1 text-sm text-[#6f5d52]" x-text="listingForm.overviewDescription || listingForm.shortDescription || 'Long overview will appear here.'"></p>
                                        </div>

                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-[0.08em] text-[#7f6b57]">Itinerary</p>
                                            <ul class="mt-1 space-y-1 text-sm text-[#6f5d52]">
                                                <template x-for="(item, index) in itineraryPreviewItems()" :key="`itinerary-${index}`">
                                                    <li class="inline-flex items-start gap-2">
                                                        <i class="fa-solid fa-location-dot mt-0.5 text-[10px] text-[#7a8730]"></i>
                                                        <span x-text="item"></span>
                                                    </li>
                                                </template>
                                            </ul>
                                        </div>

                                        <div class="grid gap-3 sm:grid-cols-2">
                                            <div>
                                                <p class="text-xs font-semibold uppercase tracking-[0.08em] text-[#7f6b57]">What's Included</p>
                                                <ul class="mt-1 space-y-1 text-xs text-[#6f5d52]">
                                                    <template x-for="item in includedPreviewItems()" :key="item">
                                                        <li class="inline-flex items-center gap-1.5"><i class="fa-solid fa-check text-[10px] text-[#4f7740]"></i><span x-text="item"></span></li>
                                                    </template>
                                                </ul>
                                            </div>

                                            <div>
                                                <p class="text-xs font-semibold uppercase tracking-[0.08em] text-[#7f6b57]">What's Not Included</p>
                                                <p class="mt-1 text-xs text-[#6f5d52]" x-text="listingForm.exclusionsText || 'Not specified yet.'"></p>
                                            </div>
                                        </div>

                                        <div class="rounded-xl border border-[#e3d4bc] bg-[#fff8ec] p-3">
                                            <p class="text-sm font-semibold text-[#3f2d22]" x-text="displayPrice()"></p>
                                            <p class="mt-1 text-[11px] text-[#7b6756]" x-text="bookingSummaryLabel()"></p>
                                            <button type="button" class="mt-2 inline-flex w-full items-center justify-center rounded-full border border-[#4f7740] bg-[#5f8a46] px-3 py-2 text-xs font-semibold text-[#f7fff4]">Book Now</button>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        </div>
                    </aside>
                </div>
            </section>
        </div>
    </div>

    @livewireScripts

    <script>
        function guideListingBuilder(config) {
            return {
                scrolled: false,
                menuOpen: false,
                activeTab: config.initialTab ?? 'requests',
                showCreateModal: false,
                previewTab: 'card',
                coverImageFile: null,
                coverImagePreview: '',
                galleryImageFiles: [],
                galleryImagePreviews: [],
                guideAvatarUrl: config.guideAvatarUrl ?? null,
                regionOptions: config.regionOptions ?? [],
                cityOptions: config.cityOptions ?? [],
                fallbackImage: @js(asset('hero/palawan.jpg')),
                activityTypeOptions: ['Walking', 'Hiking', 'Boat', 'Driving', 'Island Hopping', 'Cultural', 'Food Tour'],
                languageOptions: ['English', 'Filipino', 'Cebuano', 'Ilocano', 'Bicolano', 'Waray', 'Hiligaynon'],
                weekdayOptions: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                inclusionOptions: [
                    { value: 'guide_fee', label: 'Guide Fee' },
                    { value: 'boat_transfer', label: 'Boat Transfer' },
                    { value: 'entrance_fees', label: 'Entrance Fees' },
                    { value: 'snacks', label: 'Snacks' },
                    { value: 'water', label: 'Water' },
                    { value: 'photos', label: 'Photos' },
                    { value: 'safety_support', label: 'Safety Support' },
                ],
                listingForm: {
                    title: '',
                    region: '',
                    location: '',
                    shortDescription: '',
                    guideName: config.guideName ?? 'Certified Local Guide',
                    guideBio: config.guideBio ?? 'Certified Local Guide',
                    durationLabel: 'Full-day',
                    activityTypes: ['Walking'],
                    difficulty: 'Flexible',
                    minGuests: 1,
                    maxGuests: 6,
                    languages: ['English', 'Filipino'],
                    basePrice: null,
                    priceType: 'per_person',
                    overviewDescription: '',
                    itinerary: {
                        pickup: '',
                        stop1: '',
                        stop2: '',
                        lunch: '',
                        return: '',
                    },
                    included: ['Guide Fee', 'Safety Support'],
                    exclusionsText: '',
                    availableDays: ['Saturday', 'Sunday'],
                    startTime: '',
                    endTime: '',
                    instantBook: false,
                    requiresApproval: true,
                    maxSlots: 12,
                    safetyCertified: false,
                    ecoFriendly: false,
                    audienceBadges: [],
                },

                init() {
                    window.addEventListener('scroll', () => {
                        this.scrolled = window.scrollY > 32;
                    });
                },

                resetListingBuilder() {
                    if (this.coverImagePreview !== '') {
                        URL.revokeObjectURL(this.coverImagePreview);
                    }

                    this.galleryImagePreviews.forEach((previewUrl) => URL.revokeObjectURL(previewUrl));

                    this.previewTab = 'card';
                    this.coverImageFile = null;
                    this.coverImagePreview = '';
                    this.galleryImageFiles = [];
                    this.galleryImagePreviews = [];

                    this.listingForm = {
                        title: '',
                        region: '',
                        location: '',
                        shortDescription: '',
                        guideName: config.guideName ?? 'Certified Local Guide',
                        guideBio: config.guideBio ?? 'Certified Local Guide',
                        durationLabel: 'Full-day',
                        activityTypes: ['Walking'],
                        difficulty: 'Flexible',
                        minGuests: 1,
                        maxGuests: 6,
                        languages: ['English', 'Filipino'],
                        basePrice: null,
                        priceType: 'per_person',
                        overviewDescription: '',
                        itinerary: {
                            pickup: '',
                            stop1: '',
                            stop2: '',
                            lunch: '',
                            return: '',
                        },
                        included: ['Guide Fee', 'Safety Support'],
                        exclusionsText: '',
                        availableDays: ['Saturday', 'Sunday'],
                        startTime: '',
                        endTime: '',
                        instantBook: false,
                        requiresApproval: true,
                        maxSlots: 12,
                        safetyCertified: false,
                        ecoFriendly: false,
                        audienceBadges: [],
                    };

                    if (this.$refs.coverImageInput) {
                        this.$refs.coverImageInput.value = '';
                    }

                    if (this.$refs.galleryImagesInput) {
                        this.$refs.galleryImagesInput.value = '';
                    }
                },

                openCreateListingModal() {
                    this.resetListingBuilder();
                    this.activeTab = 'listings';
                    this.showCreateModal = true;
                },

                closeCreateListingModal() {
                    this.showCreateModal = false;
                    this.resetListingBuilder();
                },

                updateCoverImage(event) {
                    const files = Array.from(event.target.files ?? []);
                    const [file] = files;

                    if (!file) {
                        return;
                    }

                    if (this.coverImagePreview !== '') {
                        URL.revokeObjectURL(this.coverImagePreview);
                    }

                    this.coverImageFile = file;
                    this.coverImagePreview = URL.createObjectURL(file);
                },

                removeCoverImage() {
                    if (this.coverImagePreview !== '') {
                        URL.revokeObjectURL(this.coverImagePreview);
                    }

                    this.coverImageFile = null;
                    this.coverImagePreview = '';

                    if (this.$refs.coverImageInput) {
                        this.$refs.coverImageInput.value = '';
                    }
                },

                updateGalleryImages(event) {
                    const files = Array.from(event.target.files ?? []);

                    if (files.length === 0) {
                        return;
                    }

                    const combinedFiles = [...this.galleryImageFiles, ...files];
                    const uniqueFilesBySignature = new Map();

                    combinedFiles.forEach((file) => {
                        const signature = `${file.name}-${file.size}-${file.lastModified}`;
                        if (!uniqueFilesBySignature.has(signature)) {
                            uniqueFilesBySignature.set(signature, file);
                        }
                    });

                    this.galleryImageFiles = Array.from(uniqueFilesBySignature.values()).slice(0, 5);
                    this.syncGalleryInputAndPreviews();
                },

                removeGalleryImage(index) {
                    if (index < 0 || index >= this.galleryImageFiles.length) {
                        return;
                    }

                    this.galleryImageFiles.splice(index, 1);
                    this.syncGalleryInputAndPreviews();
                },

                syncGalleryInputAndPreviews() {
                    this.galleryImagePreviews.forEach((previewUrl) => URL.revokeObjectURL(previewUrl));
                    this.galleryImagePreviews = this.galleryImageFiles.map((file) => URL.createObjectURL(file));

                    if (!this.$refs.galleryImagesInput || typeof DataTransfer === 'undefined') {
                        return;
                    }

                    const transfer = new DataTransfer();
                    this.galleryImageFiles.forEach((file) => transfer.items.add(file));
                    this.$refs.galleryImagesInput.files = transfer.files;
                },

                validationChecklist() {
                    return [
                        { label: 'Cover image is required', valid: this.coverImageFile !== null },
                        { label: 'Tour title is required', valid: this.listingForm.title.trim() !== '' },
                        { label: 'Location is required', valid: this.listingForm.location.trim() !== '' && this.listingForm.region.trim() !== '' },
                        { label: 'Short description is required', valid: this.listingForm.shortDescription.trim() !== '' },
                        { label: 'Base price is required', valid: Number(this.listingForm.basePrice) > 0 },
                        { label: 'At least 3 gallery photos', valid: this.galleryImageFiles.length >= 3 },
                    ];
                },

                canPublish() {
                    return this.validationChecklist().every((rule) => rule.valid);
                },

                primaryPreviewImage() {
                    if (this.coverImagePreview !== '') {
                        return this.coverImagePreview;
                    }

                    if (this.galleryImagePreviews.length > 0) {
                        return this.galleryImagePreviews[0];
                    }

                    return this.fallbackImage;
                },

                secondaryPreviewImages() {
                    const heroImage = this.primaryPreviewImage();
                    const galleryImages = this.galleryImagePreviews.filter((image) => image !== heroImage).slice(0, 4);

                    if (galleryImages.length === 0) {
                        if (heroImage !== this.fallbackImage) {
                            return [heroImage, heroImage, heroImage, heroImage];
                        }

                        return [this.fallbackImage, this.fallbackImage, this.fallbackImage, this.fallbackImage];
                    }

                    while (galleryImages.length < 4) {
                        galleryImages.push(galleryImages[galleryImages.length - 1]);
                    }

                    return galleryImages;
                },

                formattedRegion() {
                    if (this.listingForm.region.trim() === '' && this.listingForm.location.trim() === '') {
                        return 'Region / Location';
                    }

                    if (this.listingForm.region.trim() === '') {
                        return this.listingForm.location;
                    }

                    if (this.listingForm.location.trim() === '') {
                        return this.listingForm.region;
                    }

                    return `${this.listingForm.region}, ${this.listingForm.location}`;
                },

                activityLabelForCard() {
                    if (this.listingForm.activityTypes.length === 0) {
                        return this.listingForm.difficulty || 'Flexible';
                    }

                    return this.listingForm.activityTypes[0];
                },

                displayPrice() {
                    const numericPrice = Number(this.listingForm.basePrice || 0);
                    return `₱ ${numericPrice.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                },

                bookingSummaryLabel() {
                    const daysLabel = this.listingForm.availableDays.length > 0 ? this.listingForm.availableDays.join(', ') : 'Flexible days';
                    const timeLabel = this.listingForm.startTime && this.listingForm.endTime
                        ? `${this.listingForm.startTime} - ${this.listingForm.endTime}`
                        : 'Time on request';
                    const bookingMode = this.listingForm.instantBook ? 'Instant Book' : (this.listingForm.requiresApproval ? 'Requires Approval' : 'Manual confirmation');
                    return `${this.listingForm.priceType.replace('_', ' ')} • ${daysLabel} • ${timeLabel} • ${bookingMode}`;
                },

                itineraryPreviewItems() {
                    const values = [
                        this.listingForm.itinerary.pickup,
                        this.listingForm.itinerary.stop1,
                        this.listingForm.itinerary.stop2,
                        this.listingForm.itinerary.lunch,
                        this.listingForm.itinerary.return,
                    ]
                        .map((item) => item.trim())
                        .filter((item) => item !== '');

                    if (values.length === 0) {
                        return [
                            'Pickup Point',
                            'Stop 1',
                            'Stop 2',
                            'Lunch',
                            'Return',
                        ];
                    }

                    return values;
                },

                includedPreviewItems() {
                    if (this.listingForm.included.length === 0) {
                        return ['Guide Fee', 'Safety Support'];
                    }

                    return this.listingForm.included;
                },

                guideInitials() {
                    const source = (this.listingForm.guideName || 'Guide').trim();
                    const parts = source.split(/\s+/).filter((segment) => segment !== '');
                    const first = parts[0]?.charAt(0) ?? 'G';
                    const second = parts[1]?.charAt(0) ?? '';
                    return `${first}${second}`.toUpperCase();
                },
            };
        }

        (() => {
            document.addEventListener('DOMContentLoaded', () => {
                if (!window.Echo) {
                    return;
                }

                window.Echo.private('guides.request-feed')
                    .listen('.tourist.request.posted', () => {
                        const currentTab = new URLSearchParams(window.location.search).get('tab');
                        if (currentTab === 'listings') {
                            return;
                        }

                        window.location.reload();
                    });
            });
        })();
    </script>
</body>

</html>
