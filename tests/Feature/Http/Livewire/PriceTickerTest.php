<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Http\Livewire\PriceTicker;
use App\Services\Settings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;

it('should render with the source currency, target currency and exchange rate', function () {
    Http::fakeSequence()
        ->push(['USD' => 0.2907]);

    Livewire::test(PriceTicker::class)
        ->assertSee(Network::currency())
        ->assertSee(Settings::currency())
        ->assertSee(0.29);
});

it('should update the price if currency change', function () {
    Http::fakeSequence()
        ->push(['USD' => 0.2907])
        ->push(['MXN' => 0.22907]);

    $component = Livewire::test(PriceTicker::class);

    $settings = Settings::all();
    $settings['currency'] = 'MXN';

    Session::put('settings', json_encode($settings));

    $component
        ->assertSee('ARK')
        ->assertSee('USD')
        ->assertSee(0.29)
        ->emit('currencyChanged')
        ->assertSee('ARK')
        ->assertSee('MXN')
        ->assertSee(0.23);
});
