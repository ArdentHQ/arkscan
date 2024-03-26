<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Services\Transactions\Aggregates\Type\ValidatorRegistrationAggregate;

it('should return count', function () {
    expect((new ValidatorRegistrationAggregate())->aggregate())->toBe(0);

    Transaction::factory(10)
        ->validatorRegistration()
        ->create();

    expect((new ValidatorRegistrationAggregate())->aggregate())->toBe(10);
});
