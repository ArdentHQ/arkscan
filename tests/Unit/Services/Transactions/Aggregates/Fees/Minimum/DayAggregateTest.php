<?php

declare(strict_types=1);

use App\Models\Transaction;

use App\Services\Timestamp;
use App\Services\Transactions\Aggregates\Fees\Minimum\DayAggregate;
use Carbon\Carbon;

it('should determine the average fee for the given date range', function () {
    Carbon::setTestNow('2021-01-01 00:00:00');

    $start = Transaction::factory(10)->create([
        'fee'       => '100000000',
        'timestamp' => Timestamp::now()->subHours(23)->unix(),
    ])->sortByDesc('timestamp');

    $end = Transaction::factory(10)->create([
        'fee'       => '200000000',
        'timestamp' => Timestamp::now()->subMinutes(10)->unix(),
    ])->sortByDesc('timestamp');

    $result = (new DayAggregate())->aggregate(
        Timestamp::fromGenesis($start->last()->timestamp)->startOfDay(),
        Timestamp::fromGenesis($end->last()->timestamp)->endOfDay()
    );

    expect($result)->toBeFloat();
    expect($result)->toBe(1.0);
});
