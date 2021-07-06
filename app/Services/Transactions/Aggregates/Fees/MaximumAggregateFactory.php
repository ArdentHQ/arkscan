<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Fees;

use App\Services\Transactions\Aggregates\Fees\Maximum\DayAggregate;
use App\Services\Transactions\Aggregates\Fees\Maximum\LastAggregate;
use App\Services\Transactions\Aggregates\Fees\Maximum\MonthAggregate;
use App\Services\Transactions\Aggregates\Fees\Maximum\QuarterAggregate;
use App\Services\Transactions\Aggregates\Fees\Maximum\WeekAggregate;
use App\Services\Transactions\Aggregates\Fees\Maximum\YearAggregate;
use Illuminate\Support\Str;
use InvalidArgumentException;

final class MaximumAggregateFactory
{
    /**
     * @return DayAggregate|LastAggregate|MonthAggregate|QuarterAggregate|WeekAggregate|YearAggregate
     */
    public static function make(string $period, ?string $type = null)
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

        if (Str::of($period)->contains('last')) {
            preg_match('/^[a-z]+(\d+)$/', $period, $match);

            return (new LastAggregate())
                ->setLimit((int) $match[1])
                ->setType($type ?? '');
        }

        throw new InvalidArgumentException('Invalid aggregate period.');
    }
}
