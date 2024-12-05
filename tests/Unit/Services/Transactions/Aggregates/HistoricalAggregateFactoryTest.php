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
use App\Services\Transactions\Aggregates\Type\TransferAggregate;
use App\Services\Transactions\Aggregates\Type\UnvoteAggregate;
use App\Services\Transactions\Aggregates\Type\UsernameRegistrationAggregate;
use App\Services\Transactions\Aggregates\Type\UsernameResignationAggregate;
use App\Services\Transactions\Aggregates\Type\ValidatorRegistrationAggregate;
use App\Services\Transactions\Aggregates\Type\ValidatorResignationAggregate;
use App\Services\Transactions\Aggregates\Type\VoteAggregate;

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
    [StatsTransactionType::VOTE, VoteAggregate::class],
    [StatsTransactionType::UNVOTE, UnvoteAggregate::class],
    [StatsTransactionType::VALIDATOR_REGISTRATION, ValidatorRegistrationAggregate::class],
    [StatsTransactionType::VALIDATOR_RESIGNATION, ValidatorResignationAggregate::class],
    [StatsTransactionType::USERNAME_REGISTRATION, UsernameRegistrationAggregate::class],
    [StatsTransactionType::USERNAME_RESIGNATION, UsernameResignationAggregate::class],
]);

it('should throw if an unknown type is used', function () {
    HistoricalAggregateFactory::type('unknown');
})->throws(InvalidArgumentException::class);
