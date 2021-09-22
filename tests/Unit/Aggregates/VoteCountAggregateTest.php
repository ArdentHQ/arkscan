<?php

declare(strict_types=1);

use App\Aggregates\VoteCountAggregate;
use App\Models\Wallet;

beforeEach(function () {
    Wallet::factory(10)->create([
        'balance'    => 1e8,
        'attributes' => ['vote' => 'pubkey'],
    ]);

    $this->subject = new VoteCountAggregate();
});

it('should aggregate and format', function () {
    expect($this->subject->aggregate())->toBe('10');
});
