<?php

declare(strict_types=1);

use App\DTO\Statistics\MarketDataVolumeStatistics;
use App\DTO\Statistics\TimestampedValue;
use Carbon\Carbon;

it('should create statistics object correctly', function () {
    $timestamp = Carbon::now()->timestamp;

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

    expect($volume->today)->toEqual(strval(10 * 1e8));
    expect($volume->atl->timestamp)->toEqual($timestamp);
    expect($volume->atl->value)->toEqual(0.2345);
    expect($volume->ath->timestamp)->toEqual($timestamp);
    expect($volume->ath->value)->toEqual(1.2345);

    expect($volume->toArray())->toBe([
        'today' => strval(10 * 1e8),
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
