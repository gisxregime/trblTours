<?php

namespace App\Livewire;

use App\Models\Tour;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Livewire\WithPagination;

class ExploreToursFeed extends Component
{
    use WithPagination;

    public string $locationPath = '';

    public string $sortBy = 'latest';

    public string $context = 'home';

    public bool $showFilters = false;

    public bool $showExploreRequestButton = false;

    // Using query string to keep track of filters like JS dashboard did
    protected $queryString = ['locationPath' => ['except' => '', 'as' => 'location'], 'sortBy' => ['except' => 'latest', 'as' => 'sort_by']];

    public function mount(?string $context = null): void
    {
        $routeName = request()->route()?->getName() ?? '';

        if ($context !== null && $context !== '') {
            $this->context = $context;
        } elseif ($routeName === 'explore-tours' || request()->is('explore-tours*')) {
            $this->context = 'explore';
        } elseif (str_starts_with($routeName, 'dashboard.')) {
            $this->context = 'dashboard';
        } else {
            $this->context = 'home';
        }

        $this->showFilters = in_array($this->context, ['explore', 'dashboard'], true);
        $this->showExploreRequestButton = in_array($this->context, ['explore', 'dashboard'], true);

        // Keep legacy landing-page region filtering support for tests and shared links.
        if ($this->locationPath === '' && request()->filled('region')) {
            $this->locationPath = trim((string) request()->string('region'));
        }
    }

    public function filter(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['locationPath', 'sortBy']);
        $this->resetPage();
    }

    public function render(): View
    {
        $tourQuery = Tour::query()
            ->with(['marketplaceGuide:id,name,full_name,role,status']);

        if (Auth::check() && Schema::hasTable('tour_likes')) {
            $tourQuery->withExists([
                'likedByUsers as liked_by_current_user' => fn (Builder $builder) => $builder->where('users.id', (int) Auth::id()),
            ]);
        }

        if ($this->context === 'home') {
            $tours = $this->curatedFeaturedTours($tourQuery);

            return view('livewire.explore-tours-feed', [
                'tours' => $tours,
            ]);
        }

        $tours = $tourQuery
            ->when($this->locationPath !== '', function ($query) {
                $query->where(function ($tourQuery) {
                    $tourQuery->where('region', 'like', '%'.$this->locationPath.'%')
                        ->orWhere('city', 'like', '%'.$this->locationPath.'%');
                });
            })
            ->when($this->sortBy === 'price_low_high', function ($query) {
                $query->orderBy('price_per_person', 'asc');
            })
            ->when($this->sortBy === 'price_high_low', function ($query) {
                $query->orderBy('price_per_person', 'desc');
            }, function ($query) {
                $query->latest();
            })
            ->paginate(9);

        return view('livewire.explore-tours-feed', [
            'tours' => $tours,
        ]);
    }

    private function curatedFeaturedTours(Builder $tourQuery)
    {
        $hasBookingsTable = Schema::hasTable('bookings');
        $hasBookingRequestsTable = Schema::hasTable('booking_requests');
        $hasTourLikesTable = Schema::hasTable('tour_likes');

        if (Schema::hasColumn('tours', 'status')) {
            $tourQuery->where('status', 'active');
        }

        if (Schema::hasColumn('tours', 'admin_approved')) {
            $tourQuery->where('admin_approved', true);
        }

        if (! $hasBookingsTable && ! $hasBookingRequestsTable && ! $hasTourLikesTable) {
            return collect();
        }

        if ($hasBookingsTable) {
            $tourQuery->withCount([
                'bookings as bookings_count',
                'bookings as completed_bookings_count' => fn (Builder $query) => $query->where('status', 'completed'),
            ]);
        }

        if ($hasBookingRequestsTable) {
            $tourQuery->withCount([
                'bookingRequests as demand_requests_count' => fn (Builder $query) => $query->whereIn('status', ['pending', 'accepted']),
            ]);
        }

        if ($hasTourLikesTable) {
            $tourQuery->withCount('likedByUsers as likes_count');
        }

        $tourQuery->where(function (Builder $query) use ($hasBookingsTable, $hasBookingRequestsTable, $hasTourLikesTable): void {
            $hasCondition = false;

            if ($hasBookingsTable) {
                $query->whereHas('bookings', fn (Builder $bookingQuery) => $bookingQuery->whereIn('status', ['confirmed', 'in_progress', 'completed']));
                $hasCondition = true;
            }

            if ($hasBookingRequestsTable) {
                $method = $hasCondition ? 'orWhereHas' : 'whereHas';
                $query->{$method}('bookingRequests', fn (Builder $bookingRequestQuery) => $bookingRequestQuery->whereIn('status', ['pending', 'accepted']));
                $hasCondition = true;
            }

            if ($hasTourLikesTable) {
                $method = $hasCondition ? 'orWhereHas' : 'whereHas';
                $query->{$method}('likedByUsers');
            }
        });

        if ($hasBookingsTable) {
            $tourQuery
                ->orderByDesc('completed_bookings_count')
                ->orderByDesc('bookings_count');
        }

        if ($hasBookingRequestsTable) {
            $tourQuery->orderByDesc('demand_requests_count');
        }

        if ($hasTourLikesTable) {
            $tourQuery->orderByDesc('likes_count');
        }

        if (Schema::hasColumn('tours', 'rating')) {
            $tourQuery->orderByDesc('rating');
        }

        return $tourQuery
            ->latest()
            ->limit(6)
            ->get();
    }
}
