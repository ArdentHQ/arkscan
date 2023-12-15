<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Services\Transactions\Aggregates\Type\VoteCombinationAggregate;

it('should return count', function () {
    expect((new VoteCombinationAggregate())->aggregate())->toBe(0);

    Transaction::factory(10)
        ->voteCombination()
        ->create();

    expect((new VoteCombinationAggregate())->aggregate())->toBe(10);
});
