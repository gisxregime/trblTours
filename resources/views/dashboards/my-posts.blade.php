<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TrblTours - My Posts</title>
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
            --forest-600: #4f7740;
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

        .status-pill {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 0.25rem 0.65rem;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .tour-request-overlay {
            position: fixed;
            inset: 0;
            z-index: 90;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 18px;
            background: rgba(63, 45, 34, 0.6);
            backdrop-filter: blur(7px);
            -webkit-backdrop-filter: blur(7px);
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity 0.22s ease, visibility 0.22s ease;
        }

        .tour-request-overlay.is-open {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        .tour-request-modal {
            width: min(700px, 95%);
            max-height: min(88vh, 860px);
            overflow-y: auto;
            border-radius: var(--radius-xl);
            border: 1px solid #eadfcd;
            background: #ffffff;
            box-shadow: 0 24px 56px rgba(63, 45, 34, 0.32);
            padding: 22px;
            transform: translateY(12px) scale(0.98);
            transition: transform 0.24s ease;
        }

        .tour-request-overlay.is-open .tour-request-modal {
            transform: translateY(0) scale(1);
        }

        .tour-request-title {
            color: var(--brown-900);
            font-size: clamp(1.4rem, 2vw, 1.8rem);
            font-weight: 700;
            line-height: 1.25;
        }

        .tour-request-subtitle {
            margin-top: 4px;
            font-size: 13px;
            color: #7a6a58;
        }

        .tour-request-form {
            margin-top: 16px;
            display: grid;
            gap: 12px;
        }

        .tour-request-grid-2 {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .tour-request-field {
            display: grid;
            gap: 6px;
        }

        .tour-request-label {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: #6b5a49;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .tour-request-control {
            width: 100%;
            min-height: 44px;
            border-radius: var(--radius-lg);
            border: 1px solid #deceb7;
            background: #fffcf6;
            color: #4f4134;
            font-size: 14px;
            padding: 10px 12px;
            outline: none;
            transition: border-color 0.16s ease, box-shadow 0.16s ease;
        }

        .tour-request-control:focus {
            border-color: #b9915f;
            box-shadow: 0 0 0 3px rgba(185, 145, 95, 0.2);
        }

        .tour-request-control::placeholder {
            color: #a08f7c;
        }

        .tour-request-textarea {
            min-height: 120px;
            resize: vertical;
        }

        .tour-request-chip-wrap {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .tour-request-chip {
            border-radius: 999px;
            border: 1px solid #d8c7ad;
            background: #fff7ea;
            color: #5f503f;
            font-size: 12px;
            font-weight: 600;
            padding: 7px 12px;
            transition: all 0.18s ease;
        }

        .tour-request-chip:hover {
            border-color: #b9915f;
            color: #4f4134;
        }

        .tour-request-chip.is-selected {
            border-color: #6f8e52;
            background: #edf4e6;
            color: #385032;
        }

        .tour-request-feedback {
            display: none;
            border-radius: 12px;
            padding: 10px 12px;
            font-size: 13px;
            line-height: 1.4;
        }

        .tour-request-feedback.is-error {
            display: block;
            border: 1px solid #f2b8b1;
            background: #fdeeed;
            color: #8d2d21;
        }

        .tour-request-feedback.is-success {
            display: block;
            border: 1px solid #bcd9b8;
            background: #edf8ec;
            color: #2e5b2a;
        }

        .tour-request-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding-top: 6px;
        }

        .tour-request-cancel {
            border: 1px solid transparent;
            background: transparent;
            color: var(--brown-700);
            font-size: 14px;
            font-weight: 700;
            padding: 10px 14px;
            border-radius: var(--radius-lg);
            transition: background-color 0.16s ease;
        }

        .tour-request-cancel:hover {
            background: #f6efe3;
        }

        .tour-request-submit {
            border: 1px solid var(--brown-800);
            background: var(--brown-800);
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
            padding: 10px 18px;
            border-radius: var(--radius-lg);
            transition: background-color 0.16s ease, transform 0.16s ease;
        }

        .tour-request-submit:hover:not(:disabled) {
            background: #4a3b33;
            transform: translateY(-1px);
        }

        .tour-request-submit:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }

        @media (max-width: 768px) {
            .tour-request-modal {
                padding: 16px;
            }

            .tour-request-grid-2 {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 560px) {
            .tour-request-actions {
                flex-direction: column;
            }

            .tour-request-cancel,
            .tour-request-submit {
                width: 100%;
            }
        }
    </style>
</head>

<body class="min-h-screen bg-[#f5efe2] font-inter text-[#3e2a1f]"
    x-data="myPostsPage({
        currentUserId: @js((int) auth()->id()),
        posts: @js($bootstrapPosts),
        demoPosts: @js($demoPosts),
        stats: @js($stats),
        conversations: @js($bootstrapConversations),
        showUrlTemplate: @js($showUrlTemplate),
        storeUrlTemplate: @js($storeUrlTemplate),
        readUrlTemplate: @js($readUrlTemplate),
        updateUrlTemplate: @js(route('dashboard.my-posts.update', ['touristRequest' => '__post__'])),
        deleteUrlTemplate: @js(route('dashboard.my-posts.delete', ['touristRequest' => '__post__'])),
        completeUrlTemplate: @js(route('dashboard.my-posts.complete', ['touristRequest' => '__post__'])),
        commentsUrlTemplate: @js(route('dashboard.my-posts.comments', ['touristRequest' => '__post__'])),
        storeCommentUrlTemplate: @js(route('dashboard.my-posts.comments.store', ['touristRequest' => '__post__'])),
        selectGuideUrlTemplate: @js(route('dashboard.my-posts.comments.select-guide', ['touristRequest' => '__post__', 'comment' => '__comment__'])),
        reportCommentUrlTemplate: @js(route('dashboard.my-posts.comments.report', ['touristRequest' => '__post__', 'comment' => '__comment__'])),
        requestStoreUrl: @js(route('requests.store')),
        csrfToken: @js(csrf_token()),
    })" x-init="init()">

    <div x-data="{ scrolled: false, menuOpen: false }" x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 32; })">
        <header :class="scrolled ? 'nav-shell scrolled' : 'nav-shell'" class="fixed inset-x-0 top-0 z-50 border-b border-[#ddd2bd]">
            <div class="mx-auto flex max-w-[1260px] items-center justify-between px-6 py-4">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                    <img src="{{ asset('images/tribaltours_icon.png') }}" alt="TrblTours Icon" class="h-9 w-9 rounded-full border border-[#d3c5aa]/70 bg-[#f7efdd] object-cover">
                    <span class="font-[Asimovian] text-[26px] font-bold leading-none tracking-wide text-[#5a4a39]">TrblTours</span>
                </a>

                <nav class="hidden items-center gap-10 md:flex">
                    <a href="{{ route('dashboard.tourist') }}" class="top-nav-link">Explore Tours</a>
                    <a href="{{ route('dashboard.my-posts') }}" class="top-nav-link active">My Posts</a>
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
                        <a href="{{ route('dashboard.messages') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-[#5a3c2a] transition hover:bg-[#f7ecd7]">
                            <i class="fa-solid fa-envelope w-4 text-center"></i>
                            <span>Messages</span>
                        </a>
                        <a href="{{ route('dashboard.my-bookings') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-[#5a3c2a] transition hover:bg-[#f7ecd7]">
                            <i class="fa-solid fa-calendar-check w-4 text-center"></i>
                            <span>Bookings</span>
                        </a>
                        <a href="{{ route('dashboard.likes') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-[#5a3c2a] transition hover:bg-[#f7ecd7]">
                            <i class="fa-solid fa-heart w-4 text-center"></i>
                            <span>Likes</span>
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
    </div>

    <main class="mx-auto max-w-[1260px] px-4 pb-28 pt-[118px] sm:px-6 lg:px-8">
        <section class="rounded-3xl border border-[#ddcfb5] bg-gradient-to-br from-[#fff8eb] via-[#fffdf6] to-[#f4e7d1] p-6 shadow-[0_20px_45px_-28px_rgba(63,45,34,0.65)] sm:p-8">
            <h1 class="text-2xl font-extrabold tracking-tight text-[#3f2d22] sm:text-3xl">My Tour Requests</h1>
            <p class="mt-2 max-w-2xl text-sm text-[#6d5b50] sm:text-base">Manage your travel requests, compare guide offers, and coordinate with your selected guide.</p>

            <div class="mt-6 grid grid-cols-2 gap-3 md:grid-cols-4">
                <article class="rounded-2xl border border-[#eadbc0] bg-white/90 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.08em] text-[#8f7a65]">Total Requests</p>
                    <p class="mt-2 text-2xl font-extrabold text-[#3f2d22]" x-text="stats.total_requests"></p>
                </article>
                <article class="rounded-2xl border border-[#eadbc0] bg-white/90 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.08em] text-[#8f7a65]">Open Requests</p>
                    <p class="mt-2 text-2xl font-extrabold text-[#3f2d22]" x-text="stats.open_requests"></p>
                </article>
                <article class="rounded-2xl border border-[#eadbc0] bg-white/90 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.08em] text-[#8f7a65]">Selected Guides</p>
                    <p class="mt-2 text-2xl font-extrabold text-[#3f2d22]" x-text="stats.selected_guides"></p>
                </article>
                <article class="rounded-2xl border border-[#eadbc0] bg-white/90 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.08em] text-[#8f7a65]">Completed Trips</p>
                    <p class="mt-2 text-2xl font-extrabold text-[#3f2d22]" x-text="stats.completed_trips"></p>
                </article>
            </div>
        </section>

        <section class="mt-7 space-y-4">
            <template x-if="postsLoading">
                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                    <template x-for="item in [1,2,3,4]" :key="item">
                        <article class="animate-pulse rounded-2xl border border-[#e2d4ba] bg-[#fffaf1] p-5">
                            <div class="h-5 w-1/2 rounded bg-[#ebdfcc]"></div>
                            <div class="mt-3 h-4 w-2/3 rounded bg-[#f0e5d3]"></div>
                            <div class="mt-4 grid grid-cols-2 gap-2">
                                <div class="h-10 rounded bg-[#f3e9d8]"></div>
                                <div class="h-10 rounded bg-[#f3e9d8]"></div>
                                <div class="h-10 rounded bg-[#f3e9d8]"></div>
                                <div class="h-10 rounded bg-[#f3e9d8]"></div>
                            </div>
                        </article>
                    </template>
                </div>
            </template>

            <template x-if="!postsLoading && posts.length === 0">
                <div class="rounded-2xl border border-dashed border-[#d8c9af] bg-[#fff8ec] p-8 text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-[#efe2cb] text-[#7a654f]">
                        <i class="fa-solid fa-map-location-dot text-xl"></i>
                    </div>
                    <h2 class="mt-4 text-xl font-bold text-[#3f2d22]">No request posts yet</h2>
                    <p class="mx-auto mt-2 max-w-xl text-sm text-[#6f5d52]">Tap Create Tour Request to publish your first post. Guides can offer public proposals, then you can select one and open private chat instantly.</p>

                    <div class="mt-6 grid gap-3 md:grid-cols-2">
                        <template x-for="(demo, idx) in demoPosts" :key="`demo-${idx}`">
                            <article class="rounded-xl border border-[#e8dac0] bg-white/85 p-4 text-left">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-sm font-bold text-[#3f2d22]" x-text="demo.title"></p>
                                    <span class="status-pill bg-[#f1e6d4] text-[#6a5644]">Demo</span>
                                </div>
                                <p class="mt-2 text-xs text-[#7c6a59]">Budget: ₱<span x-text="Number(demo.budget || 0).toLocaleString()"></span></p>
                                <p class="mt-1 text-xs text-[#7c6a59]" x-show="demo.preferred_date">Preferred Date: <span x-text="demo.preferred_date"></span></p>
                                <div class="mt-2 space-y-1 text-xs text-[#5e4d3f]">
                                    <template x-for="offer in demo.offers" :key="offer">
                                        <p>• <span x-text="offer"></span></p>
                                    </template>
                                </div>
                            </article>
                        </template>
                    </div>
                </div>
            </template>

            <template x-if="!postsLoading && posts.length > 0">
                <div class="space-y-4">
                    <template x-for="post in posts" :key="`post-${post.id}`">
                        <article class="overflow-hidden rounded-2xl border border-[#ddceb3] bg-[#fffdf8] shadow-[0_16px_35px_-30px_rgba(63,45,34,0.75)]">
                            <div class="p-5 sm:p-6">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <h2 class="text-lg font-bold text-[#3f2d22]" x-text="post.title"></h2>
                                        <p class="mt-1 text-sm text-[#726051]">
                                            <span x-text="post.destination"></span>
                                            <span x-show="post.region"> • <span x-text="post.region"></span></span>
                                        </p>
                                    </div>
                                    <span class="status-pill" :class="statusClass(post.status)" x-text="post.status_label"></span>
                                </div>

                                <dl class="mt-4 grid grid-cols-2 gap-3 text-sm sm:grid-cols-4">
                                    <div class="rounded-xl border border-[#e8dbc3] bg-[#fff8ec] p-2.5">
                                        <dt class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[#7f6b57]">Duration</dt>
                                        <dd class="mt-1 font-semibold text-[#4f3d31]" x-text="post.duration || 'Flexible'"></dd>
                                    </div>
                                    <div class="rounded-xl border border-[#e8dbc3] bg-[#fff8ec] p-2.5">
                                        <dt class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[#7f6b57]">Budget</dt>
                                        <dd class="mt-1 font-semibold text-[#4f3d31]">₱ <span x-text="Number(post.budget || 0).toLocaleString()"></span></dd>
                                    </div>
                                    <div class="rounded-xl border border-[#e8dbc3] bg-[#fff8ec] p-2.5">
                                        <dt class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[#7f6b57]">Travelers</dt>
                                        <dd class="mt-1 font-semibold text-[#4f3d31]"><span x-text="post.adults"></span> Adults / <span x-text="post.children"></span> Children</dd>
                                    </div>
                                    <div class="rounded-xl border border-[#e8dbc3] bg-[#fff8ec] p-2.5">
                                        <dt class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[#7f6b57]">Preferred Date</dt>
                                        <dd class="mt-1 font-semibold text-[#4f3d31]" x-text="post.preferred_date ? formatDate(post.preferred_date) : 'Flexible'"></dd>
                                    </div>
                                </dl>

                                <div class="mt-4">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[#7f6b57]">Interests</p>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        <template x-for="tag in post.interests" :key="`${post.id}-${tag}`">
                                            <span class="rounded-full border border-[#d8c8ac] bg-[#f8eedc] px-2.5 py-1 text-xs font-semibold text-[#5f4d3f]" x-text="tag"></span>
                                        </template>
                                        <span x-show="post.interests.length === 0" class="text-xs text-[#8b7763]">No tags</span>
                                    </div>
                                </div>

                                <p class="mt-4 line-clamp-2 text-sm text-[#6f5d52]" x-text="post.description"></p>

                                <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                                    <p class="text-xs text-[#8a7662]">Posted: <span x-text="formatDateTime(post.posted_at)"></span></p>
                                    <p x-show="post.selected_guide_name" class="text-xs font-semibold text-[#4f7740]">Selected Guide: <span x-text="post.selected_guide_name"></span></p>
                                </div>

                                <div class="mt-4 flex flex-wrap gap-2">
                                    <button type="button" @click="toggleComments(post.id)" class="inline-flex items-center rounded-full border border-[#d8c7a9] bg-[#fff4df] px-3 py-1.5 text-xs font-semibold text-[#5f4a36] transition hover:bg-[#f4e4ca]">
                                        <i class="fa-regular fa-comments mr-1"></i>
                                        View Comments (<span x-text="commentsCount(post.id)"></span>)
                                    </button>
                                    <button type="button" @click="openEdit(post)" class="inline-flex items-center rounded-full border border-[#d5c8af] bg-[#fffaf2] px-3 py-1.5 text-xs font-semibold text-[#5e4a39] transition hover:bg-[#f6ecd9]">
                                        <i class="fa-regular fa-pen-to-square mr-1"></i>
                                        Edit
                                    </button>
                                    <button type="button" @click="cancelRequest(post.id)" class="inline-flex items-center rounded-full border border-[#e0b5ba] bg-[#fff2f4] px-3 py-1.5 text-xs font-semibold text-[#8a3642] transition hover:bg-[#ffe7eb]">
                                        <i class="fa-regular fa-trash-can mr-1"></i>
                                        Delete
                                    </button>
                                    <button type="button" @click="markComplete(post.id)" :disabled="post.status === 'completed' || post.status === 'cancelled'" class="inline-flex items-center rounded-full border border-[#4f7740] bg-[#5f8a46] px-3 py-1.5 text-xs font-semibold text-[#f6fff2] transition hover:bg-[#4f7740] disabled:cursor-not-allowed disabled:opacity-50">
                                        <i class="fa-solid fa-check mr-1"></i>
                                        Mark Complete
                                    </button>
                                </div>
                            </div>

                            <section x-cloak x-show="expandedPostId === post.id" x-transition class="border-t border-[#ecdfc8] bg-[#fffaf1] p-4 sm:p-5">
                                <div class="grid gap-4">
                                    <div>
                                        <div class="mb-3 flex items-center justify-between">
                                            <h3 class="text-sm font-bold uppercase tracking-[0.08em] text-[#5f4a37]">Public Negotiation</h3>
                                            <span class="text-xs text-[#7c6a58]">Offers and discussion are stored for transparency.</span>
                                        </div>

                                        <template x-if="commentsLoading[post.id]">
                                            <div class="space-y-3">
                                                <template x-for="item in [1,2,3]" :key="`loading-${item}`">
                                                    <div class="animate-pulse rounded-xl border border-[#eadcc4] bg-white p-3">
                                                        <div class="h-4 w-1/3 rounded bg-[#f1e8d9]"></div>
                                                        <div class="mt-2 h-3 w-2/3 rounded bg-[#f1e8d9]"></div>
                                                        <div class="mt-2 h-3 w-full rounded bg-[#f1e8d9]"></div>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>

                                        <div class="space-y-3" x-show="!commentsLoading[post.id]">
                                            <template x-if="(commentsByPost[post.id] || []).length === 0">
                                                <p class="rounded-xl border border-dashed border-[#dac9af] bg-[#fff7eb] p-3 text-sm text-[#7b6756]">No comments yet. Guides can post availability, offers, and itinerary ideas here.</p>
                                            </template>

                                            <template x-for="comment in commentsByPost[post.id] || []" :key="`comment-${comment.id}`">
                                                <article class="rounded-xl border border-[#e3d4bc] bg-white p-3">
                                                    <div class="flex items-start justify-between gap-3">
                                                        <div class="flex items-start gap-3">
                                                            <template x-if="comment.guide_avatar_url">
                                                                <img :src="comment.guide_avatar_url" alt="Guide avatar" class="h-9 w-9 rounded-full border border-[#d7c8ad] object-cover">
                                                            </template>
                                                            <template x-if="!comment.guide_avatar_url">
                                                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-[#d7c8ad] bg-[#f4e7d1] text-xs font-bold text-[#5f4a36]" x-text="initials(comment.guide_name)"></span>
                                                            </template>
                                                            <div>
                                                                <div class="flex flex-wrap items-center gap-2">
                                                                    <p class="text-sm font-bold text-[#3f2d22]" x-text="comment.guide_name"></p>
                                                                    <span x-show="comment.is_verified" class="rounded-full bg-[#eef4e6] px-2 py-0.5 text-[10px] font-bold uppercase tracking-[0.06em] text-[#4f7740]">Verified</span>
                                                                    <span x-show="comment.rating" class="inline-flex items-center text-[11px] font-semibold text-[#8a6f4f]"><i class="fa-solid fa-star mr-1 text-[#d4a563]"></i><span x-text="comment.rating"></span></span>
                                                                </div>
                                                                <p class="mt-1 text-sm text-[#654f3f]" x-text="comment.message"></p>
                                                                <p x-show="comment.offered_price !== null" class="mt-1 text-xs font-bold text-[#3f2d22]">Offered Price: ₱<span x-text="Number(comment.offered_price || 0).toLocaleString()"></span></p>
                                                                <p class="mt-1 text-[11px] text-[#8a7764]" x-text="formatDateTime(comment.timestamp)"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-2 flex flex-wrap gap-2">
                                                        <button type="button"
                                                            x-show="comment.role === 'guide'"
                                                            @click="selectGuide(post.id, comment.id)"
                                                            :disabled="post.selected_guide_id && Number(post.selected_guide_id) !== Number(comment.guide_id)"
                                                            class="inline-flex items-center rounded-full border border-[#4f7740] bg-[#5f8a46] px-2.5 py-1 text-[11px] font-semibold text-[#f7fff4] transition hover:bg-[#4f7740] disabled:cursor-not-allowed disabled:opacity-40">
                                                            Select Guide
                                                        </button>
                                                        <button type="button" @click="replyToComment(post.id, comment.id, comment.guide_name)" class="inline-flex items-center rounded-full border border-[#d8c7a9] bg-[#fff4df] px-2.5 py-1 text-[11px] font-semibold text-[#5f4a36] transition hover:bg-[#f4e4ca]">Reply</button>
                                                        <button type="button" @click="reportComment(post.id, comment.id)" class="inline-flex items-center rounded-full border border-[#e2b8bd] bg-[#fff2f4] px-2.5 py-1 text-[11px] font-semibold text-[#8a3642] transition hover:bg-[#ffe7eb]">Report</button>
                                                    </div>
                                                </article>
                                            </template>
                                        </div>

                                        <form @submit.prevent="submitComment(post.id)" class="mt-4 rounded-xl border border-[#dfceb3] bg-[#fffdf8] p-3">
                                            <label class="block text-xs font-semibold uppercase tracking-[0.08em] text-[#6f5d52]">Reply Publicly</label>
                                            <textarea :id="`comment-body-${post.id}`" x-model="commentDraftByPost[post.id]" rows="3" maxlength="1000" placeholder="Reply to offers, ask questions, or continue negotiation..." class="mt-2 w-full rounded-lg border border-[#dcc9ad] bg-white px-3 py-2 text-sm text-[#4d3d30] placeholder:text-[#9b8a79] focus:border-[#a98b64] focus:outline-none focus:ring-2 focus:ring-[#d8c3a2]"></textarea>
                                            <div class="mt-2 flex items-center justify-between gap-2">
                                                <p class="text-[11px] text-[#8a7764]">Comments are publicly visible under this request.</p>
                                                <button type="submit" class="inline-flex items-center rounded-full border border-[#4f7740] bg-[#5f8a46] px-3 py-1.5 text-xs font-semibold text-[#f7fff4] transition hover:bg-[#4f7740]">Post Comment</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </section>
                        </article>
                    </template>
                </div>
            </template>
        </section>
    </main>

    <button id="openRequestModal" class="fixed bottom-6 right-6 z-40 inline-flex min-h-[50px] items-center justify-center rounded-full border-2 bg-[#8f9d59] px-6 text-[16px] font-semibold text-[#ffffff] shadow-[0_10px_24px_rgba(61,47,31,0.16)] transition hover:bg-[#624f27] sm:min-h-[50px] sm:px-10 sm:text-[16px]">
        Create Tour Request
    </button>

    <div id="tourRequestModal" class="tour-request-overlay" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="tourRequestTitle">
        <section class="tour-request-modal">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 id="tourRequestTitle" class="tour-request-title">Create Your Request</h2>
                    <p class="tour-request-subtitle">Tell guides what kind of trip you are planning and get matched faster.</p>
                </div>
                <button type="button" class="tour-request-cancel p-1" data-close-tour-request aria-label="Close request modal">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <form id="tourRequestForm" class="tour-request-form" novalidate>
                @csrf
                <input type="hidden" name="interests" id="tourRequestInterests">

                <div id="tourRequestFeedback" class="tour-request-feedback" aria-live="polite"></div>

                <div class="tour-request-field">
                    <label class="tour-request-label" for="tourRequestInputTitle">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <span>Request Title</span>
                    </label>
                    <input id="tourRequestInputTitle" type="text" class="tour-request-control" placeholder="Enter request title" required>
                </div>

                <div class="tour-request-grid-2">
                    <div class="tour-request-field">
                        <label class="tour-request-label" for="tourRequestDuration">
                            <i class="fa-solid fa-clock"></i>
                            <span>Duration</span>
                        </label>
                        <input id="tourRequestDuration" type="text" class="tour-request-control" placeholder="3 Days">
                    </div>
                    <div class="tour-request-field">
                        <label class="tour-request-label" for="tourRequestBudget">
                            <i class="fa-solid fa-peso-sign"></i>
                            <span>Budget</span>
                        </label>
                        <input id="tourRequestBudget" type="text" inputmode="decimal" class="tour-request-control" placeholder="Php 5,000">
                    </div>
                </div>

                <div class="tour-request-grid-2">
                    <div class="tour-request-field">
                        <label class="tour-request-label" for="tourRequestRegion">
                            <i class="fa-solid fa-earth-asia"></i>
                            <span>Philippine Region</span>
                        </label>
                        <input id="tourRequestRegion" list="tourRequestRegionList" class="tour-request-control" placeholder="Search region">
                        <datalist id="tourRequestRegionList">
                            <option value="NCR - National Capital Region"></option>
                            <option value="CAR"></option>
                            <option value="Region I - Ilocos Region"></option>
                            <option value="Region II - Cagayan Valley"></option>
                            <option value="Region III - Central Luzon"></option>
                            <option value="Region IV-A - CALABARZON"></option>
                            <option value="Region IV-B - MIMAROPA"></option>
                            <option value="Region V - Bicol Region"></option>
                            <option value="Region VI - Western Visayas"></option>
                            <option value="Region VII - Central Visayas"></option>
                            <option value="Region VIII - Eastern Visayas"></option>
                            <option value="Region IX - Zamboanga Peninsula"></option>
                            <option value="Region X - Northern Mindanao"></option>
                            <option value="Region XI - Davao Region"></option>
                            <option value="Region XII - SOCCSKSARGEN"></option>
                            <option value="Region XIII - Caraga"></option>
                            <option value="BARMM"></option>
                        </datalist>
                    </div>
                    <div class="tour-request-field">
                        <label class="tour-request-label" for="tourRequestLocation">
                            <i class="fa-solid fa-location-dot"></i>
                            <span>Location / City / Province</span>
                        </label>
                        <input id="tourRequestLocation" type="text" class="tour-request-control" placeholder="Enter city, province, or destination" required>
                    </div>
                </div>

                <div class="tour-request-grid-2">
                    <div class="tour-request-field">
                        <label class="tour-request-label" for="tourRequestAdults">
                            <i class="fa-solid fa-users"></i>
                            <span>Adults</span>
                        </label>
                        <input id="tourRequestAdults" type="number" class="tour-request-control" min="0" max="20" value="1">
                    </div>
                    <div class="tour-request-field">
                        <label class="tour-request-label" for="tourRequestChildren">
                            <i class="fa-solid fa-child-reaching"></i>
                            <span>Children</span>
                        </label>
                        <input id="tourRequestChildren" type="number" class="tour-request-control" min="0" max="20" value="0">
                    </div>
                </div>

                <div class="tour-request-field">
                    <label class="tour-request-label" for="tourRequestDate">
                        <i class="fa-solid fa-calendar-days"></i>
                        <span>Preferred Date</span>
                    </label>
                    <input id="tourRequestDate" type="date" class="tour-request-control">
                </div>

                <div class="tour-request-field">
                    <label class="tour-request-label">
                        <i class="fa-solid fa-tags"></i>
                        <span>Interests</span>
                    </label>
                    <div class="tour-request-chip-wrap" id="tourRequestChips">
                        <button type="button" class="tour-request-chip" data-interest-chip="Food">Food</button>
                        <button type="button" class="tour-request-chip" data-interest-chip="Beach">Beach</button>
                        <button type="button" class="tour-request-chip" data-interest-chip="Trek">Trek</button>
                        <button type="button" class="tour-request-chip" id="tourRequestAddInterest">+ Add</button>
                    </div>
                </div>

                <div class="tour-request-field">
                    <label class="tour-request-label" for="tourRequestDescription">
                        <i class="fa-solid fa-book-open"></i>
                        <span>Description</span>
                    </label>
                    <textarea id="tourRequestDescription" class="tour-request-control tour-request-textarea" rows="5" placeholder="Describe your travel request..." required></textarea>
                </div>

                <div class="tour-request-actions">
                    <button type="button" class="tour-request-cancel" data-close-tour-request>Cancel</button>
                    <button type="submit" class="tour-request-submit">Post</button>
                </div>
            </form>
        </section>
    </div>

    <div class="pointer-events-none fixed right-4 top-24 z-[100] flex w-[min(360px,92vw)] flex-col gap-2">
        <template x-for="toast in toasts" :key="toast.id">
            <div class="pointer-events-auto rounded-xl border px-4 py-3 text-sm shadow-xl" :class="toast.type === 'error' ? 'border-[#e7b8bf] bg-[#fff0f2] text-[#7f2734]' : 'border-[#bcd9b8] bg-[#edf8ec] text-[#2e5b2a]'">
                <p x-text="toast.message"></p>
            </div>
        </template>
    </div>

    <div x-cloak x-show="showEditModal" x-transition class="fixed inset-0 z-[95] flex items-center justify-center bg-[#3f2d22]/55 p-4">
        <section class="max-h-[92vh] w-full max-w-2xl overflow-y-auto rounded-2xl border border-[#e2d3b8] bg-[#fffdf8] p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-xl font-bold text-[#3f2d22]">Edit Request Post</h2>
                    <p class="text-sm text-[#6f5d52]">Update details for your request card.</p>
                </div>
                <button type="button" @click="showEditModal = false" class="rounded-full border border-[#dbcdb3] bg-[#fff5e5] px-2.5 py-1.5 text-sm text-[#5f4a36] transition hover:bg-[#f4e4ca]">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form @submit.prevent="saveEdit" class="mt-4 grid gap-3 sm:grid-cols-2">
                <label class="grid gap-1 text-sm text-[#5f4a36] sm:col-span-2">
                    <span class="text-xs font-semibold uppercase tracking-[0.08em] text-[#6f5d52]">Title</span>
                    <input type="text" x-model="editForm.title" required class="rounded-lg border border-[#dcc9ad] bg-white px-3 py-2 text-sm focus:border-[#a98b64] focus:outline-none focus:ring-2 focus:ring-[#d8c3a2]">
                </label>
                <label class="grid gap-1 text-sm text-[#5f4a36]">
                    <span class="text-xs font-semibold uppercase tracking-[0.08em] text-[#6f5d52]">Location</span>
                    <input type="text" x-model="editForm.location" required class="rounded-lg border border-[#dcc9ad] bg-white px-3 py-2 text-sm focus:border-[#a98b64] focus:outline-none focus:ring-2 focus:ring-[#d8c3a2]">
                </label>
                <label class="grid gap-1 text-sm text-[#5f4a36]">
                    <span class="text-xs font-semibold uppercase tracking-[0.08em] text-[#6f5d52]">Region</span>
                    <input type="text" x-model="editForm.region" class="rounded-lg border border-[#dcc9ad] bg-white px-3 py-2 text-sm focus:border-[#a98b64] focus:outline-none focus:ring-2 focus:ring-[#d8c3a2]">
                </label>
                <label class="grid gap-1 text-sm text-[#5f4a36]">
                    <span class="text-xs font-semibold uppercase tracking-[0.08em] text-[#6f5d52]">Duration</span>
                    <input type="text" x-model="editForm.duration" class="rounded-lg border border-[#dcc9ad] bg-white px-3 py-2 text-sm focus:border-[#a98b64] focus:outline-none focus:ring-2 focus:ring-[#d8c3a2]">
                </label>
                <label class="grid gap-1 text-sm text-[#5f4a36]">
                    <span class="text-xs font-semibold uppercase tracking-[0.08em] text-[#6f5d52]">Preferred Date</span>
                    <input type="date" x-model="editForm.preferred_date" required class="rounded-lg border border-[#dcc9ad] bg-white px-3 py-2 text-sm focus:border-[#a98b64] focus:outline-none focus:ring-2 focus:ring-[#d8c3a2]">
                </label>
                <label class="grid gap-1 text-sm text-[#5f4a36]">
                    <span class="text-xs font-semibold uppercase tracking-[0.08em] text-[#6f5d52]">Budget</span>
                    <input type="number" min="500" x-model="editForm.budget" required class="rounded-lg border border-[#dcc9ad] bg-white px-3 py-2 text-sm focus:border-[#a98b64] focus:outline-none focus:ring-2 focus:ring-[#d8c3a2]">
                </label>
                <label class="grid gap-1 text-sm text-[#5f4a36]">
                    <span class="text-xs font-semibold uppercase tracking-[0.08em] text-[#6f5d52]">Adults</span>
                    <input type="number" min="0" max="20" x-model="editForm.adults" class="rounded-lg border border-[#dcc9ad] bg-white px-3 py-2 text-sm focus:border-[#a98b64] focus:outline-none focus:ring-2 focus:ring-[#d8c3a2]">
                </label>
                <label class="grid gap-1 text-sm text-[#5f4a36]">
                    <span class="text-xs font-semibold uppercase tracking-[0.08em] text-[#6f5d52]">Children</span>
                    <input type="number" min="0" max="20" x-model="editForm.children" class="rounded-lg border border-[#dcc9ad] bg-white px-3 py-2 text-sm focus:border-[#a98b64] focus:outline-none focus:ring-2 focus:ring-[#d8c3a2]">
                </label>
                <label class="grid gap-1 text-sm text-[#5f4a36] sm:col-span-2">
                    <span class="text-xs font-semibold uppercase tracking-[0.08em] text-[#6f5d52]">Interests (comma-separated)</span>
                    <input type="text" x-model="editForm.interests" class="rounded-lg border border-[#dcc9ad] bg-white px-3 py-2 text-sm focus:border-[#a98b64] focus:outline-none focus:ring-2 focus:ring-[#d8c3a2]">
                </label>
                <label class="grid gap-1 text-sm text-[#5f4a36] sm:col-span-2">
                    <span class="text-xs font-semibold uppercase tracking-[0.08em] text-[#6f5d52]">Description</span>
                    <textarea rows="4" maxlength="1000" x-model="editForm.description" required class="rounded-lg border border-[#dcc9ad] bg-white px-3 py-2 text-sm focus:border-[#a98b64] focus:outline-none focus:ring-2 focus:ring-[#d8c3a2]"></textarea>
                </label>

                <div class="flex justify-end gap-2 sm:col-span-2">
                    <button type="button" @click="showEditModal = false" class="rounded-full border border-[#dbcdb3] bg-[#fff5e5] px-4 py-2 text-sm font-semibold text-[#5f4a36] transition hover:bg-[#f4e4ca]">Cancel</button>
                    <button type="submit" :disabled="editSaving" class="rounded-full border border-[#4f7740] bg-[#5f8a46] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#4f7740] disabled:cursor-not-allowed disabled:opacity-50" x-text="editSaving ? 'Saving...' : 'Save Changes'"></button>
                </div>
            </form>
        </section>
    </div>

    <script>
        function myPostsPage(config) {
            return {
                currentUserId: Number(config.currentUserId),
                posts: Array.isArray(config.posts) ? config.posts : [],
                demoPosts: Array.isArray(config.demoPosts) ? config.demoPosts : [],
                stats: {
                    total_requests: Number(config.stats?.total_requests || 0),
                    open_requests: Number(config.stats?.open_requests || 0),
                    selected_guides: Number(config.stats?.selected_guides || 0),
                    completed_trips: Number(config.stats?.completed_trips || 0),
                },
                conversations: Array.isArray(config.conversations) ? config.conversations : [],
                activeConversation: null,
                selectedConversationId: null,
                messages: [],
                chatDraft: '',
                chatSending: false,
                typingIndicator: '',
                typingTimeoutHandle: null,
                onlineUserIds: [],
                expandedPostId: null,
                commentsByPost: {},
                commentsLoading: {},
                commentDraftByPost: {},
                replyTargetByPost: {},
                postsLoading: false,
                toasts: [],
                toastCounter: 0,
                showEditModal: false,
                editSaving: false,
                editForm: {
                    id: null,
                    title: '',
                    location: '',
                    region: '',
                    duration: '',
                    budget: 500,
                    adults: 1,
                    children: 0,
                    preferred_date: '',
                    interests: '',
                    description: '',
                },
                userChannelBound: false,
                activeConversationChannelId: null,
                activePresenceChannelId: null,
                lastTypingSentAt: 0,
                showUrlTemplate: config.showUrlTemplate,
                storeUrlTemplate: config.storeUrlTemplate,
                readUrlTemplate: config.readUrlTemplate,
                updateUrlTemplate: config.updateUrlTemplate,
                deleteUrlTemplate: config.deleteUrlTemplate,
                completeUrlTemplate: config.completeUrlTemplate,
                commentsUrlTemplate: config.commentsUrlTemplate,
                storeCommentUrlTemplate: config.storeCommentUrlTemplate,
                selectGuideUrlTemplate: config.selectGuideUrlTemplate,
                reportCommentUrlTemplate: config.reportCommentUrlTemplate,
                requestStoreUrl: config.requestStoreUrl,
                csrfToken: config.csrfToken,

                init() {
                    this.posts = this.posts.map((post) => this.normalizePost(post));
                    this.refreshStats();

                    this.subscribeUserChannel();

                    window.addEventListener('tour-request-created', (event) => {
                        if (!event.detail || !event.detail.post) {
                            return;
                        }

                        this.appendPost(event.detail.post);
                        this.pushToast('Request posted successfully.', 'success');
                    });
                },

                normalizePost(post) {
                    const normalizedInterests = Array.isArray(post.interests)
                        ? post.interests
                        : String(post.interests || '')
                            .split(',')
                            .map((item) => item.trim())
                            .filter((item) => item !== '');

                    return {
                        ...post,
                        id: Number(post.id),
                        adults: Number(post.adults || 0),
                        children: Number(post.children || 0),
                        budget: Number(post.budget || 0),
                        comments_count: Number(post.comments_count || 0),
                        interests: normalizedInterests,
                        status: post.status || 'open',
                        status_label: post.status_label || this.statusLabel(post.status || 'open'),
                    };
                },

                statusLabel(status) {
                    const map = {
                        open: 'Open',
                        negotiating: 'Negotiating',
                        guide_selected: 'Guide Selected',
                        completed: 'Completed',
                        cancelled: 'Cancelled',
                    };

                    return map[status] || 'Open';
                },

                statusClass(status) {
                    const map = {
                        open: 'bg-amber-100 text-amber-800',
                        negotiating: 'bg-blue-100 text-blue-800',
                        guide_selected: 'bg-emerald-100 text-emerald-800',
                        completed: 'bg-[#eef4e6] text-[#4f6d39]',
                        cancelled: 'bg-rose-100 text-rose-800',
                    };

                    return map[status] || map.open;
                },

                formatDate(value) {
                    if (!value) {
                        return 'N/A';
                    }

                    const date = new Date(value);

                    if (Number.isNaN(date.getTime())) {
                        return 'N/A';
                    }

                    return date.toLocaleDateString([], {
                        month: 'short',
                        day: 'numeric',
                        year: 'numeric',
                    });
                },

                formatDateTime(value) {
                    if (!value) {
                        return 'N/A';
                    }

                    const date = new Date(value);

                    if (Number.isNaN(date.getTime())) {
                        return 'N/A';
                    }

                    return date.toLocaleString([], {
                        month: 'short',
                        day: 'numeric',
                        year: 'numeric',
                        hour: 'numeric',
                        minute: '2-digit',
                    });
                },

                formatTime(value) {
                    if (!value) {
                        return '';
                    }

                    const date = new Date(value);

                    if (Number.isNaN(date.getTime())) {
                        return '';
                    }

                    return date.toLocaleTimeString([], {
                        hour: 'numeric',
                        minute: '2-digit',
                    });
                },

                initials(name) {
                    if (!name) {
                        return 'G';
                    }

                    const parts = String(name).trim().split(/\s+/).filter(Boolean);
                    const first = parts[0]?.[0] || '';
                    const second = parts[1]?.[0] || '';
                    return `${first}${second}`.toUpperCase() || 'G';
                },

                pushToast(message, type = 'success') {
                    const id = ++this.toastCounter;
                    this.toasts.push({
                        id,
                        message,
                        type,
                    });

                    window.setTimeout(() => {
                        this.toasts = this.toasts.filter((toast) => toast.id !== id);
                    }, 3000);
                },

                routeUrl(template, params = {}) {
                    let url = template;

                    Object.entries(params).forEach(([key, value]) => {
                        url = url.replace(`__${key}__`, String(value));
                    });

                    return url;
                },

                appendPost(post) {
                    const normalized = this.normalizePost(post);
                    this.posts.unshift(normalized);
                    this.refreshStats();
                },

                updatePostInState(post) {
                    const normalized = this.normalizePost(post);
                    const index = this.posts.findIndex((item) => Number(item.id) === Number(normalized.id));

                    if (index === -1) {
                        this.posts.unshift(normalized);
                    } else {
                        this.posts[index] = {
                            ...this.posts[index],
                            ...normalized,
                        };
                    }

                    this.refreshStats();
                },

                refreshStats() {
                    const total = this.posts.length;
                    const open = this.posts.filter((post) => ['open', 'negotiating'].includes(post.status)).length;
                    const selected = this.posts.filter((post) => post.status === 'guide_selected').length;
                    const completed = this.posts.filter((post) => post.status === 'completed').length;

                    this.stats.total_requests = total;
                    this.stats.open_requests = open;
                    this.stats.selected_guides = selected;
                    this.stats.completed_trips = completed;
                },

                commentsCount(postId) {
                    const cached = this.commentsByPost[postId];
                    if (Array.isArray(cached)) {
                        return cached.length;
                    }

                    const post = this.posts.find((item) => Number(item.id) === Number(postId));
                    return Number(post?.comments_count || 0);
                },

                async toggleComments(postId) {
                    const numericPostId = Number(postId);
                    this.expandedPostId = this.expandedPostId === numericPostId ? null : numericPostId;

                    if (this.expandedPostId === null) {
                        return;
                    }

                    if (!Array.isArray(this.commentsByPost[numericPostId])) {
                        await this.loadComments(numericPostId);
                    }
                },

                async loadComments(postId) {
                    this.commentsLoading[postId] = true;

                    try {
                        const response = await fetch(this.routeUrl(this.commentsUrlTemplate, {
                            post: postId,
                        }), {
                            headers: {
                                Accept: 'application/json',
                            },
                            credentials: 'same-origin',
                        });

                        if (!response.ok) {
                            throw new Error('Unable to load comments.');
                        }

                        const payload = await response.json();
                        this.commentsByPost[postId] = Array.isArray(payload.data) ? payload.data : [];

                        const post = this.posts.find((item) => Number(item.id) === Number(postId));
                        if (post) {
                            post.comments_count = this.commentsByPost[postId].length;
                        }
                    } catch (error) {
                        this.pushToast('Unable to load comments right now.', 'error');
                    } finally {
                        this.commentsLoading[postId] = false;
                    }
                },

                replyToComment(postId, commentId, guideName) {
                    this.replyTargetByPost[postId] = Number(commentId);
                    this.commentDraftByPost[postId] = `@${guideName} `;

                    this.$nextTick(() => {
                        const input = document.getElementById(`comment-body-${postId}`);
                        if (input) {
                            input.focus();
                        }
                    });
                },

                async submitComment(postId) {
                    const body = String(this.commentDraftByPost[postId] || '').trim();

                    if (body === '') {
                        this.pushToast('Please write a comment first.', 'error');
                        return;
                    }

                    const payload = {
                        body,
                    };

                    if (this.replyTargetByPost[postId]) {
                        payload.parent_id = Number(this.replyTargetByPost[postId]);
                    }

                    try {
                        const response = await fetch(this.routeUrl(this.storeCommentUrlTemplate, {
                            post: postId,
                        }), {
                            method: 'POST',
                            headers: {
                                Accept: 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                            },
                            body: JSON.stringify(payload),
                            credentials: 'same-origin',
                        });

                        const result = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            throw new Error(result.message || 'Failed to post comment.');
                        }

                        if (!Array.isArray(this.commentsByPost[postId])) {
                            this.commentsByPost[postId] = [];
                        }

                        this.commentsByPost[postId].push(result.data);
                        this.commentDraftByPost[postId] = '';
                        this.replyTargetByPost[postId] = null;

                        const post = this.posts.find((item) => Number(item.id) === Number(postId));
                        if (post) {
                            post.comments_count = this.commentsByPost[postId].length;
                            if (result.post_status) {
                                post.status = result.post_status;
                                post.status_label = this.statusLabel(result.post_status);
                            }
                        }

                        this.refreshStats();
                        this.pushToast('Public comment posted.', 'success');
                    } catch (error) {
                        this.pushToast(error.message || 'Unable to post comment.', 'error');
                    }
                },

                async selectGuide(postId, commentId) {
                    const post = this.posts.find((item) => Number(item.id) === Number(postId));

                    if (!post) {
                        return;
                    }

                    if (!window.confirm('Select this guide and open private chat?')) {
                        return;
                    }

                    try {
                        const response = await fetch(this.routeUrl(this.selectGuideUrlTemplate, {
                            post: postId,
                            comment: commentId,
                        }), {
                            method: 'POST',
                            headers: {
                                Accept: 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                            },
                            credentials: 'same-origin',
                        });

                        const result = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            throw new Error(result.message || 'Unable to select guide.');
                        }

                        if (result.post) {
                            this.updatePostInState(result.post);
                        }

                        if (result.conversation && result.redirect_url) {
                            window.location.assign(result.redirect_url);
                            return;
                        }

                        if (result.conversation) {
                            this.upsertConversation(result.conversation);
                            await this.openChat(Number(result.conversation.id), result.messages || []);
                        }

                        this.pushToast(result.message || 'Guide selected successfully.', 'success');
                    } catch (error) {
                        this.pushToast(error.message || 'Unable to select guide right now.', 'error');
                    }
                },

                async reportComment(postId, commentId) {
                    const reason = window.prompt('Reason for reporting this comment (optional):', 'Suspicious offer');

                    try {
                        const response = await fetch(this.routeUrl(this.reportCommentUrlTemplate, {
                            post: postId,
                            comment: commentId,
                        }), {
                            method: 'POST',
                            headers: {
                                Accept: 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                            },
                            body: JSON.stringify({
                                reason,
                            }),
                            credentials: 'same-origin',
                        });

                        if (!response.ok) {
                            throw new Error('Unable to report comment.');
                        }

                        this.pushToast('Comment reported for review.', 'success');
                    } catch (error) {
                        this.pushToast(error.message || 'Unable to report this comment.', 'error');
                    }
                },

                openEdit(post) {
                    this.editForm = {
                        id: Number(post.id),
                        title: post.title || '',
                        location: post.destination || '',
                        region: post.region || '',
                        duration: post.duration || '',
                        budget: Number(post.budget || 500),
                        adults: Number(post.adults || 0),
                        children: Number(post.children || 0),
                        preferred_date: post.preferred_date || '',
                        interests: Array.isArray(post.interests) ? post.interests.join(', ') : '',
                        description: post.description || '',
                    };

                    this.showEditModal = true;
                },

                async saveEdit() {
                    if (!this.editForm.id) {
                        return;
                    }

                    this.editSaving = true;

                    try {
                        const payload = {
                            title: this.editForm.title,
                            location: this.editForm.location,
                            region: this.editForm.region,
                            duration: this.editForm.duration,
                            budget_min: Number(this.editForm.budget || 500),
                            budget_max: Number(this.editForm.budget || 500),
                            preferred_date: this.editForm.preferred_date,
                            adults: Number(this.editForm.adults || 0),
                            children: Number(this.editForm.children || 0),
                            interests: this.editForm.interests,
                            description: this.editForm.description,
                        };

                        const response = await fetch(this.routeUrl(this.updateUrlTemplate, {
                            post: this.editForm.id,
                        }), {
                            method: 'PATCH',
                            headers: {
                                Accept: 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                            },
                            body: JSON.stringify(payload),
                            credentials: 'same-origin',
                        });

                        const result = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            throw new Error(result.message || 'Unable to save changes.');
                        }

                        this.updatePostInState(result.data);
                        this.showEditModal = false;
                        this.pushToast(result.message || 'Post updated successfully.', 'success');
                    } catch (error) {
                        this.pushToast(error.message || 'Unable to update post.', 'error');
                    } finally {
                        this.editSaving = false;
                    }
                },

                async cancelRequest(postId) {
                    if (!window.confirm('Delete this request? This action will mark it as cancelled.')) {
                        return;
                    }

                    try {
                        const response = await fetch(this.routeUrl(this.deleteUrlTemplate, {
                            post: postId,
                        }), {
                            method: 'DELETE',
                            headers: {
                                Accept: 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                            },
                            credentials: 'same-origin',
                        });

                        const result = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            throw new Error(result.message || 'Unable to delete post.');
                        }

                        if (result.data) {
                            this.updatePostInState(result.data);
                        }

                        this.pushToast(result.message || 'Request cancelled successfully.', 'success');
                    } catch (error) {
                        this.pushToast(error.message || 'Unable to cancel request.', 'error');
                    }
                },

                async markComplete(postId) {
                    if (!window.confirm('Mark this request as completed?')) {
                        return;
                    }

                    try {
                        const response = await fetch(this.routeUrl(this.completeUrlTemplate, {
                            post: postId,
                        }), {
                            method: 'POST',
                            headers: {
                                Accept: 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                            },
                            credentials: 'same-origin',
                        });

                        const result = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            throw new Error(result.message || 'Unable to complete request.');
                        }

                        if (result.data) {
                            this.updatePostInState(result.data);
                        }

                        this.pushToast(result.message || 'Trip marked as completed.', 'success');
                    } catch (error) {
                        this.pushToast(error.message || 'Unable to mark complete.', 'error');
                    }
                },

                upsertConversation(conversation) {
                    if (!conversation || !conversation.id) {
                        return;
                    }

                    const index = this.conversations.findIndex((item) => Number(item.id) === Number(conversation.id));

                    if (index === -1) {
                        this.conversations.unshift(conversation);
                    } else {
                        this.conversations[index] = {
                            ...this.conversations[index],
                            ...conversation,
                        };
                        this.moveConversationToTop(conversation.id);
                    }
                },

                moveConversationToTop(conversationId) {
                    const index = this.conversations.findIndex((item) => Number(item.id) === Number(conversationId));

                    if (index <= 0) {
                        return;
                    }

                    const [conversation] = this.conversations.splice(index, 1);
                    this.conversations.unshift(conversation);
                },

                async openChat(conversationId, preloadedMessages = null) {
                    this.selectedConversationId = Number(conversationId);

                    const selected = this.conversations.find((item) => Number(item.id) === Number(conversationId));
                    this.activeConversation = selected || null;

                    if (Array.isArray(preloadedMessages)) {
                        this.messages = preloadedMessages;
                    } else {
                        await this.fetchConversation(conversationId);
                    }

                    await this.markConversationRead(conversationId);
                    this.subscribeConversationChannels(conversationId);

                    if (selected) {
                        selected.unread_count = 0;
                    }

                    this.$nextTick(() => this.scrollChatToBottom());
                },

                async fetchConversation(conversationId) {
                    try {
                        const response = await fetch(this.routeUrl(this.showUrlTemplate, {
                            conversation: conversationId,
                        }), {
                            headers: {
                                Accept: 'application/json',
                            },
                            credentials: 'same-origin',
                        });

                        if (!response.ok) {
                            throw new Error('Unable to open chat.');
                        }

                        const payload = await response.json();

                        this.messages = Array.isArray(payload.messages) ? payload.messages : [];
                        if (payload.conversation) {
                            this.upsertConversation(payload.conversation);
                            const selected = this.conversations.find((item) => Number(item.id) === Number(conversationId));
                            this.activeConversation = selected || this.activeConversation;
                        }
                    } catch (error) {
                        this.pushToast(error.message || 'Unable to load messages.', 'error');
                    }
                },

                async sendChatMessage() {
                    if (this.chatSending || !this.activeConversation || !this.chatDraft.trim()) {
                        return;
                    }

                    this.chatSending = true;

                    try {
                        const response = await fetch(this.routeUrl(this.storeUrlTemplate, {
                            conversation: this.activeConversation.id,
                        }), {
                            method: 'POST',
                            headers: {
                                Accept: 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                            },
                            body: JSON.stringify({
                                body: this.chatDraft.trim(),
                            }),
                            credentials: 'same-origin',
                        });

                        const payload = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            throw new Error(payload.message || 'Unable to send message.');
                        }

                        if (payload.message && !this.messages.some((message) => Number(message.id) === Number(payload.message.id))) {
                            this.messages.push(payload.message);
                        }

                        if (payload.conversation) {
                            this.upsertConversation(payload.conversation);
                        }

                        this.chatDraft = '';
                        this.typingIndicator = '';

                        this.$nextTick(() => this.scrollChatToBottom());
                    } catch (error) {
                        this.pushToast(error.message || 'Unable to send chat message.', 'error');
                    } finally {
                        this.chatSending = false;
                    }
                },

                async markConversationRead(conversationId) {
                    try {
                        await fetch(this.routeUrl(this.readUrlTemplate, {
                            conversation: conversationId,
                        }), {
                            method: 'POST',
                            headers: {
                                Accept: 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                            },
                            credentials: 'same-origin',
                        });
                    } catch (error) {
                        // Keep quiet; read status can retry on next refresh.
                    }
                },

                notifyTyping() {
                    if (!this.selectedConversationId || !window.Echo) {
                        return;
                    }

                    const now = Date.now();
                    if (now - this.lastTypingSentAt < 700) {
                        return;
                    }

                    this.lastTypingSentAt = now;

                    const channel = window.Echo.private(`messaging.conversation.${this.selectedConversationId}`);
                    channel.whisper('typing', {
                        user_id: this.currentUserId,
                        is_typing: true,
                    });
                },

                subscribeUserChannel() {
                    if (!window.Echo || this.userChannelBound) {
                        return;
                    }

                    window.Echo.private(`messaging.user.${this.currentUserId}`)
                        .listen('.message.sent', (event) => {
                            this.handleIncomingMessage(event);
                        })
                        .listen('.conversation.read', (event) => {
                            this.handleConversationRead(event);
                        });

                    this.userChannelBound = true;
                },

                subscribeConversationChannels(conversationId) {
                    if (!window.Echo) {
                        return;
                    }

                    const numericId = Number(conversationId);

                    if (this.activeConversationChannelId && this.activeConversationChannelId !== numericId) {
                        window.Echo.leave(`messaging.conversation.${this.activeConversationChannelId}`);
                    }

                    if (this.activePresenceChannelId && this.activePresenceChannelId !== numericId) {
                        window.Echo.leave(`messaging.presence.${this.activePresenceChannelId}`);
                    }

                    this.activeConversationChannelId = numericId;
                    this.activePresenceChannelId = numericId;

                    window.Echo.private(`messaging.conversation.${numericId}`)
                        .listen('.message.sent', (event) => {
                            this.handleIncomingMessage(event);
                        })
                        .listen('.conversation.read', (event) => {
                            this.handleConversationRead(event);
                        })
                        .listenForWhisper('typing', (event) => {
                            if (!event || Number(event.user_id) === this.currentUserId) {
                                return;
                            }

                            this.typingIndicator = 'Guide is typing...';

                            if (this.typingTimeoutHandle) {
                                window.clearTimeout(this.typingTimeoutHandle);
                            }

                            this.typingTimeoutHandle = window.setTimeout(() => {
                                this.typingIndicator = '';
                            }, 1400);
                        });

                    window.Echo.join(`messaging.presence.${numericId}`)
                        .here((users) => {
                            this.onlineUserIds = users.map((item) => Number(item.id));
                        })
                        .joining((user) => {
                            const numericUserId = Number(user.id);
                            if (!this.onlineUserIds.includes(numericUserId)) {
                                this.onlineUserIds.push(numericUserId);
                            }
                        })
                        .leaving((user) => {
                            const numericUserId = Number(user.id);
                            this.onlineUserIds = this.onlineUserIds.filter((id) => Number(id) !== numericUserId);
                        });
                },

                isParticipantOnline() {
                    const participantId = Number(this.activeConversation?.participant?.id || 0);
                    if (!participantId) {
                        return false;
                    }

                    return this.onlineUserIds.includes(participantId);
                },

                handleIncomingMessage(event) {
                    const conversationId = Number(event?.conversation_id || 0);
                    if (!conversationId) {
                        return;
                    }

                    const conversation = this.conversations.find((item) => Number(item.id) === conversationId);
                    const unreadCounts = event.unread_counts || {};

                    if (conversation) {
                        conversation.last_message = event.last_message || conversation.last_message;
                        conversation.last_message_at = event.last_message_at || conversation.last_message_at;

                        if (Object.prototype.hasOwnProperty.call(unreadCounts, String(this.currentUserId))) {
                            conversation.unread_count = Number(unreadCounts[String(this.currentUserId)] || 0);
                        }

                        this.moveConversationToTop(conversationId);
                    }

                    if (this.selectedConversationId === conversationId && event.message) {
                        if (!this.messages.some((message) => Number(message.id) === Number(event.message.id))) {
                            this.messages.push(event.message);
                        }

                        if (Number(event.message.sender_id) !== this.currentUserId) {
                            this.markConversationRead(conversationId);
                            if (conversation) {
                                conversation.unread_count = 0;
                            }
                        }

                        this.$nextTick(() => this.scrollChatToBottom());
                    }
                },

                handleConversationRead(event) {
                    const conversationId = Number(event?.conversation_id || 0);
                    const readerId = Number(event?.reader_id || 0);

                    if (!conversationId || !readerId || this.selectedConversationId !== conversationId || readerId === this.currentUserId) {
                        return;
                    }

                    this.messages = this.messages.map((message) => {
                        if (Number(message.sender_id) === this.currentUserId) {
                            return {
                                ...message,
                                read_at: event.read_at,
                            };
                        }

                        return message;
                    });
                },

                scrollChatToBottom() {
                    if (!this.$refs.chatMessages) {
                        return;
                    }

                    this.$refs.chatMessages.scrollTop = this.$refs.chatMessages.scrollHeight;
                },
            };
        }
    </script>

    <script>
        (() => {
            const overlay = document.getElementById('tourRequestModal');
            const form = document.getElementById('tourRequestForm');
            const feedback = document.getElementById('tourRequestFeedback');

            if (!overlay || !form || !feedback) {
                return;
            }

            const titleInput = document.getElementById('tourRequestInputTitle');
            const durationInput = document.getElementById('tourRequestDuration');
            const budgetInput = document.getElementById('tourRequestBudget');
            const regionInput = document.getElementById('tourRequestRegion');
            const locationInput = document.getElementById('tourRequestLocation');
            const adultsInput = document.getElementById('tourRequestAdults');
            const childrenInput = document.getElementById('tourRequestChildren');
            const dateInput = document.getElementById('tourRequestDate');
            const descriptionInput = document.getElementById('tourRequestDescription');
            const interestsInput = document.getElementById('tourRequestInterests');
            const chipsWrap = document.getElementById('tourRequestChips');
            const addInterestButton = document.getElementById('tourRequestAddInterest');
            const submitButton = form.querySelector('.tour-request-submit');

            const todayString = new Date().toISOString().split('T')[0];
            dateInput.min = todayString;

            const selectedInterests = new Set();

            const syncInterests = () => {
                interestsInput.value = Array.from(selectedInterests).join(', ');
            };

            const resetFeedback = () => {
                feedback.className = 'tour-request-feedback';
                feedback.textContent = '';
            };

            const setFeedback = (message, type = 'error') => {
                feedback.className = `tour-request-feedback ${type === 'success' ? 'is-success' : 'is-error'}`;
                feedback.textContent = message;
            };

            const setModalOpen = (isOpen) => {
                overlay.classList.toggle('is-open', isOpen);
                overlay.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
                document.body.style.overflow = isOpen ? 'hidden' : '';

                if (isOpen) {
                    window.setTimeout(() => {
                        titleInput?.focus();
                    }, 80);
                }
            };

            document.addEventListener('click', (event) => {
                const triggerButton = event.target.closest('#openRequestModal');
                if (triggerButton) {
                    event.preventDefault();
                    resetFeedback();
                    setModalOpen(true);
                    return;
                }

                const closeButton = event.target.closest('[data-close-tour-request]');
                if (closeButton) {
                    event.preventDefault();
                    setModalOpen(false);
                }
            });

            overlay.addEventListener('click', (event) => {
                if (event.target === overlay) {
                    setModalOpen(false);
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && overlay.classList.contains('is-open')) {
                    setModalOpen(false);
                }
            });

            chipsWrap?.addEventListener('click', (event) => {
                const chip = event.target.closest('[data-interest-chip]');
                if (!chip) {
                    return;
                }

                const value = chip.getAttribute('data-interest-chip');
                if (!value) {
                    return;
                }

                if (selectedInterests.has(value)) {
                    selectedInterests.delete(value);
                    chip.classList.remove('is-selected');
                } else {
                    selectedInterests.add(value);
                    chip.classList.add('is-selected');
                }

                syncInterests();
            });

            addInterestButton?.addEventListener('click', () => {
                const customInterest = window.prompt('Add custom interest');
                const normalized = customInterest?.trim();

                if (!normalized || selectedInterests.has(normalized)) {
                    return;
                }

                selectedInterests.add(normalized);

                const customChip = document.createElement('button');
                customChip.type = 'button';
                customChip.className = 'tour-request-chip is-selected';
                customChip.setAttribute('data-interest-chip', normalized);
                customChip.textContent = normalized;

                addInterestButton.before(customChip);
                syncInterests();
            });

            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                resetFeedback();

                const title = titleInput.value.trim();
                const cityLocation = locationInput.value.trim();
                const region = regionInput.value.trim();
                const description = descriptionInput.value.trim();
                const duration = durationInput.value.trim();

                const adults = Math.max(0, Number.parseInt(adultsInput.value || '0', 10) || 0);
                const children = Math.max(0, Number.parseInt(childrenInput.value || '0', 10) || 0);
                const passengerCount = adults + children;

                const rawBudget = (budgetInput.value || '').replace(/[^\d.]/g, '');
                const budgetValue = Number.parseFloat(rawBudget);

                if (!title) {
                    setFeedback('Request title is required.');
                    titleInput.focus();
                    return;
                }

                if (!cityLocation && !region) {
                    setFeedback('Please provide a region or city/province destination.');
                    locationInput.focus();
                    return;
                }

                if (!Number.isFinite(budgetValue) || budgetValue < 500) {
                    setFeedback('Budget must be at least PHP 500.');
                    budgetInput.focus();
                    return;
                }

                if (passengerCount < 1) {
                    setFeedback('At least 1 traveler is required.');
                    adultsInput.focus();
                    return;
                }

                if (!description) {
                    setFeedback('Description is required.');
                    descriptionInput.focus();
                    return;
                }

                const selectedDate = dateInput.value || todayString;
                const location = [region, cityLocation].filter(Boolean).join(' - ');

                let finalDescription = description;
                const detailLines = [];

                if (duration) {
                    detailLines.push(`Duration: ${duration}`);
                }

                if (interestsInput.value.trim()) {
                    detailLines.push(`Interests: ${interestsInput.value.trim()}`);
                }

                if (detailLines.length > 0) {
                    finalDescription = `${description}\n\n${detailLines.join('\n')}`;
                }

                const formData = new FormData();
                formData.set('title', title);
                formData.set('location', location);
                formData.set('preferred_date', selectedDate);
                formData.set('passenger_count', String(passengerCount));
                formData.set('budget_min', budgetValue.toFixed(2));
                formData.set('budget_max', budgetValue.toFixed(2));
                formData.set('description', finalDescription);
                formData.set('duration', duration);
                formData.set('region', region);
                formData.set('adults', String(adults));
                formData.set('children', String(children));
                formData.set('interests', interestsInput.value.trim());

                submitButton.disabled = true;
                submitButton.textContent = 'Submitting...';

                try {
                    const response = await fetch(@js(route('requests.store')), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': @js(csrf_token()),
                            Accept: 'application/json',
                        },
                        body: formData,
                    });

                    const payload = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        const errorMessage = payload?.message
                            || (payload?.errors ? Object.values(payload.errors).flat().join(' ') : null)
                            || 'Unable to submit request right now. Please check your fields and try again.';
                        setFeedback(errorMessage);
                        return;
                    }

                    setFeedback(payload?.message || 'Request submitted successfully.', 'success');

                    if (payload?.post) {
                        window.dispatchEvent(new CustomEvent('tour-request-created', {
                            detail: {
                                post: payload.post,
                            },
                        }));
                    }

                    form.reset();
                    adultsInput.value = '1';
                    childrenInput.value = '0';
                    selectedInterests.clear();
                    chipsWrap.querySelectorAll('[data-interest-chip]').forEach((chip) => {
                        chip.classList.remove('is-selected');
                    });
                    syncInterests();

                    window.setTimeout(() => {
                        setModalOpen(false);
                        resetFeedback();
                    }, 700);
                } catch (error) {
                    setFeedback('Unable to submit request right now. Please try again.');
                } finally {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Post';
                }
            });
        })();
    </script>

    @livewireScripts
</body>

</html>
