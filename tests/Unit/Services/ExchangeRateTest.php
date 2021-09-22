<?php

declare(strict_types=1);

use App\Services\Cache\CryptoDataCache;
use App\Services\ExchangeRate;
use App\Services\NumberFormatter;
use App\Services\Timestamp;
use Carbon\Carbon;

it('should convert with a historical rate', function () {
    (new CryptoDataCache())->setPrices('USD.week', collect([
        Carbon::now()->subDays(3)->format('Y-m-d') => 1,
        Carbon::now()->subDays(2)->format('Y-m-d') => 2,
        Carbon::now()->subDays(1)->format('Y-m-d') => 3,
        Carbon::now()->format('Y-m-d')             => 10,
    ]));

    expect(ExchangeRate::convert(10, Timestamp::now()->subDays(1)->timestamp))
        ->toBe(NumberFormatter::currency(30, 'USD.week'));
});

it('should convert with the current rate', function () {
    (new CryptoDataCache())->setPrices('USD.day', collect([
        Carbon::now('-3 hours')->format('Y-m-d H:i:s') => 1,
        Carbon::now('-2 hours')->format('Y-m-d H:i:s') => 2,
        Carbon::now('-1 hour')->format('Y-m-d H:i:s')  => 3,
        Carbon::now()->format('Y-m-d H:i:s')           => 10,
    ]));

    expect(ExchangeRate::now())->toBe(10.0);
});
