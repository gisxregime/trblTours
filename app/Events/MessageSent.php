<?php

namespace App\Events;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Schema;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Conversation $conversation, public Message $message) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, PrivateChannel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('messaging.user.'.$this->conversation->tourist_id),
            new PrivateChannel('messaging.user.'.$this->conversation->guide_id),
            new PrivateChannel('messaging.conversation.'.$this->conversation->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $this->message->loadMissing('sender:id,name,full_name,profile_photo_path');

        return [
            'conversation_id' => $this->conversation->id,
            'message' => [
                'id' => $this->message->id,
                'body' => $this->messageBody(),
                'sender_id' => $this->message->sender_id,
                'sender_name' => $this->message->sender?->full_name ?: $this->message->sender?->name ?: 'User',
                'created_at' => $this->message->created_at?->toIso8601String(),
            ],
            'last_message' => $this->messageBody(),
            'last_message_at' => $this->message->created_at?->toIso8601String(),
            'unread_counts' => [
                (string) $this->conversation->tourist_id => $this->unreadCountForParticipant((int) $this->conversation->tourist_id),
                (string) $this->conversation->guide_id => $this->unreadCountForParticipant((int) $this->conversation->guide_id),
            ],
        ];
    }

    private function unreadCountForParticipant(int $participantId): int
    {
        $query = Message::query()
            ->where('conversation_id', $this->conversation->id)
            ->where('sender_id', '!=', $participantId);

        if (Schema::hasColumn('messages', 'read_at')) {
            $query->whereNull('read_at');
        } else {
            $query->where('is_read', false);
        }

        return (int) $query->count();
    }

    private function messageBody(): string
    {
        $body = $this->message->getAttribute('body');

        if (is_string($body) && $body !== '') {
            return $body;
        }

        return (string) $this->message->getAttribute('message');
    }
}
