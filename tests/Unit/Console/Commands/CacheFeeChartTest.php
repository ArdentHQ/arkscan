<?php

declare(strict_types=1);

use App\Console\Commands\CacheFeeChart;
use App\Services\Cache\FeeChartCache;
use function Tests\configureExplorerDatabase;

it('should execute the command', function () {
    configureExplorerDatabase();

    (new CacheFeeChart())->handle($cache = new FeeChartCache());

    expect($cache->getDay())->toBeArray();
    expect($cache->getWeek())->toBeArray();
    expect($cache->getMonth())->toBeArray();
    expect($cache->getQuarter())->toBeArray();
    expect($cache->getYear())->toBeArray();
});
