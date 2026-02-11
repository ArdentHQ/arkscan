<?php

declare(strict_types=1);

use App\DTO\Statistics\MarketDataRecordStatistics;
use App\DTO\Statistics\TimestampedValue;
use Carbon\Carbon;

it('should create statistics object correctly', function () {
    $timestamp = Carbon::now()->timestamp;

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

    expect($caps->today)->toEqual(20 * 1e8);
    expect($caps->atl->timestamp)->toEqual($timestamp);
    expect($caps->atl->value)->toEqual(0.2345);
    expect($caps->ath->timestamp)->toEqual($timestamp);
    expect($caps->ath->value)->toEqual(1.2345);

    expect($caps->toArray())->toBe([
        'today' => 20 * 1e8,
        'atl'   => [
            'timestamp' => $timestamp,
            'value'     => 0.2345,
        ],
        'ath'   => [
            'timestamp' => $timestamp,
            'value'     => 1.2345,
        ],
    ]);
});
