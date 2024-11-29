<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates;

use App\Enums\StatsTransactionType;
use App\Services\Transactions\Aggregates\Historical\AllAggregate;
use App\Services\Transactions\Aggregates\Historical\AveragesAggregate;
use App\Services\Transactions\Aggregates\Historical\DayAggregate;
use App\Services\Transactions\Aggregates\Historical\MonthAggregate;
use App\Services\Transactions\Aggregates\Historical\QuarterAggregate;
use App\Services\Transactions\Aggregates\Historical\WeekAggregate;
use App\Services\Transactions\Aggregates\Historical\YearAggregate;
use App\Services\Transactions\Aggregates\Type\BatchTransferAggregate;
use App\Services\Transactions\Aggregates\Type\TransferAggregate;
use App\Services\Transactions\Aggregates\Type\UnvoteAggregate;
use App\Services\Transactions\Aggregates\Type\ValidatorRegistrationAggregate;
use App\Services\Transactions\Aggregates\Type\ValidatorResignationAggregate;
use App\Services\Transactions\Aggregates\Type\VoteAggregate;
use InvalidArgumentException;

final class HistoricalAggregateFactory
{
    public static function period(string $period): DayAggregate | WeekAggregate | MonthAggregate | QuarterAggregate | YearAggregate | AllAggregate
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

    public static function type(string $type): TransferAggregate | BatchTransferAggregate | VoteAggregate | UnvoteAggregate | ValidatorRegistrationAggregate | ValidatorResignationAggregate
    {
        if ($type === StatsTransactionType::TRANSFER) {
            return new TransferAggregate();
        }

        if ($type === StatsTransactionType::BATCH_TRANSFER) {
            return new BatchTransferAggregate();
        }

        if ($type === StatsTransactionType::VOTE) {
            return new VoteAggregate();
        }

        if ($type === StatsTransactionType::UNVOTE) {
            return new UnvoteAggregate();
        }

        if ($type === StatsTransactionType::VALIDATOR_REGISTRATION) {
            return new ValidatorRegistrationAggregate();
        }

        if ($type === StatsTransactionType::VALIDATOR_RESIGNATION) {
            return new ValidatorResignationAggregate();
        }

        throw new InvalidArgumentException('Invalid aggregate type.');
    }

    public static function averages(): AveragesAggregate
    {
        return new AveragesAggregate();
    }
}
