<?php

use App\Livewire\ExploreToursFeed;
use App\Models\BookingRequest;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('loads the explore tours page', function () {
    get('/explore-tours')
        ->assertSuccessful()
        ->assertSee('Explore Tours')
        ->assertSee('Filters')
        ->assertSee('Create Tour Request')
        ->assertSee('class="brand" href="'.url('/').'"', false)
        ->assertSeeLivewire(ExploreToursFeed::class);
});

it('shows hero call to action buttons on the landing page', function () {
    get('/')
        ->assertSuccessful()
        ->assertSee('View All Tours')
        ->assertSee('Create Your Request')
        ->assertDontSee('Sort By');
});

it('shows signup card cta for guests on shared explore tours cards', function () {
    $guide = User::factory()->guide()->create(['status' => 'active']);
    $tourist = User::factory()->create(['role' => 'tourist', 'status' => 'active']);

    $tour = Tour::factory()->create([
        'guide_id' => $guide->id,
        'title' => 'Coron Limestone Adventure',
        'status' => 'active',
        'admin_approved' => true,
    ]);

    BookingRequest::query()->create([
        'tourist_id' => $tourist->id,
        'guide_id' => $guide->id,
        'tour_id' => $tour->id,
        'requested_date' => now()->addDays(7)->toDateString(),
        'group_size' => 2,
        'total_price' => 5000,
        'status' => 'accepted',
    ]);

    get('/')
        ->assertSuccessful()
        ->assertSee('Coron Limestone Adventure')
        ->assertSee('data-tour-card-link="tour-'.$tour->id.'"', false)
        ->assertSee('href="'.route('tours.show', ['tour' => $tour, 'from' => 'home']).'"', false);
});

it('shows only six curated high-demand tours on home featured section and excludes new listings without engagement', function () {
    $guide = User::factory()->guide()->create(['status' => 'active']);
    $tourist = User::factory()->create(['role' => 'tourist', 'status' => 'active']);

    $featuredTours = collect(range(1, 6))->map(function (int $index) use ($guide, $tourist): Tour {
        $tour = Tour::factory()->create([
            'guide_id' => $guide->id,
            'title' => 'Qualified Tour '.$index,
            'status' => 'active',
            'admin_approved' => true,
        ]);

        BookingRequest::query()->create([
            'tourist_id' => $tourist->id,
            'guide_id' => $guide->id,
            'tour_id' => $tour->id,
            'requested_date' => now()->addDays($index)->toDateString(),
            'group_size' => 2,
            'total_price' => 4500 + ($index * 100),
            'status' => 'accepted',
        ]);

        return $tour;
    });

    $newListing = Tour::factory()->create([
        'guide_id' => $guide->id,
        'title' => 'Brand New Listing',
        'status' => 'active',
        'admin_approved' => true,
    ]);

    $response = get('/')->assertSuccessful();

    foreach ($featuredTours as $tour) {
        $response->assertSee($tour->title);
    }

    $response->assertDontSee($newListing->title);

    $content = $response->getContent();
    expect(substr_count($content, 'data-tour-card-link="tour-'))->toBe(6);
});

it('loads a tour detail page from card links', function () {
    $guide = User::factory()->guide()->create(['status' => 'active']);

    $tour = Tour::factory()->create([
        'guide_id' => $guide->id,
        'title' => 'El Nido Lagoon Explorer',
        'summary' => 'Cruise hidden lagoons and limestone coves with a local guide.',
        'duration_label' => 'Full-day',
        'max_guests' => null,
        'rating' => 4.9,
    ]);

    get(route('tours.show', $tour))
        ->assertSuccessful()
        ->assertSee('El Nido Lagoon Explorer')
        ->assertSee('Overview')
        ->assertSee('Full-day')
        ->assertSee('Walking')
        ->assertSee('Flexible')
        ->assertSee('Itinerary')
        ->assertSee('Included')
        ->assertSee('Send a message')
        ->assertSee('Book Now');
});

it('shows back to home when opened from home cards', function () {
    $guide = User::factory()->guide()->create(['status' => 'active']);

    $tour = Tour::factory()->create([
        'guide_id' => $guide->id,
        'title' => 'Coron Cliffside Escape',
    ]);

    get(route('tours.show', ['tour' => $tour, 'from' => 'home']))
        ->assertSuccessful()
        ->assertSee('Back to Home')
        ->assertSee('href="'.url('/').'"', false);
});

it('shows back to tourist dashboard when opened from dashboard cards', function () {
    $guide = User::factory()->guide()->create(['status' => 'active']);

    $tourist = User::factory()->create([
        'role' => 'tourist',
        'status' => 'active',
        'email_verified_at' => now(),
    ]);

    $tour = Tour::factory()->create([
        'guide_id' => $guide->id,
        'title' => 'Bohol Hidden Falls Route',
    ]);

    actingAs($tourist);

    get(route('tours.show', ['tour' => $tour, 'from' => 'dashboard']))
        ->assertSuccessful()
        ->assertSee('Back')
        ->assertSee('href="'.route('dashboard.tourist').'"', false);
});

it('sends tourists to messages page with tour context from tour detail overview', function () {
    $guide = User::factory()->guide()->create(['status' => 'active']);

    $tourist = User::factory()->create([
        'role' => 'tourist',
        'status' => 'active',
        'email_verified_at' => now(),
    ]);

    $tour = Tour::factory()->create([
        'guide_id' => $guide->id,
        'title' => 'Batanes Scenic Drive',
    ]);

    actingAs($tourist);

    get(route('tours.show', $tour))
        ->assertSuccessful()
        ->assertSee('href="'.route('dashboard.messages', ['tour' => $tour->id]).'"', false);
});

it('filters explore tours by location search input', function () {
    $guide = User::factory()->guide()->create(['status' => 'active']);

    Tour::factory()->create([
        'guide_id' => $guide->id,
        'title' => 'Siargao Surf Sunrise',
        'summary' => 'Cloud 9 sunrise board lessons and beach route.',
        'region' => 'Caraga',
        'city' => 'General Luna',
    ]);

    Tour::factory()->create([
        'guide_id' => $guide->id,
        'title' => 'Baguio Pines Walk',
        'summary' => 'Forest trail and heritage city stopovers.',
        'region' => 'Cordillera Administrative Region',
        'city' => 'Baguio City',
    ]);

    get('/explore-tours?location=Siargao')
        ->assertSuccessful()
        ->assertSee('Siargao Surf Sunrise')
        ->assertDontSee('Baguio Pines Walk');
});

it('applies location search and sort by together on explore tours', function () {
    $guide = User::factory()->guide()->create(['status' => 'active']);

    Tour::factory()->create([
        'guide_id' => $guide->id,
        'title' => 'Siargao Premium Route',
        'region' => 'Caraga',
        'city' => 'General Luna',
        'price_per_person' => 9000,
    ]);

    Tour::factory()->create([
        'guide_id' => $guide->id,
        'title' => 'Siargao Budget Route',
        'region' => 'Caraga',
        'city' => 'General Luna',
        'price_per_person' => 2500,
    ]);

    Tour::factory()->create([
        'guide_id' => $guide->id,
        'title' => 'Baguio Pine Escape',
        'region' => 'Cordillera Administrative Region',
        'city' => 'Baguio City',
        'price_per_person' => 1200,
    ]);

    $response = get('/explore-tours?location=Siargao&sort_by=price_low_high')
        ->assertSuccessful()
        ->assertSee('Siargao Premium Route')
        ->assertSee('Siargao Budget Route')
        ->assertDontSee('Baguio Pine Escape');

    $content = (string) $response->getContent();
    $budgetIndex = strpos($content, 'Siargao Budget Route');
    $premiumIndex = strpos($content, 'Siargao Premium Route');

    expect($budgetIndex)->not->toBeFalse();
    expect($premiumIndex)->not->toBeFalse();
    expect($budgetIndex < $premiumIndex)->toBeTrue();
});

it('filters by guide region search term and sorts by low price', function () {
    $mimaropaGuide = User::factory()->guide()->create([
        'status' => 'active',
        'region' => 'MIMAROPA',
    ]);

    $outsideGuide = User::factory()->guide()->create([
        'status' => 'active',
        'region' => 'Central Visayas',
    ]);

    $withEffectivePrice = function (array $attributes, float $amount): array {
        if (Schema::hasColumn('tours', 'price_per_person')) {
            $attributes['price_per_person'] = $amount;

            return $attributes;
        }

        if (Schema::hasColumn('tours', 'price')) {
            $attributes['price'] = $amount;

            return $attributes;
        }

        if (Schema::hasColumn('tours', 'base_price')) {
            $attributes['base_price'] = $amount;
        }

        return $attributes;
    };

    Tour::factory()->create($withEffectivePrice([
        'guide_id' => $mimaropaGuide->id,
        'title' => 'Mimaropa Island Budget',
        'region' => 'Palawan',
    ], 1800));

    Tour::factory()->create($withEffectivePrice([
        'guide_id' => $mimaropaGuide->id,
        'title' => 'Mimaropa Island Premium',
        'region' => 'Palawan',
    ], 5600));

    Tour::factory()->create([
        'guide_id' => $outsideGuide->id,
        'title' => 'Cebu City Loop',
        'region' => 'Cebu',
        'price_per_person' => 900,
    ]);

    $response = get('/explore-tours?location=MiMaRoPa&sort_by=price_low_high')
        ->assertSuccessful()
        ->assertSee('Mimaropa Island Budget')
        ->assertSee('Mimaropa Island Premium')
        ->assertDontSee('Cebu City Loop');

    $content = (string) $response->getContent();
    $budgetIndex = strpos($content, 'Mimaropa Island Budget');
    $premiumIndex = strpos($content, 'Mimaropa Island Premium');

    expect($budgetIndex)->not->toBeFalse();
    expect($premiumIndex)->not->toBeFalse();
    expect($budgetIndex < $premiumIndex)->toBeTrue();
});

it('applies case-insensitive location search and low-price sort on tourist dashboard', function () {
    $tourist = User::factory()->create([
        'role' => 'tourist',
        'status' => 'active',
        'email_verified_at' => now(),
    ]);

    $mimaropaGuide = User::factory()->guide()->create([
        'status' => 'active',
        'region' => 'MIMAROPA',
    ]);

    $outsideGuide = User::factory()->guide()->create([
        'status' => 'active',
        'region' => 'Central Luzon',
    ]);

    $withEffectivePrice = function (array $attributes, float $amount): array {
        if (Schema::hasColumn('tours', 'price_per_person')) {
            $attributes['price_per_person'] = $amount;

            return $attributes;
        }

        if (Schema::hasColumn('tours', 'price')) {
            $attributes['price'] = $amount;

            return $attributes;
        }

        if (Schema::hasColumn('tours', 'base_price')) {
            $attributes['base_price'] = $amount;
        }

        return $attributes;
    };

    Tour::factory()->create($withEffectivePrice([
        'guide_id' => $mimaropaGuide->id,
        'title' => 'Dashboard Mimaropa Budget',
        'region' => 'Palawan',
    ], 1200));

    Tour::factory()->create($withEffectivePrice([
        'guide_id' => $mimaropaGuide->id,
        'title' => 'Dashboard Mimaropa Premium',
        'region' => 'Palawan',
    ], 4200));

    Tour::factory()->create($withEffectivePrice([
        'guide_id' => $outsideGuide->id,
        'title' => 'Dashboard Non-Match Tour',
        'region' => 'Tarlac',
    ], 700));

    actingAs($tourist);

    $response = get(route('dashboard.tourist', ['location' => 'MiMaRoPa', 'sort_by' => 'price_low_high']))
        ->assertSuccessful()
        ->assertSee('Dashboard Mimaropa Budget')
        ->assertSee('Dashboard Mimaropa Premium')
        ->assertDontSee('Dashboard Non-Match Tour');

    $content = (string) $response->getContent();
    $budgetIndex = strpos($content, 'Dashboard Mimaropa Budget');
    $premiumIndex = strpos($content, 'Dashboard Mimaropa Premium');

    expect($budgetIndex)->not->toBeFalse();
    expect($premiumIndex)->not->toBeFalse();
    expect($budgetIndex < $premiumIndex)->toBeTrue();
});

it('sorts dashboard tours by displayed card price precedence', function () {
    $tourist = User::factory()->create([
        'role' => 'tourist',
        'status' => 'active',
        'email_verified_at' => now(),
    ]);

    $guide = User::factory()->guide()->create([
        'status' => 'active',
        'region' => 'MIMAROPA',
    ]);

    $tourA = [
        'guide_id' => $guide->id,
        'title' => 'Dashboard Effective Price A',
        'region' => 'Palawan',
    ];

    $tourB = [
        'guide_id' => $guide->id,
        'title' => 'Dashboard Effective Price B',
        'region' => 'Palawan',
    ];

    $hasPrice = Schema::hasColumn('tours', 'price');
    $hasPricePerPerson = Schema::hasColumn('tours', 'price_per_person');
    $hasBasePrice = Schema::hasColumn('tours', 'base_price');

    if ($hasPrice) {
        $tourA['price'] = 9000;
        $tourB['price'] = 1200;
    }

    if ($hasPricePerPerson) {
        $tourA['price_per_person'] = 1000;
        $tourB['price_per_person'] = 8000;
    }

    if (! $hasPrice && $hasBasePrice) {
        $tourA['base_price'] = 9000;
        $tourB['base_price'] = 1200;
    }

    Tour::factory()->create($tourA);
    Tour::factory()->create($tourB);

    actingAs($tourist);

    $response = get(route('dashboard.tourist', ['location' => 'mimaropa', 'sort_by' => 'price_low_high']))
        ->assertSuccessful()
        ->assertSee('Dashboard Effective Price A')
        ->assertSee('Dashboard Effective Price B');

    $content = (string) $response->getContent();
    $tourAIndex = strpos($content, 'Dashboard Effective Price A');
    $tourBIndex = strpos($content, 'Dashboard Effective Price B');

    expect($tourAIndex)->not->toBeFalse();
    expect($tourBIndex)->not->toBeFalse();

    if ($hasPrice) {
        expect($tourBIndex < $tourAIndex)->toBeTrue();

        return;
    }

    if ($hasPricePerPerson) {
        expect($tourAIndex < $tourBIndex)->toBeTrue();

        return;
    }

    if ($hasBasePrice) {
        expect($tourBIndex < $tourAIndex)->toBeTrue();
    }
});
