<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingRequest;
use App\Models\Tour;
use App\Models\TourReview;
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

        return view('dashboards.my-bookings', [
            'bookingRequests' => $bookingRequests,
        ]);
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

        return redirect()->route('dashboard.my-bookings')->with('status', 'Thanks for rating your completed booking.');
    }
}
