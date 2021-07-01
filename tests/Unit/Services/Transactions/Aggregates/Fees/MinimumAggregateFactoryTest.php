<?php

declare(strict_types=1);

use App\Services\Transactions\Aggregates\Fees\Minimum\DayAggregate;
use App\Services\Transactions\Aggregates\Fees\Minimum\MonthAggregate;
use App\Services\Transactions\Aggregates\Fees\Minimum\QuarterAggregate;
use App\Services\Transactions\Aggregates\Fees\Minimum\WeekAggregate;
use App\Services\Transactions\Aggregates\Fees\Minimum\YearAggregate;
use App\Services\Transactions\Aggregates\Fees\MinimumAggregateFactory;

it('should create an instance that matches the period', function (string $type, string $class) {
    expect(MinimumAggregateFactory::make($type))->toBeInstanceOf($class);
})->with([
    ['day', DayAggregate::class],
    ['month', MonthAggregate::class],
    ['quarter', QuarterAggregate::class],
    ['week', WeekAggregate::class],
    ['year', YearAggregate::class],
]);

it('should throw if an unknown period is used', function () {
    MinimumAggregateFactory::make('unknown');
})->throws(InvalidArgumentException::class);
