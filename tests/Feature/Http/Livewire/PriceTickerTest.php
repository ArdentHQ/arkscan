<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Facades\Settings;
use App\Http\Livewire\PriceTicker;
use App\Services\Cache\NetworkStatusBlockCache;
use Livewire\Livewire;

it('should render with the source currency, target currency and exchange rate', function () {
    (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 0.2907);

    Livewire::test(PriceTicker::class)
        ->assertSee(Network::currency())
        ->assertSee(Settings::currency())
        ->assertSee(0.29);
});

it('should render an empty state if not price set', function () {
    Livewire::test(PriceTicker::class)
        ->assertSee(Network::currency())
        ->assertSee(Settings::currency())
        ->assertSeeHtml('<span class="text-theme-secondary-500 dark:text-theme-secondary-600">-</span>');
});

it('should update the price if the currency changes', function () {
    (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 0.2907);
    (new NetworkStatusBlockCache())->setPrice('DARK', 'MXN', 0.22907);

    $component = Livewire::test(PriceTicker::class);

    $settings = Settings::all();
    $settings['currency'] = 'MXN';

    Settings::shouldReceive('all')->andReturn($settings);
    Settings::shouldReceive('currency')->andReturn('MXN');

    $component
        ->assertSee('DARK')
        ->assertSee('USD')
        ->assertSee(0.29)
        ->emit('currencyChanged')
        ->assertSee('DARK')
        ->assertSee('MXN')
        ->assertSee(0.23);
});
