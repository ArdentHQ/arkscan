<?php

declare(strict_types=1);

use App\Models\Receipt;
use App\Models\Transaction;
use App\Services\Transactions\Aggregates\Fees\Historical\MonthAggregate;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use function Spatie\Snapshots\assertMatchesSnapshot;

it('should aggregate the fees for 30 days', function () {
    Carbon::setTestNow('2021-01-01 00:00:00');

    Transaction::factory(10)->create([
        'gas_price' => 1,
        'timestamp' => Carbon::now()->subDays(30)->startOfDay()->getTimestampMs(),
    ]);

    Transaction::factory(10)->create([
        'gas_price' => 1,
        'timestamp' => Carbon::now()->subMinutes(10)->getTimestampMs(),
    ]);

    foreach (Transaction::all() as $transaction) {
        Receipt::factory()->create([
            'id'       => $transaction->id,
            'gas_used' => 1e9,
        ]);
    }

    $result = (new MonthAggregate())->aggregate();

    expect($result)->toBeInstanceOf(Collection::class);
    assertMatchesSnapshot($result);
});
