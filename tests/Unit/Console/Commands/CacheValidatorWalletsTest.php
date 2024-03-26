<?php

declare(strict_types=1);

use App\Console\Commands\CacheValidatorWallets;
use App\Models\Wallet;
use App\Services\Cache\WalletCache;

it('should execute the command', function () {
    $wallet = Wallet::factory()->create();

    (new CacheValidatorWallets())->handle($cache = new WalletCache());

    expect($cache->getValidator($wallet->public_key))->toBeInstanceOf(Wallet::class);
});
