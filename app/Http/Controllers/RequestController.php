<?php

namespace App\Http\Controllers;

use App\Events\TouristRequestPosted;
use App\Models\ServiceLocation;
use App\Models\TouristRequest;
use App\Models\User;
use App\Notifications\TourActivityNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class RequestController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = Auth::user();
        abort_unless($user?->role === 'tourist', 403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string',
            'preferred_date' => 'required|date|after_or_equal:today',
            'passenger_count' => 'required|integer|min:1|max:20',
            'budget_min' => 'required|numeric|min:500',
            'budget_max' => 'required|numeric|gte:budget_min|max:50000',
            'description' => 'required|string|max:1000',
            'duration' => 'nullable|string|max:120',
            'region' => 'nullable|string|max:255',
            'adults' => 'nullable|integer|min:0|max:20',
            'children' => 'nullable|integer|min:0|max:20',
            'interests' => 'nullable|string|max:255',
        ]);

        $columns = array_flip(Schema::getColumnListing('tourist_requests'));

        $serviceLocationId = null;
        if (
            isset($columns['service_location_id'])
            && Schema::hasTable('service_locations')
            && Schema::hasColumn('service_locations', 'name')
        ) {
            $serviceLocation = ServiceLocation::firstOrCreate(['name' => $validated['location']]);
            $serviceLocationId = $serviceLocation->id;
        }

        $metadataLines = [];

        if (! empty($validated['duration'])) {
            $metadataLines[] = 'Duration: '.$validated['duration'];
        }

        if (! empty($validated['region'])) {
            $metadataLines[] = 'Region: '.$validated['region'];
        }

        if (isset($validated['adults']) || isset($validated['children'])) {
            $adults = (int) ($validated['adults'] ?? 0);
            $children = (int) ($validated['children'] ?? 0);
            $metadataLines[] = 'Travelers: Adults '.$adults.', Children '.$children;
        }

        if (! empty($validated['interests'])) {
            $metadataLines[] = 'Interests: '.$validated['interests'];
        }

        $finalDescription = $validated['description'];
        if ($metadataLines !== []) {
            $finalDescription .= "\n\n".implode("\n", $metadataLines);
        }

        $requestPayload = [];

        if (isset($columns['tourist_id'])) {
            $requestPayload['tourist_id'] = $user->id;
        }

        if (isset($columns['service_location_id']) && $serviceLocationId !== null) {
            $requestPayload['service_location_id'] = $serviceLocationId;
        }

        if (isset($columns['title'])) {
            $requestPayload['title'] = $validated['title'];
        }

        if (isset($columns['location'])) {
            $requestPayload['location'] = $validated['location'];
        }

        if (isset($columns['preferred_date'])) {
            $requestPayload['preferred_date'] = $validated['preferred_date'];
        }

        if (isset($columns['passenger_count'])) {
            $requestPayload['passenger_count'] = $validated['passenger_count'];
        }

        if (isset($columns['budget_min'])) {
            $requestPayload['budget_min'] = $validated['budget_min'];
        }

        if (isset($columns['budget_max'])) {
            $requestPayload['budget_max'] = $validated['budget_max'];
        }

        if (isset($columns['duration'])) {
            $requestPayload['duration'] = $validated['duration'] ?? null;
        }

        if (isset($columns['region'])) {
            $requestPayload['region'] = $validated['region'] ?? null;
        }

        if (isset($columns['adults'])) {
            $requestPayload['adults'] = $validated['adults'] ?? null;
        }

        if (isset($columns['children'])) {
            $requestPayload['children'] = $validated['children'] ?? null;
        }

        if (isset($columns['interests'])) {
            $requestPayload['interests'] = $validated['interests'] ?? null;
        }

        if (isset($columns['description'])) {
            $requestPayload['description'] = $validated['description'];
        }

        if (isset($columns['status'])) {
            $requestPayload['status'] = 'open';
        }

        if (isset($columns['message'])) {
            $requestPayload['message'] = $finalDescription;
        }

        $touristRequest = TouristRequest::query()->create($requestPayload);

        event(new TouristRequestPosted($touristRequest));

        $user->notify(new TourActivityNotification(
            title: 'Request posted',
            message: 'Your tour request was submitted successfully and is now visible to guides.',
            icon: 'fa-calendar-check',
            type: 'request',
            actionUrl: route('dashboard.my-posts'),
        ));

        return response()->json([
            'success' => true,
            'message' => 'Request post created successfully! It will appear in the feed shortly.',
            'post' => [
                'id' => (int) $touristRequest->id,
                'title' => $touristRequest->title ?: 'Untitled Request',
                'destination' => $touristRequest->location ?: 'Philippines',
                'region' => $touristRequest->region,
                'duration' => $touristRequest->duration,
                'budget' => (float) ($touristRequest->budget_max ?? $touristRequest->budget_min ?? 0),
                'adults' => (int) ($touristRequest->adults ?? 0),
                'children' => (int) ($touristRequest->children ?? 0),
                'preferred_date' => $touristRequest->preferred_date,
                'interests' => collect(explode(',', (string) $touristRequest->interests))
                    ->map(fn (string $interest): string => trim($interest))
                    ->filter(fn (string $interest): bool => $interest !== '')
                    ->values(),
                'description' => $touristRequest->description ?: (string) $touristRequest->message,
                'posted_at' => $touristRequest->created_at?->toIso8601String(),
                'status' => 'open',
                'status_label' => 'Open',
                'selected_guide_id' => null,
                'selected_guide_name' => null,
                'selected_comment_id' => null,
                'comments_count' => 0,
                'recent_comments' => [],
            ],
        ]);
    }
}
