<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Services\Timestamp;
use App\Services\Transactions\Aggregates\Fees\Historical\RangeAggregate;
use Illuminate\Support\Collection;
use function Spatie\Snapshots\assertMatchesSnapshot;

it('should aggregate the fees for the given range', function () {
    $start = Transaction::factory(10)->create([
        'fee'       => '100000000',
        'timestamp' => 112982056,
    ])->sortByDesc('timestamp');

    $end = Transaction::factory(10)->create([
        'fee'       => '100000000',
        'timestamp' => 122982056,
    ])->sortByDesc('timestamp');

    $result = (new RangeAggregate())->aggregate(
        Timestamp::fromGenesis($start->last()->timestamp)->startOfDay(),
        Timestamp::fromGenesis($end->last()->timestamp)->endOfDay(),
        'YYYY-MM-DD'
    );

    expect($result)->toBeInstanceOf(Collection::class);
    assertMatchesSnapshot($result);
});
