<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardBookingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardLikesController;
use App\Http\Controllers\DashboardMessagesController;
use App\Http\Controllers\DashboardMyPostsController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\GuideDashboardController;
use App\Http\Controllers\GuideProfileController;
use App\Http\Controllers\GuideSettingsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicGuideProfileController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\SettingsController;
use App\Livewire\Guide\GuideAvailabilityManager;
use App\Livewire\Guide\GuideBookingRequests;
use App\Livewire\Guide\GuideMessages;
use App\Livewire\Guide\GuideProfile;
use App\Livewire\Guide\GuideTours;
use App\Models\GuideAvailability;
use App\Models\Tour;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/explore-tours', 'explore-tours')->name('explore-tours');

Route::get('/tours/{tour}', function (Tour $tour) {
    $tour->loadMissing('marketplaceGuide:id,name,full_name');

    $source = request()->string('from')->toString();
    if (! in_array($source, ['home', 'explore', 'dashboard'], true)) {
        $source = 'explore';
    }

    $isTourLiked = false;
    $currentUser = request()->user();

    if ($currentUser !== null && Schema::hasTable('tour_likes')) {
        $isTourLiked = $currentUser->likedTours()->whereKey($tour->id)->exists();
    }

    $availabilityOptions = collect();
    $hasGuideAvailabilityRules = false;

    if (Schema::hasTable('guide_availability') && $tour->guide_id !== null) {
        $availabilityOptions = GuideAvailability::query()
            ->where('guide_id', (int) $tour->guide_id)
            ->whereDate('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->limit(120)
            ->get(['date', 'status', 'note', 'special_price']);

        $hasGuideAvailabilityRules = $availabilityOptions->isNotEmpty();
    }

    if ($availabilityOptions->isEmpty()) {
        $defaultDates = collect([
            $tour->available_on ? Carbon::parse((string) $tour->available_on)->toDateString() : null,
            now()->addDays(1)->toDateString(),
            now()->addDays(2)->toDateString(),
            now()->addDays(3)->toDateString(),
            now()->addDays(7)->toDateString(),
            now()->addDays(14)->toDateString(),
        ])->filter();

        $availabilityOptions = $defaultDates
            ->unique()
            ->map(fn (string $date): array => [
                'date' => $date,
                'status' => 'available',
                'note' => null,
                'special_price' => null,
            ]);
    }

    return view('tours.show', [
        'tour' => $tour,
        'source' => $source,
        'isTourLiked' => $isTourLiked,
        'availabilityOptions' => $availabilityOptions,
        'hasGuideAvailabilityRules' => $hasGuideAvailabilityRules,
    ]);
})->name('tours.show');

Route::get('/guide/{guide}', [PublicGuideProfileController::class, 'show'])
    ->name('guide.profile');

Route::get('/dashboard', [DashboardController::class, 'redirect'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard/tourist', [DashboardController::class, 'tourist'])->name('dashboard.tourist');
    Route::post('/feed/filter', [FeedController::class, 'filter'])->name('feed.filter');
    Route::post('/requests', [RequestController::class, 'store'])->name('requests.store');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');

    // Nav links
    Route::get('/dashboard/my-posts', [DashboardMyPostsController::class, 'index'])->name('dashboard.my-posts');
    Route::patch('/dashboard/my-posts/{touristRequest}', [DashboardMyPostsController::class, 'update'])->name('dashboard.my-posts.update');
    Route::delete('/dashboard/my-posts/{touristRequest}', [DashboardMyPostsController::class, 'destroy'])->name('dashboard.my-posts.delete');
    Route::post('/dashboard/my-posts/{touristRequest}/complete', [DashboardMyPostsController::class, 'markComplete'])->name('dashboard.my-posts.complete');
    Route::get('/dashboard/my-posts/{touristRequest}/comments', [DashboardMyPostsController::class, 'comments'])->name('dashboard.my-posts.comments');
    Route::post('/dashboard/my-posts/{touristRequest}/comments', [DashboardMyPostsController::class, 'storeComment'])->name('dashboard.my-posts.comments.store');
    Route::post('/dashboard/my-posts/{touristRequest}/comments/{comment}/select-guide', [DashboardMyPostsController::class, 'selectGuide'])->name('dashboard.my-posts.comments.select-guide');
    Route::post('/dashboard/my-posts/{touristRequest}/comments/{comment}/report', [DashboardMyPostsController::class, 'reportComment'])->name('dashboard.my-posts.comments.report');
    Route::get('/dashboard/my-bookings', [DashboardBookingsController::class, 'index'])->name('dashboard.my-bookings');
    Route::patch('/dashboard/my-bookings/{bookingRequest}/cancel', [DashboardBookingsController::class, 'cancel'])->name('dashboard.my-bookings.cancel');
    Route::post('/dashboard/my-bookings/{booking}/rating', [DashboardBookingsController::class, 'storeRating'])->name('dashboard.my-bookings.rating');
    Route::get('/dashboard/likes', [DashboardLikesController::class, 'index'])->name('dashboard.likes');
    Route::post('/dashboard/likes/tours/{tour}/toggle', [DashboardLikesController::class, 'toggle'])->name('dashboard.likes.toggle');
    Route::get('/dashboard/messages', [DashboardMessagesController::class, 'index'])->name('dashboard.messages');
    Route::get('/dashboard/messages/{conversation}', [DashboardMessagesController::class, 'show'])->name('dashboard.messages.show');
    Route::post('/dashboard/messages/{conversation}/messages', [DashboardMessagesController::class, 'store'])->name('dashboard.messages.store');
    Route::post('/dashboard/messages/{conversation}/read', [DashboardMessagesController::class, 'markRead'])->name('dashboard.messages.read');

    Route::get('/dashboard/guide', [GuideDashboardController::class, 'index'])->name('dashboard.guide');
    Route::post('/dashboard/guide/request-posts/{touristRequest}/comments', [GuideDashboardController::class, 'storeRequestComment'])->name('dashboard.guide.request-posts.comments.store');
    Route::post('/dashboard/guide/listings', [GuideDashboardController::class, 'storeListing'])->name('dashboard.guide.listings.store');
    Route::delete('/dashboard/guide/listings/{tour}', [GuideDashboardController::class, 'destroyListing'])->name('dashboard.guide.listings.destroy');
    Route::get('/dashboard/guide/profile', GuideProfile::class)->name('dashboard.guide.profile.show');
    Route::get('/dashboard/guide/profile/edit', [GuideProfileController::class, 'edit'])->name('dashboard.guide.profile.edit');
    Route::patch('/dashboard/guide/profile', [GuideProfileController::class, 'update'])->name('dashboard.guide.profile.update');
    Route::get('/dashboard/guide/tours', GuideTours::class)->name('dashboard.guide.tours');
    Route::get('/dashboard/guide/availability', GuideAvailabilityManager::class)->name('dashboard.guide.availability');
    Route::get('/dashboard/guide/messages', GuideMessages::class)->name('dashboard.guide.messages');
    Route::get('/dashboard/guide/requests', GuideBookingRequests::class)->name('dashboard.guide.requests');
    Route::get('/dashboard/guide/settings', [GuideSettingsController::class, 'edit'])->name('dashboard.guide.settings');
    Route::put('/dashboard/guide/settings/password', [GuideSettingsController::class, 'updatePassword'])->name('dashboard.guide.settings.password.update');
    Route::delete('/dashboard/guide/settings', [GuideSettingsController::class, 'destroy'])->name('dashboard.guide.settings.destroy');
    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])->name('dashboard.admin');
    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');

    Route::prefix('/api')->name('dashboard.api.')->group(function () {
        Route::get('/tours/search', [DashboardController::class, 'touristApiTours'])->name('tours.search');
        Route::get('/tours', [DashboardController::class, 'touristApiTours'])->name('tours');
        Route::get('/requests', [DashboardController::class, 'touristApiRequests'])->name('requests');
        Route::get('/my-posts', [DashboardController::class, 'touristApiMyPosts'])->name('my-posts');
        Route::patch('/requests/{bookingRequest}', [DashboardController::class, 'touristApiUpdateMyPost'])->name('requests.update');
        Route::delete('/requests/{bookingRequest}', [DashboardController::class, 'touristApiDeleteMyPost'])->name('requests.delete');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
