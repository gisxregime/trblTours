<?php

namespace App\Livewire\Guide;

use App\Models\BookingRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class GuideBookingRequests extends Component
{
    public string $statusFilter = 'pending';

    public ?int $selectedRequestId = null;

    /**
     * @var array<int, string>
     */
    public array $declineReasons = [];

    public function selectRequest(int $requestId): void
    {
        $this->selectedRequestId = $requestId;
    }

    public function acceptRequest(int $requestId): void
    {
        if (! Schema::hasTable('booking_requests')) {
            return;
        }

        BookingRequest::query()
            ->whereKey($requestId)
            ->where('guide_id', (int) Auth::id())
            ->where('status', 'pending')
            ->update(['status' => 'accepted']);
    }

    public function declineRequest(int $requestId): void
    {
        if (! Schema::hasTable('booking_requests')) {
            return;
        }

        $reason = trim((string) ($this->declineReasons[$requestId] ?? ''));

        BookingRequest::query()
            ->whereKey($requestId)
            ->where('guide_id', (int) Auth::id())
            ->where('status', 'pending')
            ->update([
                'status' => 'declined',
                'decline_reason' => $reason !== '' ? $reason : null,
            ]);

        unset($this->declineReasons[$requestId]);
    }

    public function render()
    {
        $requests = collect();

        if (Schema::hasTable('booking_requests')) {
            $requests = BookingRequest::query()
                ->with(['tour:id,title,region', 'tourist:id,full_name,name'])
                ->where('guide_id', (int) Auth::id())
                ->when($this->statusFilter !== 'all', fn ($query) => $query->where('status', $this->statusFilter))
                ->latest()
                ->limit(30)
                ->get();
        }

        $selectedRequest = $this->selectedRequestId !== null
            ? $requests->firstWhere('id', $this->selectedRequestId)
            : $requests->first();

        return view('livewire.guide.guide-booking-requests', [
            'requests' => $requests,
            'selectedRequest' => $selectedRequest,
        ]);
    }
}
