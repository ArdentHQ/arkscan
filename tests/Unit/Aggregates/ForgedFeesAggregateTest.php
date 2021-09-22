<?php

declare(strict_types=1);

use App\Aggregates\ForgedFeesAggregate;
use App\Models\Block;

use function Spatie\Snapshots\assertMatchesSnapshot;

beforeEach(function () {
    Block::factory(10)->create([
        'total_amount' => '1000000000',
        'total_fee'    => '800000000',
        'reward'       => '200000000',
    ]);

    $this->subject = new ForgedFeesAggregate();
});

it('should aggregate and format', function () {
    assertMatchesSnapshot($this->subject->aggregate());
});
