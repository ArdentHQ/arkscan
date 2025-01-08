<?php

declare(strict_types=1);

use App\DTO\Statistics\LowHighValue;
use App\DTO\Statistics\MarketDataPriceStatistics;
use App\DTO\Statistics\MarketDataRecordStatistics;
use App\DTO\Statistics\MarketDataStatistics;
use App\DTO\Statistics\MarketDataVolumeStatistics;
use App\DTO\Statistics\TimestampedValue;
use Carbon\Carbon;

it('should convert to and from wireable array', function () {
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

    $volume = MarketDataVolumeStatistics::make(
        strval(10 * 1e8),
        TimestampedValue::fromArray([
            'timestamp' => $timestamp,
            'value'     => 0.2345,
        ]),
        TimestampedValue::fromArray([
            'timestamp' => $timestamp,
            'value'     => 1.2345,
        ]),
    );

    $caps = MarketDataRecordStatistics::make(
        20 * 1e8,
        TimestampedValue::fromArray([
            'timestamp' => $timestamp,
            'value'     => 0.2345,
        ]),
        TimestampedValue::fromArray([
            'timestamp' => $timestamp,
            'value'     => 1.2345,
        ]),
    );

    $subject = MarketDataStatistics::make(
        $prices,
        $volume,
        $caps,
    );

    expect($subject->toLivewire())->toBe([
        'prices' => [
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
        ],

        'volume' => [
            'today' => strval(10 * 1e8),
            'atl'   => [
                'timestamp' => $timestamp,
                'value'     => 0.2345,
            ],
            'ath' => [
                'timestamp' => $timestamp,
                'value'     => 1.2345,
            ],
        ],

        'caps'   => [
            'today' => 20 * 1e8,
        ],
    ]);

    $subject = MarketDataStatistics::fromLivewire($subject->toLivewire());

    expect($subject->prices->atl->timestamp)->toEqual($timestamp);
    expect($subject->prices->atl->value)->toEqual(0.2345);
    expect($subject->prices->ath->timestamp)->toEqual($timestamp);
    expect($subject->prices->ath->value)->toEqual(1.2345);
    expect($subject->prices->daily->low)->toEqual(0.2345);
    expect($subject->prices->daily->high)->toEqual(1.2345);
    expect($subject->prices->year->low)->toEqual(0.2345);
    expect($subject->prices->year->high)->toEqual(1.2345);

    expect($subject->volume->today)->toEqual(strval(10 * 1e8));
    expect($subject->volume->atl->timestamp)->toEqual($timestamp);
    expect($subject->volume->atl->value)->toEqual(0.2345);
    expect($subject->volume->ath->timestamp)->toEqual($timestamp);
    expect($subject->volume->ath->value)->toEqual(1.2345);

    expect($subject->caps->today)->toEqual(20 * 1e8);
});
