<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Services\Cache\MainsailCache;
use App\Services\MainsailApi;
use Illuminate\Support\Facades\Http;

it('should cache fees', function () {
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

    MainsailApi::fees();

    expect($cache->getFees())->toEqual([
        'min' => '5000000000',
        'max' => '5000000000',
        'avg' => '5000000000',
    ]);
});

it('should update cache', function () {
    Http::fakeSequence()
        ->push([
            'data' => [
                'evmCall' => [
                    'min' => '5',
                    'max' => '5',
                    'avg' => '5',
                ],
            ],
        ])
        ->push([
            'data' => [
                'evmCall' => [
                    'min' => '10',
                    'max' => '10',
                    'avg' => '10',
                ],
            ],
        ]);

    $cache = new MainsailCache();

    expect($cache->getFees())->toEqual([]);

    MainsailApi::fees();

    expect($cache->getFees())->toEqual([
        'min' => '5000000000',
        'max' => '5000000000',
        'avg' => '5000000000',
    ]);

    MainsailApi::fees();

    expect($cache->getFees())->toEqual([
        'min' => '10000000000',
        'max' => '10000000000',
        'avg' => '10000000000',
    ]);
});

it('should revert to cache if request fails', function () {
    Http::fake([
        '*' => Http::response(fn () => throw new Exception('Failed to connect')),
    ]);

    $cache = new MainsailCache();

    $cache->setFees([
        'min' => '2500000000',
        'max' => '7500000000',
        'avg' => '5000000000',
    ]);

    MainsailApi::fees();

    expect($cache->getFees())->toEqual([
        'min' => '2500000000',
        'max' => '7500000000',
        'avg' => '5000000000',
    ]);
});

it('should revert to cache if response is invalid', function () {
    Http::fake([
        '*' => Http::response([
            'data' => [],
        ]),
    ]);

    $cache = new MainsailCache();

    $cache->setFees([
        'min' => '2500000000',
        'max' => '7500000000',
        'avg' => '5000000000',
    ]);

    MainsailApi::fees();

    expect($cache->getFees())->toEqual([
        'min' => '2500000000',
        'max' => '7500000000',
        'avg' => '5000000000',
    ]);
});

it('should get time to forge', function () {
    expect(MainsailApi::timeToForge())->toEqual(Network::blockTime());
});
