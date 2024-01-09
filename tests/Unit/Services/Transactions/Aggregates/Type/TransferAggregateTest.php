<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Services\Transactions\Aggregates\Type\TransferAggregate;

it('should return count', function () {
    expect((new TransferAggregate())->aggregate())->toBe(0);

    Transaction::factory(10)
        ->transfer()
        ->create();

    expect((new TransferAggregate())->aggregate())->toBe(10);
});
