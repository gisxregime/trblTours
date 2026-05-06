<?php

namespace App\Livewire\Guide;

use App\Models\Booking;
use App\Models\BookingRequest;
use App\Models\TourReview;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class GuideDashboard extends Component
{
    /**
     * @var array<int, string>
     */
    public array $pendingActions = [];

    /**
     * @var array<int, string>
     */
    public array $customMessages = [];

    public ?string $actionMessage = null;

    public function acceptPending(int $bookingRequestId): void
    {
        $this->markPendingBooking($bookingRequestId, 'accepted');
    }

    public function declinePending(int $bookingRequestId): void
    {
        $this->markPendingBooking($bookingRequestId, 'declined');
    }

    public function render()
    {
        $user = Auth::user();
        abort_if($user === null, 401);
        abort_unless(in_array($user->role, ['guide', 'tour_guide'], true), 403);

        $guideId = (int) $user->id;
        $today = now()->toDateString();

        $pendingBookings = collect();
        $ongoingTours = collect();
        $declinedBookings = collect();
        $historyBookings = collect();

        if (Schema::hasTable('booking_requests')) {
            $pendingBookings = BookingRequest::query()
                ->with([
                    'tour:id,title,name',
                    'tourist:id,name,full_name',
                ])
                ->where('guide_id', $guideId)
                ->where('status', 'pending')
                ->orderBy('requested_date')
                ->orderByDesc('id')
                ->get();

            $ongoingTours = BookingRequest::query()
                ->with([
                    'tour:id,title,name',
                    'tourist:id,name,full_name',
                ])
                ->where('guide_id', $guideId)
                ->whereIn('status', ['accepted', 'confirmed', 'in_progress'])
                ->whereDate('requested_date', '>=', $today)
                ->orderBy('requested_date')
                ->orderByDesc('id')
                ->get();

            $declinedBookings = BookingRequest::query()
                ->with([
                    'tour:id,title,name',
                    'tourist:id,name,full_name',
                ])
                ->where('guide_id', $guideId)
                ->where('status', 'declined')
                ->orderByDesc('requested_date')
                ->orderByDesc('id')
                ->get();

            $historyBookings = BookingRequest::query()
                ->with([
                    'tour:id,title,name',
                    'tourist:id,name,full_name',
                    'booking:id,booking_request_id,status',
                    'booking.review:id,booking_id,rating',
                ])
                ->where('guide_id', $guideId)
                ->where(function ($query) use ($today): void {
                    $query
                        ->whereIn('status', ['completed', 'declined'])
                        ->orWhere(function ($acceptedQuery) use ($today): void {
                            $acceptedQuery
                                ->where('status', 'accepted')
                                ->whereDate('requested_date', '<', $today);
                        });
                })
                ->orderByDesc('requested_date')
                ->orderByDesc('id')
                ->get();
        }

        $acceptedVisualBookings = $pendingBookings
            ->filter(fn (BookingRequest $booking): bool => ($this->pendingActions[$booking->id] ?? null) === 'accepted')
            ->map(function (BookingRequest $booking): BookingRequest {
                $booking->setAttribute('status', 'accepted');

                return $booking;
            })
            ->values();

        $declinedVisualBookings = $pendingBookings
            ->filter(fn (BookingRequest $booking): bool => ($this->pendingActions[$booking->id] ?? null) === 'declined')
            ->map(function (BookingRequest $booking): BookingRequest {
                $booking->setAttribute('status', 'declined');

                return $booking;
            })
            ->values();

        $pendingBookings = $pendingBookings
            ->reject(fn (BookingRequest $booking): bool => isset($this->pendingActions[$booking->id]))
            ->values();

        $acceptedOngoingVisual = $acceptedVisualBookings
            ->filter(function (BookingRequest $booking) use ($today): bool {
                return optional($booking->requested_date)->toDateString() >= $today;
            })
            ->values();

        $ongoingTours = $ongoingTours
            ->concat($acceptedOngoingVisual)
            ->unique('id')
            ->values();

        $declinedBookings = $declinedVisualBookings
            ->concat($declinedBookings)
            ->unique('id')
            ->values();

        $historyBookings = $acceptedVisualBookings
            ->concat($declinedVisualBookings)
            ->concat($historyBookings)
            ->unique('id')
            ->sortByDesc(fn (BookingRequest $booking): int => (int) optional($booking->requested_date)->getTimestamp())
            ->values();

        $completedTours = TourReview::query()
            ->with([
                'tourist:id,name,full_name',
                'tour:id,title,name',
                'booking:id,booking_date,status',
            ])
            ->where('guide_id', $guideId)
            ->when(Schema::hasTable('bookings') && Schema::hasColumn('bookings', 'status'), function ($query): void {
                $query->whereHas('booking', function ($bookingQuery): void {
                    $bookingQuery->where('status', 'completed');
                });
            })
            ->latest('id')
            ->get();

        $totalEarnings = 0.0;

        if (Schema::hasTable('bookings') && Schema::hasColumn('bookings', 'net_amount')) {
            $totalEarnings = (float) Booking::query()
                ->where('guide_id', $guideId)
                ->where('status', 'completed')
                ->sum('net_amount');
        } elseif (Schema::hasTable('booking_requests')) {
            $totalEarnings = (float) BookingRequest::query()
                ->where('guide_id', $guideId)
                ->whereIn('status', ['accepted', 'completed'])
                ->sum('total_price');
        }

        $averageRatings = round((float) TourReview::query()->where('guide_id', $guideId)->avg('rating'), 1);
        $totalFeedbacks = (int) TourReview::query()->where('guide_id', $guideId)->count();
        $totalBookings = Schema::hasTable('booking_requests')
            ? (int) BookingRequest::query()->where('guide_id', $guideId)->count()
            : 0;

        return view('livewire.guide.guide-dashboard', [
            'totalEarnings' => $totalEarnings,
            'averageRatings' => $averageRatings,
            'totalFeedbacks' => $totalFeedbacks,
            'totalBookings' => $totalBookings,
            'pendingBookings' => $pendingBookings,
            'ongoingTours' => $ongoingTours,
            'completedTours' => $completedTours,
            'declinedBookings' => $declinedBookings,
            'historyBookings' => $historyBookings,
        ]);
    }

    private function markPendingBooking(int $bookingRequestId, string $status): void
    {
        if (! in_array($status, ['accepted', 'declined'], true)) {
            return;
        }

        $user = Auth::user();
        abort_if($user === null, 401);

        if (! Schema::hasTable('booking_requests')) {
            return;
        }

        $booking = BookingRequest::query()
            ->with('tourist:id,name,full_name')
            ->where('guide_id', (int) $user->id)
            ->where('status', 'pending')
            ->whereKey($bookingRequestId)
            ->first();

        if (! $booking) {
            return;
        }

        $touristName = $booking->tourist?->full_name ?? $booking->tourist?->name ?? 'Tourist';

        if ($status === 'accepted') {
            $this->actionMessage = "✓ Accepted {$touristName} — message sent to tourist";
        } else {
            $this->actionMessage = "✗ Declined {$touristName} — message sent to tourist";
        }

        $this->pendingActions[$bookingRequestId] = $status;
    }
}
