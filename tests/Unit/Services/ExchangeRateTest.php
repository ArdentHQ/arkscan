<?php

declare(strict_types=1);

use App\Services\Cache\CryptoCompareCache;
use App\Services\ExchangeRate;
use App\Services\NumberFormatter;
use Carbon\Carbon;

it('should convert with a historical rate', function () {
    (new CryptoCompareCache())->setPrices('USD', collect([]));

    expect(ExchangeRate::convert(1, 0))->toBe(NumberFormatter::currency(0, 'USD'));
});

it('should convert with the current rate', function () {
    (new CryptoCompareCache())->setPrices('USD', collect([
        Carbon::now()->format('Y-m-d') => 10,
    ]));

    expect(ExchangeRate::now(1))->toBe(10.0);
});
