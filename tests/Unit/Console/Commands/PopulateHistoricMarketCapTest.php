<?php

declare(strict_types=1);

use App\Services\Cache\StatisticsCache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

it('should cache data', function () {
    $cache = new StatisticsCache();

    Artisan::call('explorer:populate-historic-market-cap');

    expect($cache->getMarketCapAtl('BTC'))->toEqual([
        'timestamp' => 1725235200,
        'value'     => 908.5268296374669,
    ]);

    expect($cache->getMarketCapAth('USD'))->toEqual([
        'timestamp' => 1515542400,
        'value'     => 1001554886.9196,
    ]);
});

it('should error for unknown currency', function () {
    Config::set('currencies', [
        'twd' => [
            'currency' => 'TWD',
            'symbol'   => 'NT$',
            'locale'   => 'zh_TW',
        ],
    ]);

    $cache = new StatisticsCache();

    $outputBuffer = new Symfony\Component\Console\Output\BufferedOutput();
    Artisan::call('explorer:populate-historic-market-cap', [], $outputBuffer);

    expect($cache->getMarketCapAtl('TWD'))->toBeNull();

    expect($outputBuffer->fetch())->toEqual("Currency file not found for twd\n");
});
