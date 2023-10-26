<?php

use App\Facades\Settings;
use App\Http\Livewire\Delegates\FavoriteDelegateHandler;
use Illuminate\Support\Facades\Cookie;
use Livewire\Livewire;

it('should set favorite delegate', function () {
    Cookie::shouldReceive('queue')
        ->with('settings', json_encode([
            'currency'          => 'USD',
            'priceChart'        => true,
            'feeChart'          => true,
            'darkTheme'         => null,
            'favoriteDelegates' => [
                'test-public-key',
            ],
        ]), 60 * 24 * 365 * 5)
        ->once();

    Livewire::test(FavoriteDelegateHandler::class)
        ->call('setDelegate', 'test-public-key');
});

it('should remove favorite delegate', function () {
    Cookie::shouldReceive('queue')
        ->with('settings', json_encode([
            'favoriteDelegates' => [],
        ]), 60 * 24 * 365 * 5)
        ->once();

    Settings::shouldReceive('favoriteDelegates')
        ->andReturn(collect(['existing-public-key']))
        ->once()
        ->shouldReceive('all')
        ->andReturn([
            'favoriteDelegates' => [
                'existing-public-key',
            ],
        ])
        ->once();

    Livewire::test(FavoriteDelegateHandler::class)
        ->call('removeDelegate', 'existing-public-key');
});
