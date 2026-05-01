<?php

use App\Livewire\TouristNotifications;
use App\Models\User;
use App\Notifications\TourActivityNotification;
use Livewire\Livewire;

it('marks a single notification as read', function () {
    $tourist = User::factory()->create([
        'role' => 'tourist',
        'status' => 'active',
    ]);

    $tourist->notify(new TourActivityNotification(
        title: 'Booking sent',
        message: 'Your booking request is pending guide approval.',
        icon: 'fa-calendar-check',
        type: 'booking',
    ));

    $notificationId = $tourist->fresh()->unreadNotifications()->first()->id;

    Livewire::actingAs($tourist)
        ->test(TouristNotifications::class)
        ->assertSee('Booking sent')
        ->call('markAsRead', $notificationId);

    expect($tourist->fresh()->unreadNotifications()->count())->toBe(0);
});

it('marks all notifications as read', function () {
    $tourist = User::factory()->create([
        'role' => 'tourist',
        'status' => 'active',
    ]);

    $tourist->notify(new TourActivityNotification(
        title: 'Request posted',
        message: 'Your request is now available to guides.',
        icon: 'fa-calendar-check',
        type: 'request',
    ));

    $tourist->notify(new TourActivityNotification(
        title: 'Message received',
        message: 'A guide replied to your inquiry.',
        icon: 'fa-envelope',
        type: 'message',
    ));

    expect($tourist->fresh()->unreadNotifications()->count())->toBe(2);

    Livewire::actingAs($tourist)
        ->test(TouristNotifications::class)
        ->call('markAllAsRead');

    expect($tourist->fresh()->unreadNotifications()->count())->toBe(0);
});

it('opens a notification and redirects to its action url', function () {
    $guide = User::factory()->create([
        'role' => 'guide',
        'status' => 'active',
    ]);

    $guide->notify(new TourActivityNotification(
        title: 'New booking request',
        message: 'A tourist submitted a booking request for your package.',
        icon: 'fa-calendar-check',
        type: 'booking',
        actionUrl: route('dashboard.guide.requests'),
    ));

    $notificationId = $guide->fresh()->unreadNotifications()->first()->id;

    Livewire::actingAs($guide)
        ->test(TouristNotifications::class)
        ->call('openNotification', $notificationId)
        ->assertRedirect(route('dashboard.guide.requests'));

    expect($guide->fresh()->unreadNotifications()->count())->toBe(0);
});
