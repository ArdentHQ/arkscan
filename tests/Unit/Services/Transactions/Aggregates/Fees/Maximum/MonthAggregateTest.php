<?php

declare(strict_types=1);

use App\Models\Transaction;

use App\Services\Timestamp;
use App\Services\Transactions\Aggregates\Fees\Maximum\MonthAggregate;
use Carbon\Carbon;

it('should determine the average fee for the given date range', function () {
    Carbon::setTestNow('2021-01-01 00:00:00');

    Transaction::factory(10)->create([
        'fee'       => '100000000',
        'timestamp' => Timestamp::now()->subDays(29)->unix(),
    ]);

    Transaction::factory(10)->create([
        'fee'       => '200000000',
        'timestamp' => Timestamp::now()->subMinutes(10)->unix(),
    ]);

    $result = (new MonthAggregate())->aggregate();

    expect($result)->toBeFloat();
    expect($result)->toBe(2.0);
});
