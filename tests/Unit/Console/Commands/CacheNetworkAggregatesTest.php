<?php

declare(strict_types=1);

use App\Console\Commands\CacheNetworkAggregates;
use App\Services\Cache\NetworkCache;

it('should execute the command', function () {
    $cache = new NetworkCache();
    $cache->setSupply(fn () => strval(100e8));

    (new CacheNetworkAggregates())->handle($cache);

    expect($cache->getVolume())->toBeFloat();
    expect($cache->getTransactionsCount())->toBeInt();
    expect($cache->getVotesCount())->toBeInt();
    expect($cache->getVotesPercentage())->toBeFloat();
    expect($cache->getDelegateRegistrationCount())->toBeInt();
    expect($cache->getFeesCollected())->toBeFloat();
});
