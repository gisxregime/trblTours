<?php

namespace App\Events;

use App\Models\TouristRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TouristRequestPosted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var array<string, mixed>
     */
    public array $requestPost;

    /**
     * Create a new event instance.
     */
    public function __construct(TouristRequest $touristRequest)
    {
        $touristRequest->loadMissing('tourist:id,name,full_name,profile_photo_path');

        $tourist = $touristRequest->tourist;
        $preferredDate = $touristRequest->preferred_date;
        $preferredDateValue = null;

        if ($preferredDate instanceof \DateTimeInterface) {
            $preferredDateValue = $preferredDate->format('Y-m-d');
        } elseif (is_string($preferredDate) && trim($preferredDate) !== '') {
            $preferredDateValue = $preferredDate;
        }

        $this->requestPost = [
            'id' => (int) $touristRequest->id,
            'title' => (string) ($touristRequest->title ?: 'Custom Tour Request'),
            'location' => (string) ($touristRequest->location ?: 'Philippines'),
            'region' => (string) ($touristRequest->region ?: ''),
            'duration' => (string) ($touristRequest->duration ?: ''),
            'budget' => (float) ($touristRequest->budget_max ?? $touristRequest->budget_min ?? 0),
            'adults' => (int) ($touristRequest->adults ?? 0),
            'children' => (int) ($touristRequest->children ?? 0),
            'preferred_date' => $preferredDateValue,
            'interests' => (string) ($touristRequest->interests ?? ''),
            'description' => (string) ($touristRequest->description ?: $touristRequest->message ?: ''),
            'posted_at' => $touristRequest->created_at?->toIso8601String(),
            'tourist_name' => (string) ($tourist?->full_name ?: $tourist?->name ?: 'Tourist'),
            'tourist_avatar' => (string) ($tourist?->profile_photo_path ?: ''),
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('guides.request-feed'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'tourist.request.posted';
    }
}
