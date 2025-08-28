<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Services\Transactions\Aggregates\Fees\Historical\DayAggregate;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use function Spatie\Snapshots\assertMatchesSnapshot;

it('should aggregate the fees for today', function () {
    Carbon::setTestNow('2021-01-01 00:00:00');

    Transaction::factory(10)
        ->create([
            'gas_price' => 1,
            'timestamp' => Carbon::now()->subDay()->startOfDay()->getTimestampMs(),
            'gas_used'  => 10000,
        ]);

    Transaction::factory(10)
        ->create([
            'gas_price' => 1,
            'timestamp' => Carbon::now()->subMinutes(10)->getTimestampMs(),
            'gas_used'  => 10000,
        ]);

    $result = (new DayAggregate())->aggregate();

    expect($result)->toBeInstanceOf(Collection::class);
    assertMatchesSnapshot($result);
});
