<?php

declare(strict_types=1);

use App\Console\Commands\CacheValidatorAggregates;
use App\Services\Cache\ValidatorCache;

it('should execute the command', function () {
    (new CacheValidatorAggregates())->handle($cache = new ValidatorCache());

    expect($cache->getTotalAmounts())->toBeArray();
    expect($cache->getTotalFees())->toBeArray();
    expect($cache->getTotalRewards())->toBeArray();
    expect($cache->getTotalBlocks())->toBeArray();
});
