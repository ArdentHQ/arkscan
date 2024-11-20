<?php

declare(strict_types=1);

use App\Services\Cache\MainsailCache;

it('should get & set fees', function () {
    $cache = new MainsailCache();

    expect($cache->getFees())->toEqual([]);

    $cache->setFees([
        'min' => '5',
        'max' => '5',
        'avg' => '5',
    ]);

    expect($cache->getFees())->toEqual([
        'min' => '5',
        'max' => '5',
        'avg' => '5',
    ]);
});
