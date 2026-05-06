<?php

namespace App\Livewire;

use App\Models\Tour;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
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
        $this->applyFilters();
    }

    public function updatedLocationPath(): void
    {
        if (! $this->showFilters) {
            return;
        }

        $this->resetPage();
    }

    public function updatedSortBy(string $value): void
    {
        if (! in_array($value, ['latest', 'price_low_high', 'price_high_low'], true)) {
            $this->sortBy = 'latest';
        }

        if (! $this->showFilters) {
            return;
        }

        $this->resetPage();
    }

    public function applyFilters(): void
    {
        $this->locationPath = trim($this->locationPath);

        if (! in_array($this->sortBy, ['latest', 'price_low_high', 'price_high_low'], true)) {
            $this->sortBy = 'latest';
        }

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

        $availableSearchColumns = $this->availableSearchColumns();
        $canSearchGuideRegion = Schema::hasColumn('users', 'region');
        $availableSortPriceColumns = $this->availablePriceColumns();

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
            ->when($this->locationPath !== '', function ($query) use ($availableSearchColumns, $canSearchGuideRegion): void {
                $searchTerm = trim($this->locationPath);
                $searchTokens = collect(preg_split('/[\s,]+/', $searchTerm))
                    ->filter(fn (mixed $token): bool => is_string($token) && $token !== '')
                    ->map(fn (string $token): string => mb_strtolower(trim($token)))
                    ->filter(fn (string $token): bool => $token !== '')
                    ->values();

                if ($searchTokens->isEmpty()) {
                    return;
                }

                $searchableColumns = $availableSearchColumns;
                $searchGuideRegion = $canSearchGuideRegion;

                if ($searchableColumns->isEmpty() && ! $searchGuideRegion) {
                    return;
                }

                $searchTokens->each(function (string $token) use ($query, $searchableColumns, $searchGuideRegion): void {
                    $query->where(function ($tourQuery) use ($token, $searchableColumns, $searchGuideRegion): void {
                        $pattern = '%'.$token.'%';

                        $hasPreviousCondition = false;

                        foreach ($searchableColumns as $column) {
                            if (! $hasPreviousCondition) {
                                $tourQuery->whereRaw('LOWER('.$column.') LIKE ?', [$pattern]);
                                $hasPreviousCondition = true;

                                continue;
                            }

                            $tourQuery->orWhereRaw('LOWER('.$column.') LIKE ?', [$pattern]);
                        }

                        if ($searchGuideRegion) {
                            if ($hasPreviousCondition) {
                                $tourQuery->orWhereHas('marketplaceGuide', function (Builder $guideQuery) use ($pattern): void {
                                    $guideQuery->whereRaw('LOWER(region) LIKE ?', [$pattern]);
                                });

                                return;
                            }

                            $tourQuery->whereHas('marketplaceGuide', function (Builder $guideQuery) use ($pattern): void {
                                $guideQuery->whereRaw('LOWER(region) LIKE ?', [$pattern]);
                            });
                        }
                    });
                });
            })
            ->when($this->sortBy === 'price_low_high', function ($query) {
                $this->applyPriceSort($query, $this->availablePriceColumns(), 'asc');
            })
            ->when($this->sortBy === 'price_high_low', function ($query) {
                $this->applyPriceSort($query, $this->availablePriceColumns(), 'desc');
            }, function ($query): void {
                $query->orderByDesc('id');
            })
            ->when(
                in_array($this->sortBy, ['price_low_high', 'price_high_low'], true) && $availableSortPriceColumns->isNotEmpty(),
                fn (Builder $query): Builder => $query->latest(),
            )
            ->paginate(9);

        return view('livewire.explore-tours-feed', [
            'tours' => $tours,
        ]);
    }

    private function availablePriceColumns(): Collection
    {
        // Keep sort order consistent with displayed card pricing fallback.
        return collect(['price', 'price_per_person', 'base_price', 'budget'])
            ->filter(fn (string $column): bool => Schema::hasColumn('tours', $column))
            ->values();
    }

    private function availableSearchColumns(): Collection
    {
        return collect(['region', 'city', 'title', 'name', 'summary', 'description'])
            ->filter(fn (string $column): bool => Schema::hasColumn('tours', $column))
            ->values();
    }

    private function applyPriceSort(Builder|QueryBuilder $query, Collection $columns, string $direction): void
    {
        if ($columns->isEmpty()) {
            return;
        }

        if ($columns->count() === 1) {
            $query->orderBy((string) $columns->first(), $direction);

            return;
        }

        $query->orderByRaw('COALESCE('.$columns->implode(', ').', 0) '.$direction);
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
