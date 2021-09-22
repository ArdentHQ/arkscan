<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Fees;

use App\Services\Transactions\Aggregates\Fees\Historical\AllAggregate;
use App\Services\Transactions\Aggregates\Fees\Historical\DayAggregate;
use App\Services\Transactions\Aggregates\Fees\Historical\MonthAggregate;
use App\Services\Transactions\Aggregates\Fees\Historical\QuarterAggregate;
use App\Services\Transactions\Aggregates\Fees\Historical\WeekAggregate;
use App\Services\Transactions\Aggregates\Fees\Historical\YearAggregate;
use InvalidArgumentException;

final class HistoricalAggregateFactory
{
    /**
     * @return DayAggregate|WeekAggregate|MonthAggregate|QuarterAggregate|YearAggregate|AllAggregate
     */
    public static function make(string $period)
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
