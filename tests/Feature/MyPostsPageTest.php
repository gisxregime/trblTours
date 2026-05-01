<?php

use App\Models\Comment;
use App\Models\Conversation;
use App\Models\TouristRequest;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\get;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

it('renders the my posts dashboard with summary and request cards', function () {
    $tourist = User::factory()->create([
        'role' => 'tourist',
        'status' => 'active',
        'email_verified_at' => now(),
    ]);

    actingAs($tourist);

    get(route('dashboard.my-posts'))
        ->assertSuccessful()
        ->assertSee('My Tour Requests')
        ->assertSee('Total Requests')
        ->assertSee('Open Requests')
        ->assertSee('Selected Guides')
        ->assertSee('Completed Trips')
        ->assertSee('Create Tour Request');
});

it('supports public negotiation, guide selection, and private chat bootstrapping', function () {
    $tourist = User::factory()->create([
        'role' => 'tourist',
        'status' => 'active',
        'email_verified_at' => now(),
    ]);

    $guide = User::factory()->guide()->create([
        'status' => 'active',
    ]);

    $requestPost = TouristRequest::query()->create([
        'tourist_id' => $tourist->id,
        'title' => 'Island Hopping in Samal',
        'location' => 'Samal Island',
        'region' => 'Davao Region',
        'duration' => '1 Day',
        'budget_min' => 5000,
        'budget_max' => 5000,
        'adults' => 2,
        'children' => 0,
        'passenger_count' => 2,
        'preferred_date' => now()->addDays(15)->toDateString(),
        'interests' => 'Beach, Food',
        'description' => 'Need a full package with lunch and flexible pickup.',
        'status' => 'open',
    ]);

    $offerComment = Comment::query()->create([
        'tourist_request_id' => $requestPost->id,
        'user_id' => $guide->id,
        'body' => 'I can do this with lunch included.',
        'offer_price' => 4500,
    ]);

    actingAs($tourist);

    $commentResponse = postJson(route('dashboard.my-posts.comments.store', $requestPost), [
        'body' => 'Thanks, can you include snorkeling gear?',
    ]);

    $commentResponse
        ->assertCreated()
        ->assertJsonPath('post_status', 'open');

    getJson(route('dashboard.my-posts.comments', $requestPost))
        ->assertSuccessful()
        ->assertJsonStructure(['data']);

    $selectResponse = postJson(route('dashboard.my-posts.comments.select-guide', [
        'touristRequest' => $requestPost,
        'comment' => $offerComment,
    ]));

    $selectResponse
        ->assertSuccessful()
        ->assertJsonPath('post.status', 'guide_selected')
        ->assertJsonPath('conversation.participant.id', $guide->id);

    $redirectUrl = (string) $selectResponse->json('redirect_url');
    $conversationId = (int) $selectResponse->json('conversation.id');

    expect(str_starts_with($redirectUrl, route('dashboard.messages').'?conversation='))->toBeTrue();
    expect($redirectUrl)->toContain((string) $conversationId);

    $requestPost->refresh();

    expect($requestPost->status)->toBe('guide_selected');
    expect((int) $requestPost->selected_guide_id)->toBe((int) $guide->id);

    $conversation = Conversation::query()
        ->where('tourist_request_id', $requestPost->id)
        ->where('tourist_id', $tourist->id)
        ->where('guide_id', $guide->id)
        ->first();

    expect($conversation)->not->toBeNull();

    postJson(route('dashboard.messages.store', $conversation), [
        'body' => 'Hello guide, I selected your offer.',
    ])->assertCreated();

    postJson(route('dashboard.my-posts.complete', $requestPost))
        ->assertSuccessful()
        ->assertJsonPath('data.status', 'completed');

    assertDatabaseHas('tourist_requests', [
        'id' => $requestPost->id,
        'status' => 'completed',
    ]);
});

it('marks request as cancelled when delete action is triggered', function () {
    $tourist = User::factory()->create([
        'role' => 'tourist',
        'status' => 'active',
        'email_verified_at' => now(),
    ]);

    $requestPost = TouristRequest::query()->create([
        'tourist_id' => $tourist->id,
        'title' => 'Tagum Food Crawl Adventure',
        'location' => 'Tagum City',
        'region' => 'Davao Region',
        'duration' => 'Half-day',
        'budget_min' => 2000,
        'budget_max' => 2000,
        'adults' => 2,
        'children' => 0,
        'passenger_count' => 2,
        'preferred_date' => now()->addDays(7)->toDateString(),
        'interests' => 'Food',
        'description' => 'Need a weekend-only local food guide.',
        'status' => 'open',
    ]);

    actingAs($tourist);

    deleteJson(route('dashboard.my-posts.delete', $requestPost))
        ->assertSuccessful()
        ->assertJsonPath('data.status', 'cancelled');

    assertDatabaseHas('tourist_requests', [
        'id' => $requestPost->id,
        'status' => 'cancelled',
    ]);
});
