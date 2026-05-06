<?php

namespace App\Http\Controllers;

use App\Models\BookingRequest;
use App\Models\GuideAvailability;
use App\Models\Tour;
use App\Models\User;
use App\Notifications\TourActivityNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class BookingController extends Controller
{
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        /** @var User|null $user */
        $user = Auth::user();
        abort_unless($user?->role === 'tourist', 403);

        $validated = $request->validate([
            'tour_id' => 'required|exists:tours,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'group_size' => 'required|integer|min:1|max:20',
            'special_requests' => 'nullable|string|max:500',
        ]);

        $tour = Tour::query()->findOrFail((int) $validated['tour_id']);
        $guideId = $tour->guide_id ? (int) $tour->guide_id : null;

        if (! $guideId) {
            $errorMessage = 'This tour is currently unavailable for booking.';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $errorMessage,
                ], 422);
            }

            return back()->withErrors(['tour_id' => $errorMessage])->withInput();
        }

        $requestedDate = (string) $validated['booking_date'];
        $availability = null;
        $slotLimit = null;
        $reservedSlots = 0;
        $remainingSlots = null;

        if (Schema::hasTable('guide_availability')) {
            $availabilityQuery = GuideAvailability::query()
                ->where('guide_id', $guideId)
                ->whereDate('date', '>=', now()->toDateString());

            $hasConfiguredAvailability = (clone $availabilityQuery)->exists();

            if ($hasConfiguredAvailability) {
                $availability = (clone $availabilityQuery)
                    ->whereDate('date', $requestedDate)
                    ->first();

                $isUnavailable = $availability === null || $availability->status === 'fully_booked';

                if ($isUnavailable) {
                    $errorMessage = 'Selected date is unavailable. Please choose a different date from the availability list.';

                    if ($request->expectsJson()) {
                        return response()->json([
                            'message' => $errorMessage,
                        ], 422);
                    }

                    return back()->withErrors(['booking_date' => $errorMessage])->withInput();
                }

                $slotLimit = $availability->slots !== null ? (int) $availability->slots : null;

                if ($slotLimit !== null && Schema::hasTable('booking_requests')) {
                    $reservedSlots = (int) BookingRequest::query()
                        ->where('guide_id', $guideId)
                        ->whereDate('requested_date', $requestedDate)
                        ->whereIn('status', ['pending', 'accepted'])
                        ->sum('group_size');

                    $remainingSlots = max($slotLimit - $reservedSlots, 0);
                }

                $requestedGroupSize = (int) $validated['group_size'];

                if ($availability->status === 'limited_slots' && ($slotLimit === null || $slotLimit <= 0)) {
                    $errorMessage = 'Selected date has no configured slot capacity. Please choose another available date.';

                    if ($request->expectsJson()) {
                        return response()->json([
                            'message' => $errorMessage,
                        ], 422);
                    }

                    return back()->withErrors(['booking_date' => $errorMessage])->withInput();
                }

                if ($remainingSlots !== null && $requestedGroupSize > $remainingSlots) {
                    $errorMessage = $remainingSlots > 0
                        ? 'Only '.$remainingSlots.' slot'.($remainingSlots === 1 ? '' : 's').' remaining on this date. Please reduce group size or pick another date.'
                        : 'Selected date is fully booked. Please choose a different available date.';

                    if ($request->expectsJson()) {
                        return response()->json([
                            'message' => $errorMessage,
                        ], 422);
                    }

                    return back()->withErrors(['booking_date' => $errorMessage])->withInput();
                }
            }
        }

        $pricePerPerson = (float) ($availability?->special_price ?? ($tour->price_per_person ?? $tour->price ?? $tour->base_price ?? 0));

        $bookingRequest = BookingRequest::query()->create([
            'tourist_id' => $user->id,
            'guide_id' => $guideId,
            'tour_id' => $tour->id,
            'requested_date' => $requestedDate,
            'group_size' => (int) $validated['group_size'],
            'total_price' => $pricePerPerson * (int) $validated['group_size'],
            'special_requests' => $validated['special_requests'] ?? null,
            'status' => 'pending',
        ]);

        $guide = User::query()->find($guideId);

        if ($guide) {
            $guide->notify(new TourActivityNotification(
                title: 'New booking request',
                message: ($user->full_name ?: $user->name ?: 'A tourist').' requested "'.($tour->title ?: $tour->name ?: 'your tour').'".',
                icon: 'fa-calendar-check',
                type: 'booking',
                actionUrl: route('dashboard.guide.requests'),
            ));
        }

        $user->notify(new TourActivityNotification(
            title: 'Booking sent',
            message: 'Your booking request was sent to the guide. We will notify you about updates.',
            icon: 'fa-calendar-check',
            type: 'booking',
            actionUrl: route('dashboard.my-bookings'),
        ));

        $successMessage = 'Booking request sent to guide! You\'ll be notified soon.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'booking_request_id' => $bookingRequest->id,
                'message' => $successMessage,
            ], 201);
        }

        return back()->with('status', $successMessage);
    }
}
