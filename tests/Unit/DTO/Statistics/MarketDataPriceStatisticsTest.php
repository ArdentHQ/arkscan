<?php

declare(strict_types=1);

use App\DTO\Statistics\LowHighValue;
use App\DTO\Statistics\MarketDataPriceStatistics;
use App\DTO\Statistics\TimestampedValue;
use App\Facades\Settings;
use Carbon\Carbon;

it('should create statistics object correctly', function () {
    $timestamp = Carbon::now()->timestamp;

    $prices = MarketDataPriceStatistics::make(
        TimestampedValue::fromArray([
            'timestamp' => $timestamp,
            'value'     => 0.2345,
        ]),
        TimestampedValue::fromArray([
            'timestamp' => $timestamp,
            'value'     => 1.2345,
        ]),
        LowHighValue::fromArray([
            'low'  => 0.2345,
            'high' => 1.2345,
        ]),
        LowHighValue::fromArray([
            'low'  => 0.2345,
            'high' => 1.2345,
        ]),
    );

    expect($prices->atl->timestamp)->toEqual($timestamp);
    expect($prices->atl->value)->toEqual(0.2345);
    expect($prices->ath->timestamp)->toEqual($timestamp);
    expect($prices->ath->value)->toEqual(1.2345);
    expect($prices->daily->low)->toEqual(0.2345);
    expect($prices->daily->high)->toEqual(1.2345);
    expect($prices->year->low)->toEqual(0.2345);
    expect($prices->year->high)->toEqual(1.2345);

    expect($prices->toArray())->toBe([
        'atl'   => [
            'timestamp' => $timestamp,
            'value'     => 0.2345,
        ],
        'ath'   => [
            'timestamp' => $timestamp,
            'value'     => 1.2345,
        ],
        'daily' => [
            'low'  => 0.2345,
            'high' => 1.2345,
        ],
        'year'  => [
            'low'  => 0.2345,
            'high' => 1.2345,
        ],
    ]);
});

it('should fall back to 8 decimal places', function () {
    Settings::shouldReceive('currency')
        ->andReturn('BTC');

    $timestamp = Carbon::now()->timestamp;

    $prices = MarketDataPriceStatistics::make(
        TimestampedValue::fromArray([
            'timestamp' => $timestamp,
            'value'     => 0.234567890123,
        ]),
        TimestampedValue::fromArray([
            'timestamp' => $timestamp,
            'value'     => 1.234567890123,
        ]),
        LowHighValue::fromArray([
            'low'  => 0.234567890123,
            'high' => 1.234567890123,
        ]),
        LowHighValue::fromArray([
            'low'  => 0.234567890123,
            'high' => 1.234567890123,
        ]),
    );

    expect($prices->athValue())->toBe('1.23456789 BTC');
    expect($prices->atlValue())->toBe('0.23456789 BTC');
    expect($prices->dailyHigh())->toBe('1.23456789 BTC');
    expect($prices->dailyLow())->toBe('0.23456789 BTC');
});
