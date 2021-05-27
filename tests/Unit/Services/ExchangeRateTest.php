<?php

declare(strict_types=1);

use App\Services\Cache\CryptoCompareCache;
use App\Services\ExchangeRate;
use Carbon\Carbon;

it('should convert with a historical rate', function () {
    (new CryptoCompareCache())->setPrices('USD', collect([]));

    expect(ExchangeRate::convert(1, 0))->toBe('0 USD');
});

it('should convert with the current rate', function () {
    (new CryptoCompareCache())->setPrices('USD', collect([
        Carbon::now()->format('Y-m-d') => 10,
    ]));

    expect(ExchangeRate::now(1))->toBe(10.0);
});

it('should use two decimals for a fiat currency', function () {
    expect(ExchangeRate::decimalsFor('USD'))->toBe(2);
    expect(ExchangeRate::decimalsFor('BTC'))->toBe(8);
    expect(ExchangeRate::decimalsFor('ETH'))->toBe(8);
    expect(ExchangeRate::decimalsFor('LTC'))->toBe(8);
});
