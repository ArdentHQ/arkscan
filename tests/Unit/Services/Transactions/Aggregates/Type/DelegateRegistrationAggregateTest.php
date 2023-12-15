<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Services\Transactions\Aggregates\Type\DelegateRegistrationAggregate;

it('should return count', function () {
    expect((new DelegateRegistrationAggregate())->aggregate())->toBe(0);

    Transaction::factory(10)
        ->delegateRegistration()
        ->create();

    expect((new DelegateRegistrationAggregate())->aggregate())->toBe(10);
});
