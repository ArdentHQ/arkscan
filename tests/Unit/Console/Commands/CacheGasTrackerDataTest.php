<?php

declare(strict_types=1);

use App\Console\Commands\CacheGasTrackerData;
use App\Services\Cache\MainsailCache;
use Illuminate\Support\Facades\Http;

it('should execute the command', function () {
    Http::fake([
        '*' => Http::response([
            'data' => [
                'evmCall' => [
                    'min' => '5',
                    'max' => '5',
                    'avg' => '5',
                ],
            ],
        ]),
    ]);

    $cache = new MainsailCache();

    expect($cache->getFees())->toEqual([]);

    (new CacheGasTrackerData())->handle();

    expect($cache->getFees())->toEqual([
        'min' => '2500000000',
        'max' => '7500000000',
        'avg' => '5000000000',
    ]);
});
