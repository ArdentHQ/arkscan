<?php

declare(strict_types=1);

use App\Services\Transactions\Aggregates\Historical\AllAggregate;
use App\Services\Transactions\Aggregates\Historical\DayAggregate;
use App\Services\Transactions\Aggregates\Historical\MonthAggregate;
use App\Services\Transactions\Aggregates\Historical\QuarterAggregate;
use App\Services\Transactions\Aggregates\Historical\WeekAggregate;
use App\Services\Transactions\Aggregates\Historical\YearAggregate;
use App\Services\Transactions\Aggregates\HistoricalAggregateFactory;

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
