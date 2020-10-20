<?php

declare(strict_types=1);

use App\Aggregates\TransactionCountAggregate;
use App\Models\Transaction;
use function Tests\configureExplorerDatabase;

beforeEach(function () {
    configureExplorerDatabase();

    Transaction::factory(10)->create();

    $this->subject = new TransactionCountAggregate();
});

it('should aggregate and format', function () {
    expect($this->subject->aggregate())->toBe('10');
});
