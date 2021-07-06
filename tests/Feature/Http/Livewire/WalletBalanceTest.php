<?php

declare(strict_types=1);

use App\Http\Livewire\WalletBalance;
use App\Models\Wallet;
use App\Services\Cache\CryptoCompareCache;
use App\Services\NumberFormatter;
use App\Services\Settings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;

it('should show the balance of the wallet', function () {
    (new CryptoCompareCache())->setPrices('USD', collect([
        Carbon::now()->format('Y-m-d') => 10,
    ]));

    $wallet = Wallet::factory()->create(['balance' => 125456]);

    Livewire::test(WalletBalance::class, ['wallet' => $wallet])
        ->assertSee(NumberFormatter::currency(0.01, 'USD'));
});

it('updates the balance when currency changes', function () {
    (new CryptoCompareCache())->setPrices('USD', collect([
        Carbon::now()->format('Y-m-d') => 10,
    ]));

    (new CryptoCompareCache())->setPrices('BTC', collect([
        Carbon::now()->format('Y-m-d') => 0.1234567,
    ]));

    $wallet = Wallet::factory()->create(['balance' => 125456]);

    $component = Livewire::test(WalletBalance::class, ['wallet' => $wallet])
        ->assertSee(NumberFormatter::currency(0.01, 'USD'));

    $settings = Settings::all();
    $settings['currency'] = 'BTC';
    Session::put('settings', json_encode($settings));

    $component->emit('currencyChanged', 'BTC')->assertSee('0.00015488 BTC');
});
