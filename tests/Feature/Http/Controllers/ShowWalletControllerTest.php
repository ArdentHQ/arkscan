<?php

declare(strict_types=1);

use App\Models\Wallet;
use App\Services\Cache\DelegateCache;
use App\Services\Cache\NetworkCache;

it('should render the page without any errors', function () {
    $this->withoutExceptionHandling();

    $wallet = Wallet::factory()->create();

    (new NetworkCache())->setSupply(fn () => '10000000000');

    ((new DelegateCache())->setTotalAmounts(fn () => [$wallet->public_key => '1000000000']));
    ((new DelegateCache())->setTotalFees(fn () => [$wallet->public_key => '1000000000']));
    ((new DelegateCache())->setTotalRewards(fn () => [$wallet->public_key => '1000000000']));
    ((new DelegateCache())->setTotalBlocks(fn () => [$wallet->public_key => '1000000000']));

    $this
        ->get(route('wallet', $wallet))
        ->assertOk();
});
