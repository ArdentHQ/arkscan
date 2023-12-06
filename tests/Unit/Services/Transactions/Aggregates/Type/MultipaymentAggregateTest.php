<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Services\Transactions\Aggregates\Type\MultipaymentAggregate;

it('should return count', function () {
    expect((new MultipaymentAggregate())->aggregate())->toBe(0);

    Transaction::factory(10)
        ->multipayment()
        ->create();

    expect((new MultipaymentAggregate())->aggregate())->toBe(10);
});
