<?php

declare(strict_types=1);

use App\Facades\Settings;
use App\Services\Cache\CryptoDataCache;
use App\Services\Cache\NetworkStatusBlockCache;
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

it('should convert with current rate if no timestamp', function () {
    (new CryptoDataCache())->setPrices('USD.week', collect([
        Carbon::now()->subDays(3)->format('Y-m-d') => 1,
        Carbon::now()->subDays(2)->format('Y-m-d') => 2,
        Carbon::now()->subDays(1)->format('Y-m-d') => 3,
        Carbon::now()->format('Y-m-d')             => 10,
    ]));

    (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 24);

    expect(ExchangeRate::convert(10))
        ->toBe('$240.00');
});

it('should convert with a historical rate and return numerical value', function () {
    (new CryptoDataCache())->setPrices('USD.week', collect([
        Carbon::now()->subDays(3)->format('Y-m-d') => 1,
        Carbon::now()->subDays(2)->format('Y-m-d') => 2,
        Carbon::now()->subDays(1)->format('Y-m-d') => 3,
        Carbon::now()->format('Y-m-d')             => 10,
    ]));

    expect(ExchangeRate::convertNumerical(10, Timestamp::now()->subDays(1)->timestamp))
        ->toBe(30.0);
});

it('should convert with current rate and return numerical value', function () {
    (new CryptoDataCache())->setPrices('USD.week', collect([
        Carbon::now()->subDays(3)->format('Y-m-d') => 1,
        Carbon::now()->subDays(2)->format('Y-m-d') => 2,
        Carbon::now()->subDays(1)->format('Y-m-d') => 3,
        Carbon::now()->format('Y-m-d')             => 10,
    ]));

    (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 24);

    expect(ExchangeRate::convertNumerical(10))
        ->toBe(240.0);
});

it('should convert with the current rate', function () {
    (new CryptoDataCache())->setPrices('USD.day', collect([
        Carbon::parse('-3 hours')->format('Y-m-d H:i:s') => 1,
        Carbon::parse('-2 hours')->format('Y-m-d H:i:s') => 2,
        Carbon::parse('-1 hour')->format('Y-m-d H:i:s')  => 3,
        Carbon::now()->format('Y-m-d H:i:s')             => 10,
    ]));

    expect(ExchangeRate::now())->toBe(10.0);
});

it('should convert one currency to another', function () {
    Settings::shouldReceive('currency')
        ->andReturn('GBP');

    (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 1);
    (new NetworkStatusBlockCache())->setPrice('DARK', 'GBP', 2);

    expect(ExchangeRate::convertFiatToCurrency(4, 'USD', 'GBP'))->toBe('£8.00');
});

it('should convert one currency to crypto', function () {
    Settings::shouldReceive('currency')
        ->andReturn('BTC');

    (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 1);
    (new NetworkStatusBlockCache())->setPrice('DARK', 'BTC', 0.00002);

    expect(ExchangeRate::convertFiatToCurrency(4, 'USD', 'BTC'))->toBe('0.00008 BTC');
});

it('should return null if a currency value is missing', function () {
    Settings::shouldReceive('currency')
        ->andReturn('GBP');

    (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 1);

    expect(ExchangeRate::convertFiatToCurrency(4, 'USD', 'GBP'))->toBeNull();
});

it('should list all rates', function () {
    Settings::shouldReceive('currency')
        ->andReturn('USD');

    $this->travelTo(Carbon::parse('2023-07-05'));

    (new CryptoDataCache())->setPrices('USD.week', collect([
        Carbon::now()->subDays(3)->format('Y-m-d') => 1,
        Carbon::now()->subDays(2)->format('Y-m-d') => 2,
        Carbon::now()->subDays(1)->format('Y-m-d') => 3,
        Carbon::now()->format('Y-m-d')             => 10,
    ]));

    expect(ExchangeRate::rates())->toEqual(collect([
        '2023-07-02' => 1,
        '2023-07-03' => 2,
        '2023-07-04' => 3,
        '2023-07-05' => 10,
    ]));
});
