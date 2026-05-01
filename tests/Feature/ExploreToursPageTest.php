<?php

use App\Livewire\ExploreToursFeed;
use App\Models\BookingRequest;
use App\Models\Tour;
use App\Models\User;

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
        'rating' => 4.9,
    ]);

    get(route('tours.show', $tour))
        ->assertSuccessful()
        ->assertSee('El Nido Lagoon Explorer')
        ->assertSee('Overview')
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
        ->assertSee('Back to Tourist Dashboard')
        ->assertSee('href="'.route('dashboard.tourist').'"', false);
});
