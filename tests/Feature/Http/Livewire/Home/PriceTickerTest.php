<?php

declare(strict_types=1);

use App\Facades\Settings;
use App\Http\Livewire\Home\PriceTicker;
use App\Services\Cache\NetworkStatusBlockCache;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;

it('should render price', function () {
    (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 0.2907);

    Livewire::test(PriceTicker::class)
        ->assertSee('$0.29');
});

it('should render the component with fiat value', function () {
    Config::set('arkscan.networks.development.canBeExchanged', true);
    Config::set('arkscan.networks.development.currency', 'ARK');

    (new NetworkStatusBlockCache())->setPrice('ARK', 'USD', 1.4);

    Livewire::test(PriceTicker::class)
        ->assertSeeInOrder([
            'ARK Price',
            '$1.40',
            'USD',
            '$1.40',
            'USD',
        ]);
});

it('should render the component with non fiat value', function () {
    Settings::shouldReceive('all')->andReturn(Settings::all());
    Settings::shouldReceive('theme')->andReturn('light');
    Settings::shouldReceive('currency')->andReturn('BTC');

    Config::set('arkscan.networks.development.canBeExchanged', true);
    Config::set('arkscan.networks.development.currency', 'ARK');

    (new NetworkStatusBlockCache())->setPrice('ARK', 'BTC', 15);

    Livewire::test(PriceTicker::class)
        ->assertSeeInOrder([
            'ARK Price',
            '15 BTC',
            '15 BTC',
        ])
        ->assertSee('15 BTC');
});
