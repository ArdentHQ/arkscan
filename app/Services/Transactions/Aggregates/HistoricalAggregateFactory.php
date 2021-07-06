<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates;

use App\Services\Transactions\Aggregates\Historical\AllAggregate;
use App\Services\Transactions\Aggregates\Historical\DayAggregate;
use App\Services\Transactions\Aggregates\Historical\MonthAggregate;
use App\Services\Transactions\Aggregates\Historical\QuarterAggregate;
use App\Services\Transactions\Aggregates\Historical\WeekAggregate;
use App\Services\Transactions\Aggregates\Historical\YearAggregate;
use InvalidArgumentException;

final class HistoricalAggregateFactory
{
    public static function make(string $period): DayAggregate | WeekAggregate | MonthAggregate | QuarterAggregate | YearAggregate | AllAggregate
    {
        if ($period === 'day') {
            return new DayAggregate();
        }

        if ($period === 'week') {
            return new WeekAggregate();
        }

        if ($period === 'month') {
            return new MonthAggregate();
        }

        if ($period === 'quarter') {
            return new QuarterAggregate();
        }

        if ($period === 'year') {
            return new YearAggregate();
        }

        if ($period === 'all') {
            return new AllAggregate();
        }

        throw new InvalidArgumentException('Invalid aggregate period.');
    }
}
