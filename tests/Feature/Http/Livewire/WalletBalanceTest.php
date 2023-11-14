<?php

declare(strict_types=1);

use App\Facades\Settings;
use App\Http\Livewire\WalletBalance;
use App\Models\Wallet;
use App\Services\Cache\CryptoDataCache;
use App\Services\Cache\NetworkStatusBlockCache;
use App\Services\NumberFormatter;
use Carbon\Carbon;
use Livewire\Livewire;

it('should show the balance of the wallet', function () {
    (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 10);

    $wallet = Wallet::factory()->create(['balance' => 125456]);

    Livewire::test(WalletBalance::class, ['wallet' => $wallet])
        ->assertSee(NumberFormatter::currency(0.01, 'USD'));
});

it('updates the balance when currency changes', function () {
    (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 10);

    (new NetworkStatusBlockCache())->setPrice('DARK', 'BTC', 0.1234567);

    $wallet = Wallet::factory()->create(['balance' => 125456]);

    $component = Livewire::test(WalletBalance::class, ['wallet' => $wallet])
        ->assertSee(NumberFormatter::currency(0.01, 'USD'));

    $settings             = Settings::all();
    $settings['currency'] = 'BTC';

    Settings::shouldReceive('all')->andReturn($settings);
    Settings::shouldReceive('currency')->andReturn('BTC');

    $component->emit('currencyChanged', 'BTC')->assertSee('0.00015488 BTC');
});
