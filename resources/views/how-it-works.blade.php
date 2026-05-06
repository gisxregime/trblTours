<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TrblTours') }} | About & How It Works</title>

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
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Instrument Sans', sans-serif;
            color: var(--brown-800);
            background: var(--sand-100);
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
            gap: 16px;
            flex-wrap: wrap;
            padding: 14px 0;
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
            font-size: clamp(18px, 2.2vw, 24px);
            color: var(--brown-900);
        }

        .header-nav {
            display: inline-flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .header-link {
            color: var(--brown-900);
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            padding: 8px 10px;
            border-radius: 999px;
        }

        .header-link:hover {
            background: #f9efdc;
            color: var(--olive);
        }

        .header-actions {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .lang-select {
            border: 1px solid rgba(111, 62, 44, 0.2);
            border-radius: 999px;
            background: var(--white);
            color: var(--brown-900);
            min-height: 38px;
            padding: 0 12px;
            font-size: 14px;
            font-weight: 600;
        }

        .btn {
            border-radius: 999px;
            min-height: 38px;
            padding: 0 14px;
            border: 1px solid rgba(111, 62, 44, 0.2);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            transition: 0.2s ease;
        }

        .btn-light {
            background: var(--white);
            color: var(--brown-900);
        }

        .btn-light:hover {
            border-color: var(--olive);
            color: var(--olive);
        }

        .btn-solid {
            background: var(--olive);
            border-color: var(--olive);
            color: var(--white);
        }

        .btn-solid:hover {
            background: #7f8d4d;
        }

        .page {
            padding: 24px 0 60px;
        }

        .breadcrumb {
            color: var(--brown-700);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .hero-title {
            margin: 10px 0 8px;
            color: var(--brown-900);
            font-size: clamp(32px, 4.4vw, 48px);
            line-height: 1.08;
        }

        .hero-subtitle {
            margin: 0;
            color: var(--brown-700);
            font-size: 16px;
            line-height: 1.6;
            max-width: 860px;
        }

        .section {
            margin-top: 22px;
            border-radius: var(--radius-xl);
            border: 1px solid rgba(111, 62, 44, 0.14);
            background: var(--white);
            box-shadow: var(--shadow-md);
            padding: 20px;
        }

        .section h2 {
            margin: 0;
            color: var(--brown-900);
            font-size: clamp(22px, 2.4vw, 30px);
        }

        .section h3 {
            margin: 0;
            color: var(--brown-900);
            font-size: 20px;
        }

        .section p {
            color: var(--brown-700);
            line-height: 1.7;
            margin: 10px 0 0;
        }

        .muted-kicker {
            margin: 0 0 8px;
            color: var(--brown-700);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .two-col {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-top: 14px;
        }

        .mini-card {
            border-radius: var(--radius-lg);
            border: 1px solid rgba(111, 62, 44, 0.12);
            background: #fdf7ea;
            padding: 14px;
        }

        .mini-card h4 {
            margin: 0 0 8px;
            color: var(--brown-900);
            font-size: 16px;
        }

        .mini-card ul {
            margin: 0;
            padding-left: 18px;
            color: var(--brown-700);
            display: grid;
            gap: 7px;
            line-height: 1.6;
        }

        .bridge {
            border-radius: var(--radius-xl);
            border: 1px dashed rgba(111, 62, 44, 0.24);
            background: #fffaf2;
            padding: 16px;
            margin-top: 14px;
            color: var(--brown-700);
        }

        .cards-3 {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-top: 14px;
        }

        .offer-card {
            border-radius: var(--radius-lg);
            border: 1px solid rgba(111, 62, 44, 0.14);
            background: #fffaf2;
            padding: 14px;
        }

        .offer-card h4 {
            margin: 0;
            color: var(--brown-900);
            font-size: 17px;
        }

        .offer-card p {
            margin-top: 8px;
        }

        .mission-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-top: 14px;
        }

        .step-toggle {
            margin-top: 16px;
            display: inline-flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .toggle-btn {
            min-height: 40px;
            border-radius: 999px;
            border: 1px solid rgba(111, 62, 44, 0.2);
            background: #fffaf2;
            color: var(--brown-900);
            padding: 0 14px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .toggle-btn.active {
            border-color: var(--olive);
            background: var(--olive);
            color: var(--white);
        }

        .journey-panel {
            margin-top: 16px;
        }

        .journey-panel.hidden {
            display: none;
        }

        .steps {
            display: grid;
            gap: 14px;
        }

        .step-card {
            border-radius: var(--radius-lg);
            border: 1px solid rgba(111, 62, 44, 0.14);
            background: var(--white);
            box-shadow: var(--shadow-md);
            padding: 16px;
        }

        .step-card h4 {
            margin: 0;
            color: var(--brown-900);
            font-size: 20px;
        }

        .step-meta {
            margin-top: 10px;
            border-radius: 12px;
            border: 1px solid rgba(111, 62, 44, 0.12);
            background: #fdf7ea;
            padding: 10px;
            color: var(--brown-700);
            line-height: 1.6;
        }

        .chips {
            margin-top: 10px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            border: 1px solid rgba(111, 62, 44, 0.18);
            background: #fffaf2;
            padding: 5px 10px;
            color: var(--brown-900);
            font-size: 12px;
            font-weight: 700;
        }

        .chip.active {
            background: var(--olive);
            border-color: var(--olive);
            color: var(--white);
        }

        .step-button {
            margin-top: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            border: 1px solid var(--olive);
            background: var(--olive);
            color: var(--white);
            min-height: 38px;
            padding: 0 14px;
            font-size: 14px;
            font-weight: 700;
        }

        .example-box {
            margin-top: 10px;
            border-radius: 12px;
            border: 1px solid rgba(111, 62, 44, 0.16);
            background: #fff8eb;
            padding: 10px;
            color: var(--brown-700);
            line-height: 1.6;
        }

        .example-line {
            margin-top: 6px;
            padding: 8px 10px;
            border-radius: 10px;
            border: 1px solid rgba(111, 62, 44, 0.14);
            background: var(--white);
            color: var(--brown-700);
        }

        @media (max-width: 960px) {
            .cards-3 {
                grid-template-columns: 1fr;
            }

            .two-col,
            .mission-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <header class="site-header">
        <div class="container header-inner">
            <a href="{{ route('dashboard') }}" class="brand" aria-label="Trbltours home">
                <img src="{{ asset('images/tribaltours_icon.png') }}" alt="Trbltours" class="brand-logo">
                <span class="brand-font brand-name">Trbltours</span>
            </a>

            <nav class="header-nav" aria-label="Primary navigation">
                <a href="{{ route('explore-tours') }}" class="header-link">Explore Tours</a>
                <a href="#" class="header-link">Become a Guide</a>
                <a href="{{ route('how-it-works') }}" class="header-link">How It Works</a>
                <a href="#" class="header-link">Safety Tips</a>
            </nav>

            <div class="header-actions">
                <select class="lang-select" aria-label="Language selector">
                    <option>English</option>
                </select>
                <a href="{{ route('login') }}" class="btn btn-light">Log In</a>
                <a href="{{ route('register') }}" class="btn btn-solid">Sign Up</a>
            </div>
        </div>
    </header>

    <main class="page">
        <div class="container">
            <p class="breadcrumb">HOME / ABOUT US</p>
            <h1 class="hero-font hero-title">About & How Trbltours Works</h1>
            <p class="hero-subtitle">We connect travelers with trusted local guides for meaningful and personalized journeys.</p>

            <section class="section">
                <h2>Why TrblTours Exists</h2>
                <p>Travel in the Philippines is full of untapped potential—but both sides of the equation are stuck with the same frustrations.</p>

                <p class="muted-kicker" style="margin-top: 14px;">WITHOUT TRBLTOURS</p>
                <div class="two-col">
                    <article class="mini-card">
                        <h4>Tourists</h4>
                        <ul>
                            <li>Tourists struggle to find guides they can trust</li>
                            <li>No way to verify if a guide is legitimate or experienced</li>
                            <li>Hidden fees and vague pricing with no transparency</li>
                            <li>Generic tour packages that don't fit personal needs or budget</li>
                        </ul>
                    </article>

                    <article class="mini-card">
                        <h4>Local Guides</h4>
                        <ul>
                            <li>Local guides can't turn their hometown knowledge into income</li>
                            <li>Miss out on tourists actively looking for guides in their area</li>
                            <li>No structured way to compare multiple guide offers at once</li>
                            <li>No system to manage bookings, requests, or earnings in one place</li>
                        </ul>
                    </article>
                </div>

                <p class="bridge">TrblTours bridges this gap — with a dual-feed platform where tourists post custom requests or book verified listings directly, guides respond publicly with transparent offers, and a private real-time chat opens the moment a match is made.</p>
            </section>

            <section class="section">
                <p class="muted-kicker">WHY CHOOSE US</p>
                <h2>What We Offer</h2>

                <div class="cards-3">
                    <article class="offer-card">
                        <h4>Authentic Experiences</h4>
                        <p>We promote real local experiences beyond the usual tourist spots.</p>
                    </article>

                    <article class="offer-card">
                        <h4>Trusted & Safe</h4>
                        <p>All guides are verified and reviews ensure a safe and reliable travel experience.</p>
                    </article>

                    <article class="offer-card">
                        <h4>Custom & Flexible</h4>
                        <p>From fixed tour packages to custom travel requests — we make travel fit your needs.</p>
                    </article>
                </div>
            </section>

            <section class="section">
                <h2>OUR MISSION & VISION</h2>
                <div class="mission-grid">
                    <article class="mini-card">
                        <h4>Mission</h4>
                        <p>To empower travelers by offering personalized and authentic travel experiences through a transparent and interactive platform.</p>
                    </article>

                    <article class="mini-card">
                        <h4>Vision</h4>
                        <p>To become a leading travel community where tourists and local guides collaborate to create unforgettable journeys around the world.</p>
                    </article>
                </div>
            </section>

            <section class="section">
                <h2>HOW IT WORKS - Your Journey, Simplified</h2>
                <p>TrblTours uses a dual-feed model - browse fixed tour listings or post a custom request. Verified guides respond publicly with offers, you pick the best fit, and a private chat opens automatically to finalize every detail.</p>

                <div class="step-toggle" role="tablist" aria-label="How it works audience switcher">
                    <button type="button" class="toggle-btn active" data-journey-tab="tourists" aria-selected="true">For Tourists</button>
                    <button type="button" class="toggle-btn" data-journey-tab="guides" aria-selected="false">For Guides</button>
                </div>

                <div id="touristsPanel" class="journey-panel" data-journey-panel="tourists">
                    <div class="steps">
                        <article class="step-card">
                            <h4>Step 1 - Browse the Unified Feed</h4>
                            <p>The TrblTours feed shows two types of content side by side: Tour Listings created by verified guides (ready to book directly) and Request Posts from tourists looking for custom arrangements. Filter by destination, date, or budget to find the perfect match.</p>
                            <div class="step-meta">Pro Tip: Use location filters to surface guides who operate in your target destination.</div>
                        </article>

                        <article class="step-card">
                            <h4>Step 2 - Create your account</h4>
                            <p>Radio/Toggle: Tourist (selected) | Guide</p>
                            <div class="chips">
                                <span class="chip active">Tourist (selected)</span>
                                <span class="chip">Guide</span>
                            </div>
                            <p>Button: Sign Up</p>
                            <span class="step-button">Sign Up</span>
                            <p>Sign up with your email and password, then select the Tourist role during registration. Your account unlocks a personal dashboard where you can track requests, manage bookings, and message guides - all in one place.</p>
                            <div class="step-meta">Your Security Matters: Role-based access control keeps your data secure and ensures you only see what's relevant to you.</div>
                        </article>

                        <article class="step-card">
                            <h4>Step 3 - Post a Custom Travel Request</h4>
                            <p>Can't find a listing that fits? Post a Request to the community feed. Describe your destination, travel dates, group size, budget, and preferences. Verified guides in that area will see your request and respond with personalized offers.</p>
                            <div class="step-meta">Location-aware: Guides are notified based on their registered service areas, so only relevant guides see your post.</div>
                            <div class="example-box">Example Request Card: "3-Day Palawan Adventure - Looking for a local guide for island hopping. El Nido. Group of 4, budget ₱6,000 total. Flexible on dates in June."</div>
                            <div class="example-box">Details: El Nido | 4 pax | ₱6,000 | June</div>
                        </article>

                        <article class="step-card">
                            <h4>Step 4 - Review Public Guide Offers</h4>
                            <p>Verified guides respond to your request through a public comment section — visible to everyone so the process stays transparent. Each offer shows the guide's profile, their Verified badge, pricing, availability, and service details. Compare offers openly before deciding.</p>
                            <div class="step-meta">Transparent by design: Public comments prevent hidden fees and help you make an informed choice.</div>
                            <div class="example-line">Maria S. ✓Verified - Available June 5–8. Full package ₱7,500 incl. bangka & snorkel gear. [Select Guide]</div>
                            <div class="example-line">Jose R. ✓Verified - ₱8,000 with lunch and sunset tour included.</div>
                        </article>
                    </div>
                </div>

                <div id="guidesPanel" class="journey-panel hidden" data-journey-panel="guides">
                    <div class="steps">
                        <article class="step-card">
                            <h4>Step 1 - Guide Registration</h4>
                            <p>Toggle: Tourist | Guide (selected)</p>
                            <div class="chips">
                                <span class="chip">Tourist</span>
                                <span class="chip active">Guide (selected)</span>
                            </div>
                            <p>Sign up with the Guide role and complete your profile - include your experience, specializations, languages spoken, and service locations. Upload your credentials for admin review. Once approved, you earn a Verified Guide badge displayed across your listings, comments, and bookings.</p>
                            <p>Upload button: Upload Credentials (PDF/Image)</p>
                            <p>Submit button: Submit Application</p>
                            <div class="chips">
                                <span class="step-button">Upload Credentials (PDF/Image)</span>
                                <span class="step-button">Submit Application</span>
                            </div>
                            <div class="step-meta">Verification builds trust: Tourists can filter for verified guides, giving you a competitive edge over unverified listings.</div>
                        </article>

                        <article class="step-card">
                            <h4>Step 2 - Create Your Listings</h4>
                            <div class="example-box">Example Card: "2D1N Chocolate Hills, Bohol - Includes ATV ride, Tarsier Sanctuary, and overnight at eco-lodge. Max 6 pax. | Bohol: ₱3,200/pax | Verified Guide"</div>
                            <p>Once verified, publish Tour Listings directly on the feed. Set your itinerary, pricing, group size, included services, and available dates. Tourists can book directly from your listing without needing to post a request - giving you a steady pipeline of inbound bookings.</p>
                        </article>

                        <article class="step-card">
                            <h4>Step 3 - Respond to Tourist Requests</h4>
                            <p>Browse the feed for open Request Posts matching your service areas. Respond with a public comment detailing your offer – pricing, availability, what's included, and any questions. Your Verified badge is displayed on every comment to build tourist confidence before they select you.</p>
                            <div class="step-meta">First to respond wins attention: Tourists often pick the guide whose offer is clearest and most competitive.</div>
                            <div class="example-box">Example Offer: You (Maria S.) ✓ Verified - I cover El Nido routes year-round. Full 3D trip for ₱7,500 incl. gear & meals. Available June 5–8.</div>
                        </article>

                        <article class="step-card">
                            <h4>Step 4 - Get Selected & Open Private Chat</h4>
                            <p>When a tourist picks your offer, the request automatically closes to further competition and a private real-time chat room opens exclusively between you and the tourist. Use this space to share the full itinerary, confirm logistics, and build rapport before the trip.</p>
                            <div class="example-box">Example Chat:</div>
                            <div class="example-line">- Tourist #42: "Hi Maria! We picked you 🏅 Can we do June 5?"</div>
                            <div class="example-line">- Maria S.: "Absolutely! Here's your detailed itinerary ✈️"</div>
                            <div class="example-line">- Tourist #42: "Perfect, confirming now!"</div>
                        </article>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <script>
        (() => {
            const tabs = Array.from(document.querySelectorAll('[data-journey-tab]'));
            const panels = Array.from(document.querySelectorAll('[data-journey-panel]'));

            const activate = (target) => {
                tabs.forEach((tab) => {
                    const active = tab.dataset.journeyTab === target;
                    tab.classList.toggle('active', active);
                    tab.setAttribute('aria-selected', active ? 'true' : 'false');
                });

                panels.forEach((panel) => {
                    const active = panel.dataset.journeyPanel === target;
                    panel.classList.toggle('hidden', !active);
                });
            };

            tabs.forEach((tab) => {
                tab.addEventListener('click', () => activate(tab.dataset.journeyTab));
            });
        })();
    </script>
</body>

</html>
