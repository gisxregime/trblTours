<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingRequest;
use App\Models\Tour;
use App\Models\TourReview;
use App\Notifications\TourActivityNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardBookingsController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user !== null, 401);
        abort_unless($user->role === 'tourist', 403);

        $tourColumns = collect(['id', 'title', 'name', 'image_path', 'image_url', 'featured_image', 'region'])
            ->filter(fn (string $column): bool => $column === 'id' || Schema::hasColumn('tours', $column))
            ->implode(',');

        $bookingRequests = BookingRequest::query()
            ->with([
                'tour:'.$tourColumns,
                'guide:id,name,full_name',
                'booking:id,booking_request_id,tourist_id,guide_id,tour_id,booking_date,total_amount,status',
                'booking.review:id,booking_id,rating,review',
            ])
            ->where('tourist_id', $user->id)
            ->latest()
            ->paginate(12);

        $selectedBookingRequestId = $request->integer('focus');

        $selectedBookingRequest = $selectedBookingRequestId > 0
            ? $bookingRequests->getCollection()->firstWhere('id', $selectedBookingRequestId)
            : null;

        if ($selectedBookingRequest === null) {
            $selectedBookingRequest = $bookingRequests->getCollection()->first(function (BookingRequest $bookingRequest): bool {
                $status = strtolower((string) ($bookingRequest->booking?->status ?? ($bookingRequest->status ?? 'pending')));

                return $status === 'completed';
            });
        }

        if ($selectedBookingRequest === null) {
            $selectedBookingRequest = $bookingRequests->getCollection()->first();
        }

        return view('dashboards.my-bookings', [
            'bookingRequests' => $bookingRequests,
            'selectedBookingRequest' => $selectedBookingRequest,
        ]);
    }

    public function cancel(Request $request, BookingRequest $bookingRequest): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user !== null, 401);
        abort_unless($user->role === 'tourist', 403);
        abort_unless((int) $bookingRequest->tourist_id === (int) $user->id, 403);

        $bookingRequest->loadMissing([
            'booking:id,booking_request_id,status',
            'tour:id,title,name',
            'guide:id,name,full_name',
        ]);

        $currentStatus = strtolower((string) ($bookingRequest->booking?->status ?? ($bookingRequest->status ?? 'pending')));

        if ($currentStatus !== 'pending') {
            return back()->withErrors([
                'booking' => 'Only pending bookings can be cancelled.',
            ]);
        }

        $bookingRequest->update(['status' => 'cancelled']);

        if ($bookingRequest->guide !== null) {
            $tourTitle = $bookingRequest->tour?->title ?: $bookingRequest->tour?->name ?: 'a tour';

            $bookingRequest->guide->notify(new TourActivityNotification(
                title: 'Booking request cancelled',
                message: ($user->full_name ?: $user->name ?: 'A tourist').' cancelled their pending booking for "'.$tourTitle.'".',
                icon: 'fa-calendar-xmark',
                type: 'booking',
                actionUrl: route('dashboard.guide.requests'),
            ));
        }

        return redirect()->route('dashboard.my-bookings')->with('status', 'Pending booking cancelled successfully.');
    }

    public function storeRating(Request $request, Booking $booking): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user !== null, 401);
        abort_unless($user->role === 'tourist', 403);
        abort_unless((int) $booking->tourist_id === (int) $user->id, 403);

        if ($booking->status !== 'completed') {
            return back()->withErrors([
                'rating' => 'You can only rate tours after the booking is completed.',
            ]);
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['nullable', 'string', 'max:1000'],
        ]);

        TourReview::query()->updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'tourist_id' => $user->id,
                'guide_id' => $booking->guide_id,
                'tour_id' => $booking->tour_id,
                'rating' => (int) $validated['rating'],
                'review' => $validated['review'] ?? null,
            ]
        );

        $averageRating = TourReview::query()
            ->where('tour_id', $booking->tour_id)
            ->avg('rating');

        if ($averageRating !== null) {
            Tour::query()
                ->whereKey($booking->tour_id)
                ->update(['rating' => round((float) $averageRating, 1)]);
        }

        if ($booking->guide !== null) {
            $tourName = $booking->tour?->title ?: $booking->tour?->name ?: 'your tour';

            $booking->guide->notify(new TourActivityNotification(
                title: 'New tour review received',
                message: ($user->full_name ?: $user->name ?: 'A tourist').' left a '.(int) $validated['rating'].'/5 review for "'.$tourName.'".',
                icon: 'fa-star',
                type: 'review',
                actionUrl: route('dashboard.guide.requests'),
            ));
        }

        return redirect()
            ->route('dashboard.my-bookings', ['focus' => (int) $booking->booking_request_id])
            ->with('status', 'Thanks for rating your completed booking.');
    }
}
