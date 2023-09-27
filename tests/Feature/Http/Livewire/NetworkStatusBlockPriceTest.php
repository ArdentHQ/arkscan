<?php

declare(strict_types=1);

use App\Facades\Settings;
use App\Http\Livewire\NetworkStatusBlockPrice;
use App\Services\Cache\NetworkStatusBlockCache;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;

it('should render with price', function () {
    Config::set('arkscan.network', 'production');

    (new NetworkStatusBlockCache())->setPrice('ARK', 'USD', 1.606);
    (new NetworkStatusBlockCache())->setHistoricalHourly('ARK', 'USD', collect());

    Livewire::test(NetworkStatusBlockPrice::class)->assertSee('1.61');
});

it('should render with a different currency', function () {
    Config::set('arkscan.network', 'production');

    Settings::shouldReceive('currency')
        ->andReturn('BTC');

    (new NetworkStatusBlockCache())->setPrice('ARK', 'BTC', 0.0000006);
    (new NetworkStatusBlockCache())->setHistoricalHourly('ARK', 'BTC', collect());

    Livewire::test(NetworkStatusBlockPrice::class)->assertSee('0.0000006');
});

it('should render the price change', function () {
    Config::set('arkscan.networks.development.canBeExchanged', true);

    (new NetworkStatusBlockCache())->setPriceChange('DARK', 'USD', 0.137);
    (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 1);
    (new NetworkStatusBlockCache())->setHistoricalHourly('DARK', 'USD', collect());

    Livewire::test(NetworkStatusBlockPrice::class)->assertSee('13.70%');
});

it('handle price change when price is zero', function () {
    Config::set('arkscan.networks.development.canBeExchanged', true);

    (new NetworkStatusBlockCache())->setPriceChange('DARK', 'USD', 0);
    (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 1);
    (new NetworkStatusBlockCache())->setHistoricalHourly('DARK', 'USD', collect());

    Livewire::test(NetworkStatusBlockPrice::class)->assertSee('0.00%');
});
