<?php

declare(strict_types=1);

use App\Aggregates\ForgedAmountsAggregate;
use App\Models\Block;

use function Tests\configureExplorerDatabase;

beforeEach(function () {
    configureExplorerDatabase();

    Block::factory(10)->create([
        'total_amount' => 10 * 1e8,
        'total_fee'    => 8 * 1e8,
        'reward'       => 2 * 1e8,
    ]);

    $this->subject = new ForgedAmountsAggregate();
});

it('should aggregate and format', function () {
    expect($this->subject->aggregate())->toBe('ARK 100.00');
});
