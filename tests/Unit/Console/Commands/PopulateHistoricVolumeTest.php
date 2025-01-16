<?php

declare(strict_types=1);

use App\Services\Cache\StatisticsCache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

it('should cache data', function () {
    $cache = new StatisticsCache();

    Artisan::call('explorer:populate-historic-volume');

    expect($cache->getVolumeAtl('BTC'))->toEqual([
        'timestamp' => 1688774400,
        'value'     => 1.3374225031894302,
    ]);

    expect($cache->getVolumeAth('USD'))->toEqual([
        'timestamp' => 1726963200,
        'value'     => 478176573.80723304,
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
    Artisan::call('explorer:populate-historic-volume', [], $outputBuffer);

    expect($cache->getVolumeAtl('TWD'))->toBeNull();

    expect($outputBuffer->fetch())->toEqual("Currency file not found for twd\n");
});
