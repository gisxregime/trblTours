<?php

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Event;

test('tourist dashboard dropdown messages icon points to messaging page', function () {
    $tourist = User::factory()->create();

    $response = $this
        ->actingAs($tourist)
        ->get(route('dashboard.tourist'));

    $response
        ->assertOk()
        ->assertSee(route('dashboard.messages'), false);
});

test('messages page displays existing conversation', function () {
    $tourist = User::factory()->create();
    $guide = User::factory()->guide()->create();

    $conversation = Conversation::query()->create([
        'tourist_id' => $tourist->id,
        'guide_id' => $guide->id,
        'last_message_at' => now(),
    ]);

    Message::query()->create([
        'conversation_id' => $conversation->id,
        'sender_id' => $guide->id,
        'body' => 'Welcome to chat',
        'message' => 'Welcome to chat',
    ]);

    $response = $this
        ->actingAs($tourist)
        ->get(route('dashboard.messages'));

    $response
        ->assertOk()
        ->assertSee('Messages')
        ->assertSee($guide->full_name ?: $guide->name)
        ->assertSee('Welcome to chat');
});

test('participant can send message and event is broadcast', function () {
    Event::fake([MessageSent::class]);

    $tourist = User::factory()->create();
    $guide = User::factory()->guide()->create();

    $conversation = Conversation::query()->create([
        'tourist_id' => $tourist->id,
        'guide_id' => $guide->id,
    ]);

    $response = $this
        ->actingAs($tourist)
        ->postJson(route('dashboard.messages.store', $conversation), [
            'body' => 'Hello guide!',
        ]);

    $response
        ->assertCreated()
        ->assertJsonPath('message.body', 'Hello guide!')
        ->assertJsonPath('message.sender_id', $tourist->id)
        ->assertJsonPath('conversation.id', $conversation->id);

    $this->assertDatabaseHas('messages', [
        'conversation_id' => $conversation->id,
        'sender_id' => $tourist->id,
        'body' => 'Hello guide!',
    ]);

    Event::assertDispatched(MessageSent::class);
});

test('non participant cannot send message in another conversation', function () {
    $tourist = User::factory()->create();
    $guide = User::factory()->guide()->create();
    $intruder = User::factory()->create();

    $conversation = Conversation::query()->create([
        'tourist_id' => $tourist->id,
        'guide_id' => $guide->id,
    ]);

    $response = $this
        ->actingAs($intruder)
        ->postJson(route('dashboard.messages.store', $conversation), [
            'body' => 'I should not send this',
        ]);

    $response->assertForbidden();
});
