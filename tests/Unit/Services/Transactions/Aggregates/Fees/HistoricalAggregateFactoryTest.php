<?php

declare(strict_types=1);

use App\Services\Transactions\Aggregates\Fees\Historical\AllAggregate;
use App\Services\Transactions\Aggregates\Fees\Historical\DayAggregate;
use App\Services\Transactions\Aggregates\Fees\Historical\MonthAggregate;
use App\Services\Transactions\Aggregates\Fees\Historical\QuarterAggregate;
use App\Services\Transactions\Aggregates\Fees\Historical\WeekAggregate;
use App\Services\Transactions\Aggregates\Fees\Historical\YearAggregate;
use App\Services\Transactions\Aggregates\Fees\HistoricalAggregateFactory;

it('should create an instance that matches the period', function (string $type, string $class) {
    expect(HistoricalAggregateFactory::make($type))->toBeInstanceOf($class);
})->with([
    ['day', DayAggregate::class],
    ['month', MonthAggregate::class],
    ['quarter', QuarterAggregate::class],
    ['week', WeekAggregate::class],
    ['year', YearAggregate::class],
    ['all', AllAggregate::class],
]);

it('should throw if an unknown period is used', function () {
    HistoricalAggregateFactory::make('unknown');
})->throws(InvalidArgumentException::class);
