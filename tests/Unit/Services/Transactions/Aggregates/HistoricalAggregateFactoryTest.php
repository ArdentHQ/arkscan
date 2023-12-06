<?php

declare(strict_types=1);

use App\Enums\StatsTransactionType;
use App\Services\Transactions\Aggregates\Historical\AllAggregate;
use App\Services\Transactions\Aggregates\Historical\DayAggregate;
use App\Services\Transactions\Aggregates\Historical\MonthAggregate;
use App\Services\Transactions\Aggregates\Historical\QuarterAggregate;
use App\Services\Transactions\Aggregates\Historical\WeekAggregate;
use App\Services\Transactions\Aggregates\Historical\YearAggregate;
use App\Services\Transactions\Aggregates\HistoricalAggregateFactory;
use App\Services\Transactions\Aggregates\Type\DelegateRegistrationAggregate;
use App\Services\Transactions\Aggregates\Type\DelegateResignationAggregate;
use App\Services\Transactions\Aggregates\Type\MultipaymentAggregate;
use App\Services\Transactions\Aggregates\Type\TransferAggregate;
use App\Services\Transactions\Aggregates\Type\UnvoteAggregate;
use App\Services\Transactions\Aggregates\Type\VoteAggregate;
use App\Services\Transactions\Aggregates\Type\VoteCombinationAggregate;

it('should create an instance that matches the period', function (string $type, string $class) {
    expect(HistoricalAggregateFactory::period($type))->toBeInstanceOf($class);
})->with([
    ['day', DayAggregate::class],
    ['month', MonthAggregate::class],
    ['quarter', QuarterAggregate::class],
    ['week', WeekAggregate::class],
    ['year', YearAggregate::class],
    ['all', AllAggregate::class],
]);

it('should throw if an unknown period is used', function () {
    HistoricalAggregateFactory::period('unknown');
})->throws(InvalidArgumentException::class);

it('should create an instance that matches the type', function (string $type, string $class) {
    expect(HistoricalAggregateFactory::type($type))->toBeInstanceOf($class);
})->with([
    [StatsTransactionType::TRANSFER, TransferAggregate::class],
    [StatsTransactionType::MULTIPAYMENT, MultipaymentAggregate::class],
    [StatsTransactionType::VOTE, VoteAggregate::class],
    [StatsTransactionType::UNVOTE, UnvoteAggregate::class],
    [StatsTransactionType::SWITCH_VOTE, VoteCombinationAggregate::class],
    [StatsTransactionType::DELEGATE_REGISTRATION, DelegateRegistrationAggregate::class],
    [StatsTransactionType::DELEGATE_RESIGNATION, DelegateResignationAggregate::class],
]);

it('should throw if an unknown type is used', function () {
    HistoricalAggregateFactory::type('unknown');
})->throws(InvalidArgumentException::class);
