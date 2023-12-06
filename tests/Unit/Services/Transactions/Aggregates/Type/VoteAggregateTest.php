<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Services\Transactions\Aggregates\Type\VoteAggregate;

it('should return count', function () {
    expect((new VoteAggregate())->aggregate())->toBe(0);

    Transaction::factory(10)
        ->vote()
        ->create();

    expect((new VoteAggregate())->aggregate())->toBe(10);
});
