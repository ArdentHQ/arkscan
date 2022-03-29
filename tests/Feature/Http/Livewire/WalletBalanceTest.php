<?php

declare(strict_types=1);

use App\Facades\Settings;
use App\Http\Livewire\WalletBalance;
use App\Models\Wallet;
use App\Services\Cache\CryptoDataCache;
use App\Services\NumberFormatter;
use Carbon\Carbon;
use Livewire\Livewire;

it('should show the balance of the wallet', function () {
    (new CryptoDataCache())->setPrices('USD.week', collect([
        Carbon::now()->format('Y-m-d') => 10,
    ]));

    $wallet = Wallet::factory()->create(['balance' => 125456]);

    Livewire::test(WalletBalance::class, ['wallet' => $wallet])
        ->assertSee(NumberFormatter::currency(0.01, 'USD'));
});

it('updates the balance when currency changes', function () {
    (new CryptoDataCache())->setPrices('USD.week', collect([
        Carbon::now()->format('Y-m-d') => 10,
    ]));

    (new CryptoDataCache())->setPrices('BTC.week', collect([
        Carbon::now()->format('Y-m-d') => 0.1234567,
    ]));

    $wallet = Wallet::factory()->create(['balance' => 125456]);

    $component = Livewire::test(WalletBalance::class, ['wallet' => $wallet])
        ->assertSee(NumberFormatter::currency(0.01, 'USD'));

    $settings             = Settings::all();
    $settings['currency'] = 'BTC';

    Settings::shouldReceive('all')->andReturn($settings);
    Settings::shouldReceive('currency')->andReturn('BTC');

    $component->emit('currencyChanged', 'BTC')->assertSee('0.00015488 BTC');
});
