<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Services\Transactions\Aggregates\Type\DelegateResignationAggregate;

it('should return count', function () {
    expect((new DelegateResignationAggregate())->aggregate())->toBe(0);

    Transaction::factory(10)
        ->delegateResignation()
        ->create();

    expect((new DelegateResignationAggregate())->aggregate())->toBe(10);
});
