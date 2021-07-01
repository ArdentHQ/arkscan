<?php

declare(strict_types=1);

use App\Services\Transactions\Aggregates\Fees\Average\DayAggregate;
use App\Services\Transactions\Aggregates\Fees\Average\MonthAggregate;
use App\Services\Transactions\Aggregates\Fees\Average\QuarterAggregate;
use App\Services\Transactions\Aggregates\Fees\Average\WeekAggregate;
use App\Services\Transactions\Aggregates\Fees\Average\YearAggregate;
use App\Services\Transactions\Aggregates\Fees\AverageAggregateFactory;

it('should create an instance that matches the period', function (string $type, string $class) {
    expect(AverageAggregateFactory::make($type))->toBeInstanceOf($class);
})->with([
    ['day', DayAggregate::class],
    ['month', MonthAggregate::class],
    ['quarter', QuarterAggregate::class],
    ['week', WeekAggregate::class],
    ['year', YearAggregate::class],
]);

it('should throw if an unknown period is used', function () {
    AverageAggregateFactory::make('unknown');
})->throws(InvalidArgumentException::class);
