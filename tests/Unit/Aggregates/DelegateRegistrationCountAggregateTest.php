<?php

declare(strict_types=1);

use App\Aggregates\DelegateRegistrationCountAggregate;
use App\Models\Transaction;
use function Tests\configureExplorerDatabase;

beforeEach(function () {
    configureExplorerDatabase();

    Transaction::factory(10)->delegateRegistration()->create();

    $this->subject = new DelegateRegistrationCountAggregate();
});

it('should aggregate and format', function () {
    expect($this->subject->aggregate())->toBe('10');
});
