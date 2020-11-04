<?php

declare(strict_types=1);

use App\Console\Commands\CacheNetworkStatistics;
use App\Services\Cache\NetworkCache;
use function Tests\configureExplorerDatabase;

it('should execute the command', function () {
    configureExplorerDatabase();

    $cache = new NetworkCache();
    $cache->setSupply(strval(100e8));

    (new CacheNetworkStatistics())->handle($cache);

    expect($cache->getVolume())->toBeFloat();
    expect($cache->getTransactionsCount())->toBeInt();
    expect($cache->getVotesCount())->toBeInt();
    expect($cache->getVotesPercentage())->toBeFloat();
    expect($cache->getDelegateRegistrationCount())->toBeInt();
    expect($cache->getFeesCollected())->toBeFloat();
});
