<?php

declare(strict_types=1);

use App\Services\Cache\NetworkCache;
use App\Services\Cache\NetworkStatusBlockCache;
use App\Services\MarketCap;

it('should calculate the supply', function () {
    (new NetworkStatusBlockCache())->setPrice('ARK', 'USD', 1.234);
    (new NetworkCache())->setSupply(fn () => 4.567 * 1e8);

    expect(MarketCap::get('ARK', 'USD'))->toBe(5.635678);
});

it('should return the supply formatted', function () {
    (new NetworkStatusBlockCache())->setPrice('ARK', 'USD', 1.234);
    (new NetworkCache())->setSupply(fn () => 4.567 * 1e8);

    expect(MarketCap::getFormatted('ARK', 'USD'))->toBe('$5.64');
});

it('should return the null if no price', function () {
    (new NetworkCache())->setSupply(fn () => 4.567 * 1e8);

    expect(MarketCap::get('ARK', 'USD'))->toBeNull;
    expect(MarketCap::getFormatted('ARK', 'USD'))->toBeNull;
});
