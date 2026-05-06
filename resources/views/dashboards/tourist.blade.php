<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrblTours - Tourist Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600&family=Asimovian:wght@400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <style>
        :root {
            --white: #ffffff;
            --brown-700: #6f5d52;
            --brown-800: #5a4a39;
            --brown-900: #3f2d22;
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
            color: #5f8a46;
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
            border-color: #d4a563;
            color: #5f8a46;
            background: #ffffff;
        }

        .tour-request-overlay {
            position: fixed;
            inset: 0;
            z-index: 70;
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
            background: var(--white);
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
            color: var(--white);
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
<body class="min-h-screen bg-[#f5efe2] text-[#3e2a1f] font-inter">
    @php
        $currentUser = auth()->user();
    @endphp

    <div x-data="{ scrolled: false, menuOpen: false }" x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 32; })">
        <header :class="scrolled ? 'nav-shell scrolled' : 'nav-shell'" class="fixed inset-x-0 top-0 z-50 border-b border-[#ddd2bd]">
            <div class="mx-auto flex max-w-[1260px] items-center justify-between px-6 py-4">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                    <img src="{{ asset('images/tribaltours_icon.png') }}" alt="TrblTours Icon" class="h-9 w-9 rounded-full border border-[#d3c5aa]/70 bg-[#f7efdd] object-cover">
                    <span class="font-[Asimovian] text-[26px] leading-none font-bold tracking-wide text-[#5a4a39]">TrblTours</span>
                </a>

                <nav class="hidden items-center gap-10 md:flex">
                    <a href="{{ route('dashboard.tourist') }}" class="top-nav-link {{ request()->routeIs('dashboard.tourist') ? 'active' : '' }}">Explore Tours</a>
                    <a href="{{ route('dashboard.my-posts') }}" class="top-nav-link {{ request()->routeIs('dashboard.my-posts') ? 'active' : '' }}">My Posts</a>
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

        <main class="mx-auto max-w-[1260px] px-6 pb-24 pt-[128px]">
            <livewire:explore-tours-feed context="dashboard" />
        </main>
    </div>

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
                    const response = await fetch("{{ route('requests.store') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            'Accept': 'application/json',
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
                    }, 900);
                } catch (error) {
                    setFeedback('Unable to submit request right now. Please try again.');
                } finally {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Submit Request';
                }
            });
        })();
    </script>
    
    @livewireScripts
</body>
</html>
