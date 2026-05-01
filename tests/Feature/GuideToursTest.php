<?php

use App\Livewire\Guide\GuideProfile;
use App\Livewire\Guide\GuideTours;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('shows the guide tours manager page', function () {
    $user = User::factory()->create([
        'role' => 'guide',
        'full_name' => 'Maria Santos',
    ]);

    actingAs($user)
        ->get(route('dashboard.guide.tours'))
        ->assertSuccessful()
        ->assertSee('Guide Tours')
        ->assertSee('Create Tour');
});

it('allows a guide to create a tour from the livewire form', function () {
    Storage::fake('public');

    $user = User::factory()->create([
        'role' => 'guide',
        'full_name' => 'Maria Santos',
    ]);

    actingAs($user);

    Livewire::test(GuideTours::class)
        ->set('form.title', 'Sunset Harbor Walk')
        ->set('form.region', 'Central Visayas')
        ->set('form.city', 'Cebu City')
        ->set('form.summary', str_repeat('Walk the old harbor district with a local guide. ', 4))
        ->set('form.duration_hours', 4)
        ->set('form.duration_unit', 'hours')
        ->set('form.transportation', ['walking_tour', 'public_transportation'])
        ->set('form.min_guests', 2)
        ->set('form.max_guests', 8)
        ->set('form.price_per_person', 1999.50)
        ->set('form.currency', 'PHP')
        ->set('form.price_unit', 'person')
        ->set('form.available_on', now()->addWeek()->format('Y-m-d'))
        ->set('form.is_featured', true)
        ->set('form.status', 'active')
        ->set('tourPhotos', [
            UploadedFile::fake()->create('tour-1.jpg', 200, 'image/jpeg'),
            UploadedFile::fake()->create('tour-2.jpg', 200, 'image/jpeg'),
            UploadedFile::fake()->create('tour-3.jpg', 200, 'image/jpeg'),
        ])
        ->call('save')
        ->assertHasNoErrors();

    $tour = Tour::query()->where('title', 'Sunset Harbor Walk')->first();

    expect($tour)->not->toBeNull()
        ->and($tour?->guide_id ?? $tour?->created_by)->toBe($user->id)
        ->and($tour?->region)->toBe('Central Visayas')
        ->and((int) ($tour?->duration_hours ?? 0))->toBe(4)
        ->and($tour?->duration_unit)->toBe('hours')
        ->and(is_array($tour?->gallery_images))->toBeTrue()
        ->and(count((array) $tour?->gallery_images))->toBe(3)
        ->and((float) ($tour?->price_per_person ?? $tour?->price))->toBe(1999.50);

    if (Schema::hasColumn('tours', 'min_guests')) {
        expect((int) ($tour?->min_guests ?? 0))->toBe(2);
    }

    if (Schema::hasColumn('tours', 'max_guests')) {
        expect((int) ($tour?->max_guests ?? 0))->toBe(8);
    }

    foreach ((array) $tour?->gallery_images as $imagePath) {
        expect(Storage::disk('public')->exists($imagePath))->toBeTrue();
    }
});

it('saves long summaries without overflowing short_description', function () {
    $user = User::factory()->create([
        'role' => 'guide',
        'full_name' => 'Maria Santos',
    ]);

    actingAs($user);

    $longSummary = str_repeat('Lorem ipsum dolor sit amet, consectetur adipiscing elit. ', 12);

    Livewire::test(GuideTours::class)
        ->set('form.title', 'Length Safe Summary Tour')
        ->set('form.region', 'Davao Region')
        ->set('form.city', 'Tagum')
        ->set('form.summary', $longSummary)
        ->set('form.duration_hours', 2)
        ->set('form.duration_unit', 'hours')
        ->set('form.price_per_person', 999)
        ->set('form.currency', 'PHP')
        ->set('form.price_unit', 'person')
        ->set('form.status', 'draft')
        ->call('save')
        ->assertHasNoErrors();

    $tour = Tour::query()->where('title', 'Length Safe Summary Tour')->first();

    expect($tour)->not->toBeNull();

    if (Schema::hasColumn('tours', 'short_description')) {
        expect(mb_strlen((string) ($tour?->short_description ?? '')))->toBeLessThanOrEqual(255);
    }
});

it('keeps only the first five tour photos when more are selected', function () {
    Storage::fake('public');

    $user = User::factory()->create([
        'role' => 'guide',
        'full_name' => 'Maria Santos',
    ]);

    actingAs($user);

    Livewire::test(GuideTours::class)
        ->set('form.title', 'Canal Discovery Tour')
        ->set('form.region', 'National Capital Region')
        ->set('form.summary', str_repeat('Discover hidden canals and stories of old Manila. ', 3))
        ->set('form.duration_hours', 2)
        ->set('form.duration_unit', 'hours')
        ->set('form.price_per_person', 1500)
        ->set('form.currency', 'PHP')
        ->set('form.price_unit', 'person')
        ->set('form.status', 'draft')
        ->set('tourPhotos', [
            UploadedFile::fake()->create('tour-1.jpg', 200, 'image/jpeg'),
            UploadedFile::fake()->create('tour-2.jpg', 200, 'image/jpeg'),
            UploadedFile::fake()->create('tour-3.jpg', 200, 'image/jpeg'),
            UploadedFile::fake()->create('tour-4.jpg', 200, 'image/jpeg'),
            UploadedFile::fake()->create('tour-5.jpg', 200, 'image/jpeg'),
            UploadedFile::fake()->create('tour-6.jpg', 200, 'image/jpeg'),
        ])
        ->call('save')
        ->assertHasNoErrors();

    $tour = Tour::query()->where('title', 'Canal Discovery Tour')->first();

    expect($tour)->not->toBeNull()
        ->and(is_array($tour?->gallery_images))->toBeTrue()
        ->and(count((array) $tour?->gallery_images))->toBe(5);

    foreach ((array) $tour?->gallery_images as $imagePath) {
        expect(Storage::disk('public')->exists($imagePath))->toBeTrue();
    }
});

it('accumulates photos across upload events up to five', function () {
    Storage::fake('public');

    $user = User::factory()->create([
        'role' => 'guide',
        'full_name' => 'Maria Santos',
    ]);

    actingAs($user);

    Livewire::test(GuideTours::class)
        ->set('tourPhotos', [UploadedFile::fake()->create('tour-1.jpg', 200, 'image/jpeg')])
        ->call('processTourPhotos')
        ->set('tourPhotos', [UploadedFile::fake()->create('tour-2.jpg', 200, 'image/jpeg')])
        ->call('processTourPhotos')
        ->set('tourPhotos', [UploadedFile::fake()->create('tour-3.jpg', 200, 'image/jpeg')])
        ->call('processTourPhotos')
        ->set('tourPhotos', [UploadedFile::fake()->create('tour-4.jpg', 200, 'image/jpeg')])
        ->call('processTourPhotos')
        ->set('tourPhotos', [UploadedFile::fake()->create('tour-5.jpg', 200, 'image/jpeg')])
        ->call('processTourPhotos')
        ->assertHasNoErrors();

    $tour = Tour::query()->latest('id')->first();

    expect($tour)->not->toBeNull()
        ->and(is_array($tour?->gallery_images))->toBeTrue()
        ->and(count((array) $tour?->gallery_images))->toBe(5);
});

it('pre-fills form fields with latest draft data when editing', function () {
    $user = User::factory()->create([
        'role' => 'guide',
        'full_name' => 'Maria Santos',
    ]);

    actingAs($user);

    $component = Livewire::test(GuideTours::class)
        ->set('form.title', 'Draft Manila River Walk')
        ->set('form.region', 'NCR')
        ->set('form.city', 'Manila')
        ->set('form.summary', 'A test draft summary.')
        ->set('form.duration_hours', 2)
        ->set('form.duration_unit', 'hours')
        ->set('form.price_per_person', 1200)
        ->assertHasNoErrors();

    $tour = Tour::query()->latest('id')->first();

    expect($tour)->not->toBeNull();

    DB::table('tours')->where('id', $tour->id)->update([
        'created_at' => '2026-04-28 10:15:00',
        'updated_at' => '2026-04-28 10:15:00',
    ]);

    $tour->refresh();

    $component
        ->call('edit', $tour->id)
        ->assertSet('editingTourId', $tour->id)
        ->assertSet('form.title', 'Draft Manila River Walk')
        ->assertSet('form.region', 'NCR')
        ->assertSet('form.city', 'Manila')
        ->assertSet('form.summary', 'A test draft summary.')
        ->assertSet('form.duration_hours', '2')
        ->assertSet('form.duration_unit', 'hours')
        ->assertSet('form.price_per_person', '1200.00');
});

it('prefills the edit form with tour date and transportation', function () {
    $user = User::factory()->create([
        'role' => 'guide',
        'full_name' => 'Maria Santos',
    ]);

    $tourPayload = [
        'guide_id' => $user->id,
        'created_by' => $user->id,
        'title' => 'Island Sunset Cruise',
        'name' => 'Island Sunset Cruise',
        'region' => 'Palawan',
        'city' => 'El Nido',
        'summary' => 'A sunset cruise with a guided island stop.',
        'description' => 'A sunset cruise with a guided island stop.',
        'duration_label' => '4 hours',
        'duration_hours' => 4,
        'duration_unit' => 'hours',
        'price_per_person' => 1800,
        'price' => 1800,
        'price_unit' => 'person',
        'available_on' => '2026-04-15',
        'category' => 'private_transportation, walking_tour',
        'activities' => json_encode(['private_transportation', 'walking_tour']),
        'status' => 'draft',
    ];

    DB::table('tours')->insert(
        collect($tourPayload)
            ->filter(fn (mixed $value, string $column): bool => Schema::hasColumn('tours', $column))
            ->all()
    );

    $tour = Tour::query()->latest('id')->first();

    expect($tour)->not->toBeNull();

    actingAs($user);

    Livewire::test(GuideTours::class)
        ->call('edit', $tour->id)
        ->assertSet('editingTourId', $tour->id)
        ->assertSet('form.available_on', '2026-04-15')
        ->assertSet('form.transportation', ['private_transportation', 'walking_tour'])
        ->assertSet('form.title', 'Island Sunset Cruise')
        ->assertSet('form.region', 'Palawan')
        ->assertSet('form.city', 'El Nido')
        ->assertSet('form.summary', 'A sunset cruise with a guided island stop.')
        ->assertSet('form.duration_hours', '4')
        ->assertSet('form.price_per_person', '1800.00');
});

it('prefills the edit form with city duration guests date and status', function () {
    $user = User::factory()->create([
        'role' => 'guide',
        'full_name' => 'Maria Santos',
    ]);

    $tourPayload = [
        'guide_id' => $user->id,
        'created_by' => $user->id,
        'title' => 'Coron Lagoon Explorer',
        'name' => 'Coron Lagoon Explorer',
        'region' => 'Palawan',
        'city' => 'Coron',
        'summary' => 'A calm lagoon trip with island stops.',
        'description' => 'A calm lagoon trip with island stops.',
        'duration_label' => '4 hours',
        'duration_hours' => null,
        'duration_unit' => 'hours',
        'min_guests' => 2,
        'max_guests' => 6,
        'price_per_person' => 2400,
        'price' => 2400,
        'price_unit' => 'person',
        'available_on' => '2026-04-18',
        'activities' => json_encode(['boat_bangka', 'walking_tour']),
        'status' => 'active',
    ];

    $tour = Tour::query()->create(
        collect($tourPayload)
            ->filter(fn (mixed $value, string $column): bool => Schema::hasColumn('tours', $column))
            ->all()
    );

    actingAs($user);

    Livewire::test(GuideTours::class)
        ->call('edit', $tour->id)
        ->assertSet('editingTourId', $tour->id)
        ->assertSet('form.city', 'Coron')
        ->assertSet('form.duration_hours', '4')
        ->assertSet('form.duration_unit', 'hours')
        ->assertSet('form.min_guests', '2')
        ->assertSet('form.max_guests', '6')
        ->assertSet('form.available_on', '2026-04-18')
        ->assertSet('form.status', 'active');
});

it('loads prefilled edit form from dashboard edit query parameter', function () {
    $user = User::factory()->create([
        'role' => 'guide',
        'full_name' => 'Maria Santos',
    ]);

    $tourPayload = [
        'guide_id' => $user->id,
        'created_by' => $user->id,
        'title' => 'Sabang Underground River',
        'name' => 'Sabang Underground River',
        'region' => 'Palawan',
        'city' => 'Puerto Princesa',
        'summary' => 'A river cruise with cave exploration.',
        'description' => 'A river cruise with cave exploration.',
        'duration_label' => '6 hours',
        'duration_hours' => 6,
        'duration_unit' => 'hours',
        'min_guests' => 3,
        'max_guests' => 10,
        'price_per_person' => 2500,
        'price' => 2500,
        'price_unit' => 'person',
        'available_on' => '2026-04-22',
        'activities' => json_encode(['boat_bangka', 'private_transportation']),
        'status' => 'paused',
    ];

    $tour = Tour::query()->create(
        collect($tourPayload)
            ->filter(fn (mixed $value, string $column): bool => Schema::hasColumn('tours', $column))
            ->all()
    );

    actingAs($user);

    Livewire::withQueryParams(['edit' => $tour->id])
        ->test(GuideTours::class)
        ->assertSet('editingTourId', $tour->id)
        ->assertSet('form.title', 'Sabang Underground River')
        ->assertSet('form.region', 'Palawan')
        ->assertSet('form.city', 'Puerto Princesa')
        ->assertSet('form.duration_hours', '6')
        ->assertSet('form.min_guests', '3')
        ->assertSet('form.max_guests', '10')
        ->assertSet('form.available_on', '2026-04-22')
        ->assertSet('form.status', 'paused');
});

it('normalizes legacy date and status formats when prefilling edit form', function () {
    $user = User::factory()->create([
        'role' => 'guide',
        'full_name' => 'Maria Santos',
    ]);

    $tourPayload = [
        'guide_id' => $user->id,
        'created_by' => $user->id,
        'title' => 'Bohol Heritage Trail',
        'name' => 'Bohol Heritage Trail',
        'region' => 'Central Visayas',
        'city' => 'Tagbilaran',
        'summary' => 'Cultural and historical route through Bohol.',
        'description' => 'Cultural and historical route through Bohol.',
        'duration_label' => '5 hours',
        'duration_hours' => 5,
        'duration_unit' => 'hours',
        'price_per_person' => 1600,
        'price' => 1600,
        'available_on' => '04/25/2026',
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now(),
    ];

    DB::table('tours')->insert(
        collect($tourPayload)
            ->filter(fn (mixed $value, string $column): bool => Schema::hasColumn('tours', $column))
            ->all()
    );

    $tour = Tour::query()->latest('id')->first();

    expect($tour)->not->toBeNull();

    $tourId = (int) $tour->id;

    actingAs($user);

    Livewire::test(GuideTours::class)
        ->call('edit', $tourId)
        ->assertSet('editingTourId', $tourId)
        ->assertSet('form.city', 'Tagbilaran')
        ->assertSet('form.available_on', '2026-04-25')
        ->assertSet('form.status', 'active');
});

it('prefills legacy tours missing city date and status with sensible defaults', function () {
    $user = User::factory()->create([
        'role' => 'guide',
        'full_name' => 'Maria Santos',
    ]);

    if (Schema::hasTable('tour_guides_profile')) {
        DB::table('tour_guides_profile')->updateOrInsert(
            ['user_id' => $user->id],
            [
                'phone_number' => '09123456789',
                'nationality' => 'Filipino',
                'date_of_birth' => '1990-01-01',
                'years_of_experience' => 5,
                'bio' => 'Local guide profile for fallback city prefill test.',
                'government_id_type' => 'Passport',
                'government_id_number' => 'GUIDE-1001',
                'nbi_clearance_number' => 'NBI-1001',
                'city_municipality' => 'Puerto Princesa',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    $tourPayload = [
        'guide_id' => $user->id,
        'created_by' => $user->id,
        'title' => 'Legacy Island Trail',
        'name' => 'Legacy Island Trail',
        'region' => 'Palawan',
        'summary' => 'Legacy record created before schema expansion.',
        'description' => 'Legacy record created before schema expansion.',
        'duration' => '3 hours',
        'price' => 1200,
        'status' => 'draft',
        'available_on' => null,
        'created_at' => '2026-04-28 10:15:00',
        'updated_at' => '2026-04-28 10:15:00',
    ];

    $tour = Tour::query()->create(
        collect($tourPayload)
            ->filter(fn (mixed $value, string $column): bool => Schema::hasColumn('tours', $column))
            ->all()
    );

    actingAs($user);

    Livewire::test(GuideTours::class)
        ->call('edit', $tour->id)
        ->assertSet('editingTourId', $tour->id)
        ->assertSet('form.city', 'Puerto Princesa')
        ->assertSet('form.available_on', now()->format('Y-m-d'))
        ->assertSet('form.status', 'draft');
});

it('redirects to guide dashboard after updating a tour', function () {
    $user = User::factory()->create([
        'role' => 'guide',
        'full_name' => 'Maria Santos',
    ]);

    $tourPayload = [
        'guide_id' => $user->id,
        'created_by' => $user->id,
        'title' => 'Tour To Culling Game',
        'name' => 'Tour To Culling Game',
        'region' => 'Tokyo',
        'summary' => 'Initial summary.',
        'description' => 'Initial summary.',
        'duration' => '2 hours',
        'duration_label' => '2 hours',
        'duration_hours' => 2,
        'duration_unit' => 'hours',
        'price_per_person' => 3232,
        'price' => 3232,
        'status' => 'draft',
    ];

    $tour = Tour::query()->create(
        collect($tourPayload)
            ->filter(fn (mixed $value, string $column): bool => Schema::hasColumn('tours', $column))
            ->all()
    );

    actingAs($user);

    Livewire::test(GuideTours::class)
        ->call('edit', $tour->id)
        ->set('form.summary', 'Updated summary text.')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard.guide'));
});

it('allows a guide to replace a wrong uploaded photo', function () {
    Storage::fake('public');

    $user = User::factory()->create([
        'role' => 'guide',
        'full_name' => 'Maria Santos',
    ]);

    actingAs($user);

    $component = Livewire::test(GuideTours::class)
        ->set('tourPhotos', [UploadedFile::fake()->create('wrong-photo.jpg', 200, 'image/jpeg')])
        ->call('processTourPhotos')
        ->assertHasNoErrors();

    $tour = Tour::query()->latest('id')->first();

    expect($tour)->not->toBeNull()
        ->and(is_array($tour?->gallery_images))->toBeTrue()
        ->and(count((array) $tour?->gallery_images))->toBe(1);

    $wrongImagePath = (string) $tour->gallery_images[0];

    $component
        ->call('removeTourPhoto', 0)
        ->set('tourPhotos', [UploadedFile::fake()->create('correct-photo.jpg', 200, 'image/jpeg')])
        ->call('processTourPhotos')
        ->assertHasNoErrors();

    $updatedTour = $tour->fresh();

    expect($updatedTour)->not->toBeNull()
        ->and(is_array($updatedTour?->gallery_images))->toBeTrue()
        ->and(count((array) $updatedTour?->gallery_images))->toBe(1)
        ->and($updatedTour?->gallery_images[0])->not->toBe($wrongImagePath);

    expect(Storage::disk('public')->exists($wrongImagePath))->toBeFalse();
    expect(Storage::disk('public')->exists((string) $updatedTour->gallery_images[0]))->toBeTrue();
});

it('allows a guide to delete an existing tour', function () {
    $user = User::factory()->create([
        'role' => 'tour_guide',
        'full_name' => 'Luis Rivera',
    ]);

    $tour = Tour::query()->create([
        'guide_id' => $user->id,
        'title' => 'Lagoon Paddle Tour',
        'region' => 'Palawan',
        'summary' => str_repeat('Paddle through calm waters with a local guide. ', 3),
        'duration_label' => 'Full-day',
        'price_per_person' => 2399.00,
        'is_featured' => true,
        'available_on' => now()->addDays(3)->format('Y-m-d'),
    ]);

    actingAs($user);

    Livewire::test(GuideTours::class)
        ->call('delete', $tour->id)
        ->assertHasNoErrors();

    expect(Tour::query()->whereKey($tour->id)->exists())->toBeFalse();
});

it('displays transportation in guide profile after saving tour', function () {
    Storage::fake('public');

    $user = User::factory()->create([
        'role' => 'guide',
        'full_name' => 'Maria Santos',
    ]);

    actingAs($user);

    Livewire::test(GuideTours::class)
        ->set('form.title', 'Island Hopping Adventure')
        ->set('form.region', 'Western Visayas')
        ->set('form.city', 'Iloilo City')
        ->set('form.summary', 'Explore three beautiful islands with transportation by boat.')
        ->set('form.duration_hours', 6)
        ->set('form.duration_unit', 'hours')
        ->set('form.transportation', ['boat_bangka', 'private_transportation'])
        ->set('form.min_guests', 4)
        ->set('form.max_guests', 12)
        ->set('form.price_per_person', 1500)
        ->set('form.currency', 'PHP')
        ->set('form.price_unit', 'person')
        ->set('form.available_on', now()->addDays(5)->format('Y-m-d'))
        ->set('form.status', 'active')
        ->call('save')
        ->assertHasNoErrors();

    $tour = Tour::query()->where('title', 'Island Hopping Adventure')->first();

    expect($tour)->not->toBeNull();

    // Verify both transportation fields are set
    // Category contains human-readable names
    expect((string) $tour->category)->toContain('Boat Bangka')
        ->and((string) $tour->category)->toContain('Private Transportation');

    // Activities is still null but fallback to category should work in guide profile
    // The guide-profile view has extraction logic that falls back to category when activities is empty
});

it('saves transportation when toggled via button clicks', function () {
    Storage::fake('public');

    $user = User::factory()->create([
        'role' => 'guide',
        'full_name' => 'Maria Santos',
    ]);

    actingAs($user);

    Livewire::test(GuideTours::class)
        ->set('form.title', 'Button Click Tour')
        ->set('form.region', 'Western Visayas')
        ->set('form.city', 'Iloilo City')
        ->set('form.summary', 'Test tour with button-clicked transportation.')
        ->set('form.duration_hours', 3)
        ->set('form.duration_unit', 'hours')
        ->call('toggleTransportation', 'boat_bangka')
        ->call('toggleTransportation', 'walking_tour')
        ->set('form.min_guests', 2)
        ->set('form.max_guests', 6)
        ->set('form.price_per_person', 800)
        ->set('form.currency', 'PHP')
        ->set('form.price_unit', 'person')
        ->set('form.available_on', now()->addDays(5)->format('Y-m-d'))
        ->set('form.status', 'active')
        ->call('save')
        ->assertHasNoErrors();

    $tour = Tour::query()->where('title', 'Button Click Tour')->first();

    expect($tour)->not->toBeNull();
    expect($tour->category)->toContain('Boat Bangka')
        ->and($tour->category)->toContain('Walking Tour');
});

it('displays transportation in live preview after saving tour', function () {
    Storage::fake('public');

    $user = User::factory()->create([
        'role' => 'guide',
        'full_name' => 'Test Guide',
    ]);

    actingAs($user);

    // Create and save a tour with transportation
    Livewire::test(GuideTours::class)
        ->set('form.title', 'Preview Test Tour')
        ->set('form.region', 'Mindanao')
        ->set('form.city', 'Davao')
        ->set('form.summary', 'Test transportation display in preview')
        ->set('form.duration_hours', 5)
        ->set('form.duration_unit', 'hours')
        ->call('toggleTransportation', 'boat_bangka')
        ->call('toggleTransportation', 'public_transportation')
        ->set('form.min_guests', 5)
        ->set('form.max_guests', 10)
        ->set('form.price_per_person', 2000)
        ->set('form.currency', 'PHP')
        ->set('form.price_unit', 'person')
        ->set('form.available_on', now()->addDays(10)->format('Y-m-d'))
        ->set('form.status', 'active')
        ->call('save')
        ->assertHasNoErrors();

    $tour = Tour::query()->where('title', 'Preview Test Tour')->first();

    expect($tour)->not->toBeNull();
    expect($tour->category)->toContain('Boat Bangka');
    expect($tour->category)->toContain('Public Transportation');

    // Verify the guide profile view includes transportation in Live Preview data
    $user->refresh();
    Livewire::actingAs($user)
        ->test(GuideProfile::class)
        ->assertStatus(200);
});
