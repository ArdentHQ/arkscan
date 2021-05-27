<?php

declare(strict_types=1);

use App\Http\Livewire\WalletBalance;
use App\Models\Wallet;
use App\Services\Cache\CryptoCompareCache;
use Carbon\Carbon;
use Livewire\Livewire;

it('should show the balance of the wallet', function () {
    (new CryptoCompareCache())->setPrices('USD', collect([
        Carbon::now()->format('Y-m-d') => 10,
    ]));

    $wallet = Wallet::factory()->create(['balance' => 125456]);

    Livewire::test(WalletBalance::class, ['wallet' => $wallet])->assertSee('0.01 USD');
});
