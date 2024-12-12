<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Transactions\Aggregates\Type\VoteAggregate;

it('should return count', function () {
    expect((new VoteAggregate())->aggregate())->toBe(0);

    $wallet = Wallet::factory()->activeValidator()->create();

    Transaction::factory(10)
        ->vote($wallet->address)
        ->create();

    expect((new VoteAggregate())->aggregate())->toBe(10);
});
