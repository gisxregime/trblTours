<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TouristNotifications extends Component
{
    public function getUnreadCountProperty(): int
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            return 0;
        }

        return $user->unreadNotifications()->count();
    }

    /**
     * @return Collection<int, DatabaseNotification>
     */
    public function getNotificationsProperty(): Collection
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            return new Collection;
        }

        return $user->notifications()->latest()->limit(8)->get();
    }

    public function markAsRead(string $notificationId): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $notification = $user->unreadNotifications()->whereKey($notificationId)->first();

        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead(): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $user->unreadNotifications->markAsRead();
    }

    public function openNotification(string $notificationId): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $notification = $user->notifications()->whereKey($notificationId)->first();

        if (! $notification) {
            return;
        }

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        $actionUrl = $notification->data['action_url'] ?? null;

        if (is_string($actionUrl) && trim($actionUrl) !== '') {
            $this->redirect($actionUrl, navigate: false);

            return;
        }

        $fallbackRoute = in_array((string) $user->role, ['guide', 'tour_guide'], true)
            ? route('dashboard.guide.requests')
            : route('dashboard.my-bookings');

        $this->redirect($fallbackRoute, navigate: false);
    }

    public function render(): View
    {
        return view('livewire.tourist-notifications');
    }
}
