<?php

namespace App\Livewire\Guide;

use App\Models\Booking;
use App\Models\BookingRequest;
use App\Models\GuideEarning;
use App\Models\Tour;
use App\Models\TourReview;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class GuideDashboard extends Component
{
    /**
     * @var array<int, string>
     */
    public array $declineReasons = [];

    public function openSettings(): void
    {
        $user = Auth::user();
        abort_if($user === null, 401);
        abort_unless(in_array($user->role, ['guide', 'tour_guide'], true), 403);

        $this->redirectRoute('dashboard.guide.settings', navigate: true);
    }

    public function acceptRequest(int $requestId): void
    {
        $guideId = (int) Auth::id();

        if (! Schema::hasTable('booking_requests')) {
            return;
        }

        BookingRequest::query()
            ->whereKey($requestId)
            ->where('guide_id', $guideId)
            ->where('status', 'pending')
            ->update(['status' => 'accepted']);
    }

    public function declineRequest(int $requestId): void
    {
        $guideId = (int) Auth::id();

        if (! Schema::hasTable('booking_requests')) {
            return;
        }

        $reason = trim((string) ($this->declineReasons[$requestId] ?? ''));

        BookingRequest::query()
            ->whereKey($requestId)
            ->where('guide_id', $guideId)
            ->where('status', 'pending')
            ->update([
                'status' => 'declined',
                'decline_reason' => $reason !== '' ? $reason : null,
            ]);

        unset($this->declineReasons[$requestId]);
    }

    public function deleteTour(int $tourId): void
    {
        $user = Auth::user();
        abort_if($user === null, 401);

        if (! Schema::hasTable('tours')) {
            return;
        }

        $tour = Tour::query()->find($tourId);
        if ($tour === null || ($tour->guide_id !== $user->id && $tour->created_by !== $user->id)) {
            return;
        }

        $tour->delete();
    }

    public function render()
    {
        $user = Auth::user();
        abort_if($user === null, 401);
        abort_unless(in_array($user->role, ['guide', 'tour_guide'], true), 403);

        $guideId = (int) $user->id;
        $profile = $this->getGuideProfile($guideId);

        $firstName = $this->firstName((string) ($user->full_name ?: $user->name));
        $criteria = $this->completionCriteria($profile, $user->bio, $user->specialty);
        $completionPercentage = (int) collect($criteria)
            ->filter(fn (array $criterion): bool => (bool) $criterion['complete'])
            ->sum('weight');

        $progressTheme = match (true) {
            $completionPercentage <= 40 => ['bar' => 'bg-red-500', 'badge' => 'bg-red-100 text-red-700', 'label' => 'Needs Attention'],
            $completionPercentage <= 70 => ['bar' => 'bg-yellow-500', 'badge' => 'bg-yellow-100 text-yellow-700', 'label' => 'In Progress'],
            default => ['bar' => 'bg-green-500', 'badge' => 'bg-green-100 text-green-700', 'label' => 'Almost Ready'],
        };

        $pendingRequests = Schema::hasTable('booking_requests')
            ? BookingRequest::query()
                ->with('tour:id,name,title,region,city')
                ->where('guide_id', $guideId)
                ->where('status', 'pending')
                ->latest()
                ->limit(5)
                ->get()
            : collect();

        $todaySchedule = collect();
        if (Schema::hasTable('bookings')) {
            $todayScheduleQuery = Booking::query()
                ->with('tour:id,name,title')
                ->whereDate('booking_date', now()->toDateString())
                ->whereIn('status', ['confirmed', 'in_progress']);

            if (Schema::hasColumn('bookings', 'booking_request_id') && Schema::hasTable('booking_requests')) {
                $todayScheduleQuery->whereIn(
                    'booking_request_id',
                    BookingRequest::query()->select('id')->where('guide_id', $guideId)
                );
            } else {
                $todayScheduleQuery->whereRaw('1 = 0');
            }

            $todaySchedule = $todayScheduleQuery
                ->orderBy('booking_date')
                ->limit(6)
                ->get();
        }

        $activeTours = 0;
        if (Schema::hasTable('tours')) {
            $activeToursQuery = Tour::query();
            $hasGuideId = Schema::hasColumn('tours', 'guide_id');
            $hasCreatedBy = Schema::hasColumn('tours', 'created_by');

            if ($hasGuideId && $hasCreatedBy) {
                $activeToursQuery->where(function ($query) use ($guideId): void {
                    $query->where('guide_id', $guideId)
                        ->orWhere(function ($fallback) use ($guideId): void {
                            $fallback->whereNull('guide_id')->where('created_by', $guideId);
                        });
                });
            } elseif ($hasGuideId) {
                $activeToursQuery->where('guide_id', $guideId);
            } elseif ($hasCreatedBy) {
                $activeToursQuery->where('created_by', $guideId);
            } else {
                $activeToursQuery->whereRaw('1 = 0');
            }

            if (Schema::hasColumn('tours', 'status')) {
                $activeToursQuery->where('status', 'active');
            }

            $activeTours = $activeToursQuery->count();
        }

        $featuredTours = 0;
        if (Schema::hasTable('bookings') && Schema::hasColumn('bookings', 'tour_id')) {
            $featuredToursQuery = Booking::query()
                ->select('bookings.tour_id')
                ->join('tours', 'tours.id', '=', 'bookings.tour_id')
                ->whereNotNull('bookings.tour_id');

            if (Schema::hasColumn('tours', 'guide_id')) {
                $featuredToursQuery->where('tours.guide_id', $guideId);
            } elseif (Schema::hasColumn('tours', 'created_by')) {
                $featuredToursQuery->where('tours.created_by', $guideId);
            } else {
                $featuredToursQuery->whereRaw('1 = 0');
            }

            $featuredTours = (int) $featuredToursQuery->distinct()->count('bookings.tour_id');
        }

        $accepted = Schema::hasTable('booking_requests')
            ? BookingRequest::query()->where('guide_id', $guideId)->where('status', 'accepted')->count()
            : 0;
        $declined = Schema::hasTable('booking_requests')
            ? BookingRequest::query()->where('guide_id', $guideId)->where('status', 'declined')->count()
            : 0;
        $responseRate = ($accepted + $declined) > 0
            ? (int) round(($accepted / ($accepted + $declined)) * 100)
            : 100;

        $monthlyEarnings = 0.0;
        $totalEarnings = 0.0;
        if (Schema::hasTable('guide_earnings')) {
            $monthlyEarnings = (float) GuideEarning::query()
                ->where('guide_id', $guideId)
                ->whereBetween('earning_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
                ->sum('net_amount');
            $totalEarnings = (float) GuideEarning::query()
                ->where('guide_id', $guideId)
                ->sum('net_amount');
        } elseif (Schema::hasTable('bookings')) {
            $amountColumn = null;
            if (Schema::hasColumn('bookings', 'net_amount')) {
                $amountColumn = 'net_amount';
            } elseif (Schema::hasColumn('bookings', 'total_amount')) {
                $amountColumn = 'total_amount';
            }

            if ($amountColumn === null) {
                $monthlyEarnings = 0.0;
                $totalEarnings = 0.0;
            } else {
                $monthlyEarningsQuery = Booking::query()
                    ->where('status', 'completed')
                    ->whereBetween('booking_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()]);

                if (Schema::hasColumn('bookings', 'booking_request_id') && Schema::hasTable('booking_requests')) {
                    $monthlyEarningsQuery->whereIn(
                        'booking_request_id',
                        BookingRequest::query()->select('id')->where('guide_id', $guideId)
                    );
                } else {
                    $monthlyEarningsQuery->whereRaw('1 = 0');
                }

                $monthlyEarnings = (float) $monthlyEarningsQuery->sum($amountColumn);

                $totalEarningsQuery = Booking::query()
                    ->where('status', 'completed');

                if (Schema::hasColumn('bookings', 'booking_request_id') && Schema::hasTable('booking_requests')) {
                    $totalEarningsQuery->whereIn(
                        'booking_request_id',
                        BookingRequest::query()->select('id')->where('guide_id', $guideId)
                    );
                } else {
                    $totalEarningsQuery->whereRaw('1 = 0');
                }

                $totalEarnings = (float) $totalEarningsQuery->sum($amountColumn);
            }
        }

        $averageRating = Schema::hasTable('tour_reviews')
            ? round((float) TourReview::query()->where('guide_id', $guideId)->avg('rating'), 1)
            : 0.0;

        $profileViews = 0; // Placeholder: can be updated with actual analytics

        $recentReviews = collect();
        if (Schema::hasTable('tour_reviews')) {
            $recentReviews = TourReview::query()
                ->where('guide_id', $guideId)
                ->with('tourist:id,full_name,name')
                ->latest()
                ->limit(5)
                ->get();
        }

        $guideTours = collect();
        if (Schema::hasTable('tours')) {
            $toursQuery = Tour::query();
            $hasGuideId = Schema::hasColumn('tours', 'guide_id');
            $hasCreatedBy = Schema::hasColumn('tours', 'created_by');

            if ($hasGuideId && $hasCreatedBy) {
                $toursQuery->where(function ($query) use ($guideId): void {
                    $query->where('guide_id', $guideId)
                        ->orWhere(function ($fallback) use ($guideId): void {
                            $fallback->whereNull('guide_id')->where('created_by', $guideId);
                        });
                });
            } elseif ($hasGuideId) {
                $toursQuery->where('guide_id', $guideId);
            } elseif ($hasCreatedBy) {
                $toursQuery->where('created_by', $guideId);
            } else {
                $toursQuery->whereRaw('1 = 0');
            }
            $guideTours = $toursQuery->latest()->get();
        }

        $unreadMessages = (Schema::hasTable('conversations') && Schema::hasTable('messages'))
            ? DB::table('messages')
                ->join('conversations', 'messages.conversation_id', '=', 'conversations.id')
                ->where('conversations.guide_id', $guideId)
                ->where('messages.sender_id', '!=', $guideId)
                ->where('messages.is_read', false)
                ->count()
            : 0;

        return view('livewire.guide.guide-dashboard', [
            'firstName' => $firstName,
            'criteria' => $criteria,
            'completionPercentage' => $completionPercentage,
            'progressTheme' => $progressTheme,
            'showCompletionReminder' => $completionPercentage < 100,
            'showVerificationNotice' => $this->hasPendingVerification($profile),
            'showRejectedNotice' => ($profile['approval_status'] ?? null) === 'rejected',
            'rejectionReason' => (string) ($profile['rejection_reason'] ?? ''),
            'activeTours' => $activeTours,
            'featuredTours' => $featuredTours,
            'pendingRequests' => $pendingRequests,
            'todaySchedule' => $todaySchedule,
            'responseRate' => $responseRate,
            'monthlyEarnings' => $monthlyEarnings,
            'totalEarnings' => $totalEarnings,
            'averageRating' => $averageRating,
            'profileViews' => $profileViews,
            'recentReviews' => $recentReviews,
            'guideTours' => $guideTours,
            'unreadMessages' => $unreadMessages,
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function getGuideProfile(int $userId): array
    {
        if (! Schema::hasTable('tour_guides_profile')) {
            return [];
        }

        return (array) (DB::table('tour_guides_profile')->where('user_id', $userId)->first() ?? []);
    }

    private function firstName(string $name): string
    {
        $trimmed = trim($name);
        if ($trimmed === '') {
            return 'Guide';
        }

        return explode(' ', $trimmed)[0];
    }

    /**
     * @param  array<string, mixed>  $profile
     * @return array<int, array{label: string, weight: int, complete: bool}>
     */
    private function completionCriteria(array $profile, ?string $userBio, ?string $userSpecialty): array
    {
        return [
            ['label' => 'Profile photo uploaded', 'weight' => 15, 'complete' => $this->isFilled($profile, ['profile_photo_path', 'profile_photo', 'avatar_path', 'selfie_path'])],
            ['label' => 'Bio completed (min. 100 characters)', 'weight' => 15, 'complete' => mb_strlen(trim((string) Arr::first([$profile['bio'] ?? null, $userBio], fn ($value) => is_string($value)))) >= 100],
            ['label' => 'Languages selected (min. 1)', 'weight' => 10, 'complete' => $this->hasAtLeastOneSelection(Arr::first([$profile['languages_spoken'] ?? null, $profile['languages'] ?? null]))],
            ['label' => 'Specialties selected (min. 1)', 'weight' => 10, 'complete' => $this->hasAtLeastOneSelection(Arr::first([$profile['specializations'] ?? null, $profile['specialties'] ?? null, $profile['specialty'] ?? null, $userSpecialty]))],
            ['label' => 'Government ID uploaded', 'weight' => 15, 'complete' => $this->isFilled($profile, ['government_id_path_front', 'government_id_path_back', 'id_front_path', 'id_back_path', 'government_id_path'])],
            ['label' => 'NBI Clearance uploaded', 'weight' => 15, 'complete' => $this->isFilled($profile, ['nbi_clearance_path', 'nbi_clearance_file_path'])],
            ['label' => 'Barangay Clearance uploaded', 'weight' => 10, 'complete' => $this->isFilled($profile, ['barangay_clearance_path', 'barangay_clearance_file_path'])],
            ['label' => 'Payout details completed', 'weight' => 10, 'complete' => $this->isFilled($profile, ['bank_account_name', 'bank_name', 'bank_account_number', 'gcash_number', 'payout_method', 'payout_account_name', 'payout_account_number', 'maya_number'])],
        ];
    }

    /**
     * @param  array<string, mixed>  $record
     * @param  array<int, string>  $keys
     */
    private function isFilled(array $record, array $keys): bool
    {
        foreach ($keys as $key) {
            if (! array_key_exists($key, $record)) {
                continue;
            }

            $value = $record[$key];

            if (is_string($value) && trim($value) !== '') {
                return true;
            }

            if (is_numeric($value) && (string) $value !== '') {
                return true;
            }
        }

        return false;
    }

    private function hasAtLeastOneSelection(mixed $value): bool
    {
        if ($value === null) {
            return false;
        }

        if (is_array($value)) {
            return count(array_filter($value, fn (mixed $item): bool => filled($item))) >= 1;
        }

        if (! is_string($value)) {
            return false;
        }

        $decoded = json_decode($value, true);
        if (is_array($decoded)) {
            return count(array_filter($decoded, fn (mixed $item): bool => filled($item))) >= 1;
        }

        return collect(explode(',', $value))
            ->map(fn (string $item): string => trim($item))
            ->filter(fn (string $item): bool => $item !== '')
            ->isNotEmpty();
    }

    /**
     * @param  array<string, mixed>  $profile
     */
    private function hasPendingVerification(array $profile): bool
    {
        if (($profile['approval_status'] ?? null) === 'approved') {
            return false;
        }

        $pendingPairs = [
            ['path' => 'government_id_path_front', 'verified' => 'id_verified'],
            ['path' => 'nbi_clearance_path', 'verified' => 'nbi_verified'],
            ['path' => 'barangay_clearance_path', 'verified' => 'barangay_verified'],
            ['path' => 'tourism_certificates_path', 'verified' => 'cert_verified'],
            ['path' => 'id_front_path', 'verified' => 'id_front_verified'],
            ['path' => 'nbi_clearance_file_path', 'verified' => 'nbi_clearance_validated'],
        ];

        foreach ($pendingPairs as $pair) {
            $pathValue = $profile[$pair['path']] ?? null;
            $verificationValue = $profile[$pair['verified']] ?? null;

            if (is_string($pathValue) && trim($pathValue) !== '' && ! (bool) $verificationValue) {
                return true;
            }
        }

        if (($profile['approval_status'] ?? null) === 'pending') {
            return $this->isFilled($profile, [
                'government_id_path_front',
                'nbi_clearance_path',
                'barangay_clearance_path',
                'tourism_certificates_path',
                'id_front_path',
                'nbi_clearance_file_path',
            ]);
        }

        return false;
    }
}
