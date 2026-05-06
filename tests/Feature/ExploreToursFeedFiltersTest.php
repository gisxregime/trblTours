<?php

use App\Livewire\ExploreToursFeed;
use Livewire\Livewire;

it('applies dashboard filters without redirect', function () {
    Livewire::test(ExploreToursFeed::class, ['context' => 'dashboard'])
        ->set('locationPath', 'MiMaRoPa')
        ->set('sortBy', 'price_low_high')
        ->call('filter')
        ->assertNoRedirect()
        ->assertSet('locationPath', 'MiMaRoPa')
        ->assertSet('sortBy', 'price_low_high');
});

it('updates location filter reactively in dashboard context', function () {
    Livewire::test(ExploreToursFeed::class, ['context' => 'dashboard'])
        ->set('locationPath', 'Palawan')
        ->assertSet('locationPath', 'Palawan')
        ->assertNoRedirect();
});

it('normalizes invalid sort in dashboard context', function () {
    Livewire::test(ExploreToursFeed::class, ['context' => 'dashboard'])
        ->set('locationPath', '   ')
        ->set('sortBy', 'not-a-valid-sort')
        ->call('filter')
        ->assertSet('sortBy', 'latest')
        ->assertNoRedirect();
});
