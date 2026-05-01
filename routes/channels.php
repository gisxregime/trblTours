<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('messaging.user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('messaging.conversation.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::query()->find((int) $conversationId);

    if (! $conversation) {
        return false;
    }

    return in_array((int) $user->id, [(int) $conversation->tourist_id, (int) $conversation->guide_id], true);
});

Broadcast::channel('messaging.presence.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::query()->find((int) $conversationId);

    if (! $conversation) {
        return false;
    }

    if (! in_array((int) $user->id, [(int) $conversation->tourist_id, (int) $conversation->guide_id], true)) {
        return false;
    }

    return [
        'id' => (int) $user->id,
        'name' => $user->full_name ?: $user->name,
    ];
});

Broadcast::channel('guides.request-feed', function ($user) {
    return in_array((string) $user->role, ['guide', 'tour_guide'], true);
});
