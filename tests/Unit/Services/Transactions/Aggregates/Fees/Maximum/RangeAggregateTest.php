<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Services\Timestamp;
use App\Services\Transactions\Aggregates\Fees\Maximum\RangeAggregate;

it('should determine the average fee for the given date range', function () {
    Transaction::factory()->create(['fee' => 1e8]);
    Transaction::factory()->create(['fee' => 2e8]);
    Transaction::factory()->create(['fee' => 3e8]);
    Transaction::factory()->create(['fee' => 4e8]);
    Transaction::factory()->create(['fee' => 5e8]);

    $transactions = Transaction::get();

    $result = (new RangeAggregate())->aggregate(
        Timestamp::fromGenesis($transactions->last()->timestamp)->startOfDay(),
        Timestamp::fromGenesis($transactions->last()->timestamp)->endOfDay()
    );

    expect($result)->toBeFloat();
    expect($result)->toBe(5.0);
});
