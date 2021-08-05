<?php

declare(strict_types=1);

use App\Aggregates\DailyFeeAggregate;
use App\Models\Transaction;

beforeEach(function () {
    Transaction::factory(10)->delegateRegistration()->create();

    $this->subject = new DailyFeeAggregate();
});

it('should aggregate and format', function () {
    expect($this->subject->aggregate())->toBeString();
});
