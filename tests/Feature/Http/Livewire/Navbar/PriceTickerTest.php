<?php

declare(strict_types=1);

use App\Facades\Settings;
use App\Http\Livewire\Navbar\PriceTicker;
use App\Services\Cache\NetworkStatusBlockCache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Livewire\Livewire;

it('should render with the source currency, target currency and exchange rate', function () {
    Config::set('arkscan.network', 'production');
    Config::set('arkscan.networks.production.canBeExchanged', true);

    (new NetworkStatusBlockCache())->setPrice('ARK', 'USD', 0.2907);

    Livewire::test(PriceTicker::class)
        ->assertDontSee('N/A')
        ->assertSee(0.29);
});

it('should render n/a if no price set', function () {
    Livewire::test(PriceTicker::class)
        ->assertSee(Settings::currency())
        ->assertSee('N/A');
});

it('should render n/a if development environment', function () {
    Config::set('arkscan.network', 'development');

    Livewire::test(PriceTicker::class)
        ->assertSee('N/A');
});

it('should update the price if the currency changes', function () {
    Config::set('arkscan.network', 'production');
    Config::set('arkscan.networks.production.canBeExchanged', true);

    (new NetworkStatusBlockCache())->setPrice('ARK', 'USD', 0.2907);
    (new NetworkStatusBlockCache())->setPrice('ARK', 'MXN', 0.22907);

    $component = Livewire::test(PriceTicker::class);

    $settings             = Settings::all();
    $settings['currency'] = 'MXN';

    Settings::shouldReceive('all')->andReturn($settings);
    Settings::shouldReceive('currency')->andReturn('MXN');

    $component
        ->assertDontSee('N/A')
        ->assertSee('USD')
        ->assertSee(0.29)
        ->emit('currencyChanged')
        ->assertDontSee('N/A')
        ->assertSee('MXN')
        ->assertSee(0.23);
});

it('should set currency', function () {
    Cookie::shouldReceive('queue')
        ->with('settings', json_encode([
            ...Settings::all(),
            'currency' => 'GBP',
        ]), 60 * 24 * 365 * 5)
        ->once();

    expect(Settings::currency())->toBe('USD');

    Livewire::test(PriceTicker::class)
        ->call('setCurrency', 'GBP');
});
