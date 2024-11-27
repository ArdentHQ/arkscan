<?php

declare(strict_types=1);

use App\Services\Cache\MainsailCache;
use App\Services\GasTracker;

it('should get fee', function ($method, $expected) {
    (new MainsailCache())->setFees([
        'min' => '1500000000',
        'avg' => '2500000000',
        'max' => '3500000000',
    ]);

    expect((new GasTracker())->{$method}())->toBe($expected);
})->with([
    'low' => ['low', 1.5],
    'avg' => ['average', 2.5],
    'max' => ['high', 3.5],
]);
