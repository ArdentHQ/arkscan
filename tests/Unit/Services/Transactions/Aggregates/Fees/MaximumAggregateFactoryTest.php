<?php

declare(strict_types=1);

use App\Services\Transactions\Aggregates\Fees\Maximum\DayAggregate;
use App\Services\Transactions\Aggregates\Fees\Maximum\MonthAggregate;
use App\Services\Transactions\Aggregates\Fees\Maximum\QuarterAggregate;
use App\Services\Transactions\Aggregates\Fees\Maximum\WeekAggregate;
use App\Services\Transactions\Aggregates\Fees\Maximum\YearAggregate;
use App\Services\Transactions\Aggregates\Fees\MaximumAggregateFactory;

it('should create an instance that matches the period', function (string $type, string $class) {
    expect(MaximumAggregateFactory::make($type))->toBeInstanceOf($class);
})->with([
    ['day', DayAggregate::class],
    ['month', MonthAggregate::class],
    ['quarter', QuarterAggregate::class],
    ['week', WeekAggregate::class],
    ['year', YearAggregate::class],
]);

it('should throw if an unknown period is used', function () {
    MaximumAggregateFactory::make('unknown');
})->throws(InvalidArgumentException::class);
