<?php

use App\Models\Tour;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\postJson;

test('tourist dashboard dropdown likes link points to likes page', function () {
    $tourist = User::factory()->create();

    actingAs($tourist);

    $response = get(route('dashboard.tourist'));

    $response
        ->assertOk()
        ->assertSee(route('dashboard.likes'), false);
});

test('tourist can like a tour and redirect to likes page', function () {
    $tourist = User::factory()->create();
    $tour = Tour::factory()->create();

    actingAs($tourist);

    $response = post(route('dashboard.likes.toggle', $tour), [
        'redirect' => 'likes',
    ]);

    $response->assertRedirect(route('dashboard.likes'));

    assertDatabaseHas('tour_likes', [
        'user_id' => $tourist->id,
        'tour_id' => $tour->id,
    ]);
});

test('tourist can toggle like through json endpoint', function () {
    $tourist = User::factory()->create();
    $tour = Tour::factory()->create();

    actingAs($tourist);

    $likeResponse = postJson(route('dashboard.likes.toggle', $tour));

    $likeResponse
        ->assertOk()
        ->assertJsonPath('tour_id', $tour->id)
        ->assertJsonPath('liked', true);

    assertDatabaseHas('tour_likes', [
        'user_id' => $tourist->id,
        'tour_id' => $tour->id,
    ]);

    $unlikeResponse = postJson(route('dashboard.likes.toggle', $tour));

    $unlikeResponse
        ->assertOk()
        ->assertJsonPath('tour_id', $tour->id)
        ->assertJsonPath('liked', false);

    assertDatabaseMissing('tour_likes', [
        'user_id' => $tourist->id,
        'tour_id' => $tour->id,
    ]);
});

test('likes page displays liked tours', function () {
    $tourist = User::factory()->create();
    $tour = Tour::factory()->create([
        'title' => 'El Nido Hidden Lagoons',
    ]);

    $tourist->likedTours()->syncWithoutDetaching([$tour->id]);

    actingAs($tourist);

    $response = get(route('dashboard.likes'));

    $response
        ->assertOk()
        ->assertSee('Liked Tours')
        ->assertSee('El Nido Hidden Lagoons');
});

test('clicking heart again removes liked tour', function () {
    $tourist = User::factory()->create();
    $tour = Tour::factory()->create();

    $tourist->likedTours()->syncWithoutDetaching([$tour->id]);

    actingAs($tourist);

    $response = post(route('dashboard.likes.toggle', $tour), [
        'redirect' => 'likes',
    ]);

    $response->assertRedirect(route('dashboard.likes'));

    assertDatabaseMissing('tour_likes', [
        'user_id' => $tourist->id,
        'tour_id' => $tour->id,
    ]);
});

test('liked tour details page shows filled heart icon', function () {
    $tourist = User::factory()->create();
    $tour = Tour::factory()->create();

    $tourist->likedTours()->syncWithoutDetaching([$tour->id]);

    actingAs($tourist);

    $response = get(route('tours.show', [
        'tour' => $tour,
        'from' => 'explore',
    ]));

    $response
        ->assertOk()
        ->assertSee('fa-solid fa-heart', false);
});

test('guide cannot toggle tour likes', function () {
    $guide = User::factory()->guide()->create();
    $tour = Tour::factory()->create();

    actingAs($guide);

    $response = post(route('dashboard.likes.toggle', $tour));

    $response->assertForbidden();
});
