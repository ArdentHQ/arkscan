<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Services\Transactions\Aggregates\Type\UnvoteAggregate;

it('should return count', function () {
    expect((new UnvoteAggregate())->aggregate())->toBe(0);

    Transaction::factory(10)
        ->unvote()
        ->create();

    expect((new UnvoteAggregate())->aggregate())->toBe(10);
});
