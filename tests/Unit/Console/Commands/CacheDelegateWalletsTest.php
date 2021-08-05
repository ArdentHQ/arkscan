<?php

declare(strict_types=1);

use App\Console\Commands\CacheDelegateWallets;
use App\Models\Wallet;
use App\Services\Cache\WalletCache;

it('should execute the command', function () {
    $wallet = Wallet::factory()->create();

    (new CacheDelegateWallets())->handle($cache = new WalletCache());

    expect($cache->getDelegate($wallet->public_key))->toBeInstanceOf(Wallet::class);
});
