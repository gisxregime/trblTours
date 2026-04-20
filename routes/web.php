<?php

use App\Http\Controllers\Auth\SignupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuideProfileController;
use App\Http\Controllers\GuideSettingsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicGuideProfileController;
use App\Livewire\Guide\GuideAvailabilityManager;
use App\Livewire\Guide\GuideBookingRequests;
use App\Livewire\Guide\GuideDashboard;
use App\Livewire\Guide\GuideMessages;
use App\Livewire\Guide\GuideProfile;
use App\Livewire\Guide\GuideTours;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

Route::redirect('/signup.php', '/signup');

Route::middleware('guest')->group(function () {
    Route::get('/signup', [SignupController::class, 'create'])->name('signup.start');
    Route::post('/signup/email', [SignupController::class, 'storeEmail'])->name('signup.email.store');
    Route::get('/signup/{token}/otp', [SignupController::class, 'createOtp'])->name('signup.otp');
    Route::post('/signup/{token}/otp', [SignupController::class, 'storeOtp'])->name('signup.otp.store');
    Route::post('/signup/{token}/otp/resend', [SignupController::class, 'resendOtp'])
        ->middleware('throttle:6,1')
        ->name('signup.otp.resend');
    Route::get('/signup/{token}/role', [SignupController::class, 'createRole'])->name('signup.role');
    Route::post('/signup/{token}/role', [SignupController::class, 'storeRole'])->name('signup.role.store');
    Route::get('/signup/{token}/details', [SignupController::class, 'createDetails'])->name('signup.details');
    Route::post('/signup/{token}/details', [SignupController::class, 'storeDetails'])->name('signup.details.store');
});

Route::get('/', function (Request $request) {
    $regions = [
        'National Capital Region',
        'Cordillera Administrative Region',
        'Ilocos Region',
        'Cagayan Valley',
        'Central Luzon',
        'Calabarzon',
        'Mimaropa',
        'Bicol Region',
        'Western Visayas',
        'Central Visayas',
        'Eastern Visayas',
        'Zamboanga Peninsula',
        'Northern Mindanao',
        'Davao Region',
        'Soccsksargen',
        'Caraga',
        'Bangsamoro Autonomous Region in Muslim Mindanao',
    ];

    $query = trim((string) $request->string('q')->toString());
    $selectedRegion = $request->string('region')->toString();
    $selectedDate = $request->string('date')->toString();

    $tours = collect();

    if (Schema::hasTable('tours')) {
        $tourQuery = Tour::query()
            ->with('guide:id,full_name')
            ->where('is_featured', true)
            ->when($query !== '', function ($builder) use ($query): void {
                $builder->where(function ($search) use ($query): void {
                    $search->where('name', 'like', '%'.$query.'%')
                        ->orWhere('description', 'like', '%'.$query.'%')
                        ->orWhere('region', 'like', '%'.$query.'%')
                        ->orWhereHas('guide', function ($guideQuery) use ($query): void {
                            $guideQuery->where('full_name', 'like', '%'.$query.'%');
                        });
                });
            })
            ->when($selectedRegion !== '' && $selectedRegion !== 'all', fn ($builder) => $builder->where('region', $selectedRegion));

        if ($selectedDate !== '' && Schema::hasColumn('tours', 'available_on')) {
            $tourQuery->whereDate('available_on', '>=', $selectedDate);
        }

        if (Schema::hasColumn('tours', 'rating')) {
            $tourQuery->orderByDesc('rating');
        }

        $tourQuery->orderBy(Schema::hasColumn('tours', 'name') ? 'name' : 'id')
            ->limit(6);

        $tours = $tourQuery->get();
    }

    return view('welcome', [
        'tours' => $tours,
        'regions' => $regions,
        'filters' => [
            'q' => $query,
            'region' => $selectedRegion ?: 'all',
            'date' => $selectedDate,
        ],
    ]);
});

// Public guide profile page
Route::get('/guide/{guide}', [PublicGuideProfileController::class, 'show'])->name('guide.profile');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'redirect'])->name('dashboard');
    Route::get('/dashboard/tourist', [DashboardController::class, 'tourist'])
        ->middleware('role:tourist')
        ->name('dashboard.tourist');
    Route::get('/dashboard/guide', GuideDashboard::class)
        ->middleware('role:guide')
        ->name('dashboard.guide');
    Route::get('/dashboard/guide/profile', GuideProfile::class)
        ->middleware('role:guide')
        ->name('dashboard.guide.profile.show');
    Route::get('/dashboard/guide/profile/edit', [GuideProfileController::class, 'edit'])
        ->middleware('role:guide')
        ->name('dashboard.guide.profile.edit');
    Route::get('/dashboard/guide/settings', [GuideSettingsController::class, 'edit'])
        ->middleware('role:guide')
        ->name('dashboard.guide.settings');
    Route::put('/dashboard/guide/settings/password', [GuideSettingsController::class, 'updatePassword'])
        ->middleware('role:guide')
        ->name('dashboard.guide.settings.password.update');
    Route::delete('/dashboard/guide/settings/account', [GuideSettingsController::class, 'destroy'])
        ->middleware('role:guide')
        ->name('dashboard.guide.settings.destroy');
    Route::patch('/dashboard/guide/profile', [GuideProfileController::class, 'update'])
        ->middleware('role:guide')
        ->name('dashboard.guide.profile.update');
    Route::get('/dashboard/guide/tours', GuideTours::class)
        ->middleware('role:guide')
        ->name('dashboard.guide.tours');
    Route::get('/dashboard/guide/availability', GuideAvailabilityManager::class)
        ->middleware('role:guide')
        ->name('dashboard.guide.availability');
    Route::get('/dashboard/guide/requests', GuideBookingRequests::class)
        ->middleware('role:guide')
        ->name('dashboard.guide.requests');
    Route::get('/dashboard/guide/messages', GuideMessages::class)
        ->middleware('role:guide')
        ->name('dashboard.guide.messages');
    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])
        ->middleware('role:admin')
        ->name('dashboard.admin');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
