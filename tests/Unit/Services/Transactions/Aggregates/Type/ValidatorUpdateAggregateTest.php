<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Services\Transactions\Aggregates\Type\ValidatorUpdateAggregate;

it('should return count', function () {
    expect((new ValidatorUpdateAggregate())->aggregate())->toBe(0);

    Transaction::factory(10)
        ->validatorUpdate()
        ->create();

    expect((new ValidatorUpdateAggregate())->aggregate())->toBe(10);
});
