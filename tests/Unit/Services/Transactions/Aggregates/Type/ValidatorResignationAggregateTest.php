<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Services\Transactions\Aggregates\Type\ValidatorResignationAggregate;

it('should return count', function () {
    expect((new ValidatorResignationAggregate())->aggregate())->toBe(0);

    Transaction::factory(10)
        ->validatorResignation()
        ->create();

    expect((new ValidatorResignationAggregate())->aggregate())->toBe(10);
});
