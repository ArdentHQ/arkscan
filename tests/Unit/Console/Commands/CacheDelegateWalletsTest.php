<?php

declare(strict_types=1);

use App\Console\Commands\CacheDelegateWallets;
use App\Models\Wallet;

use App\Services\Cache\WalletCache;
use function Tests\configureExplorerDatabase;

it('should execute the command', function () {
    configureExplorerDatabase();

    $wallet = Wallet::factory()->create();

    (new CacheDelegateWallets())->handle($cache = new WalletCache());

    expect($cache->getDelegate($wallet->public_key))->toBeInstanceOf(Wallet::class);
});
