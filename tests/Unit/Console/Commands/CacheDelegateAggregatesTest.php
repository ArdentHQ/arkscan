<?php

declare(strict_types=1);

use App\Console\Commands\CacheDelegateAggregates;
use App\Services\Cache\DelegateCache;

it('should execute the command', function () {
    (new CacheDelegateAggregates())->handle($cache = new DelegateCache());

    expect($cache->getTotalAmounts())->toBeArray();
    expect($cache->getTotalFees())->toBeArray();
    expect($cache->getTotalRewards())->toBeArray();
    expect($cache->getTotalBlocks())->toBeArray();
});
