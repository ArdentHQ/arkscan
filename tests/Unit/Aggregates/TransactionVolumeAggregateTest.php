<?php

declare(strict_types=1);

use App\Aggregates\TransactionVolumeAggregate;
use App\Models\Transaction;

beforeEach(function () {
    Transaction::factory(10)->create([
        'amount' => '500000000',
    ]);

    $this->subject = new TransactionVolumeAggregate();
});

it('should aggregate and format', function () {
    expect($this->subject->aggregate())->toBe('50');
});
