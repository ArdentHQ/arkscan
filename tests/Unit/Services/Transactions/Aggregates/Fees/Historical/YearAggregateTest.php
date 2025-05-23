<?php

declare(strict_types=1);

use App\Models\Receipt;
use App\Models\Transaction;
use App\Services\Transactions\Aggregates\Fees\Historical\YearAggregate;
use Carbon\Carbon;
use Illuminate\Support\Collection;

it('should aggregate the fees for 12 months', function () {
    Carbon::setTestNow('2021-01-01 00:00:00');

    $startTime = Carbon::parse('2020-10-19 04:54:16');
    $endTime   = Carbon::now()->subMinutes(10);

    Transaction::factory(10)
        ->withReceipt(10000)
        ->create([
            'gas_price' => 1,
            'timestamp' => $startTime->getTimestampMs(),
        ])->sortByDesc('timestamp');

    Transaction::factory(10)
        ->withReceipt(10000)
        ->create([
            'gas_price' => 1,
            'timestamp' => $endTime->getTimestampMs(),
        ]);

    $result = (new YearAggregate())->aggregate();

    expect($result)->toBeInstanceOf(Collection::class);
    expect($result->toArray())->toEqual([
        'Feb' => 0,
        'Mar' => 0,
        'Apr' => 0,
        'May' => 0,
        'Jun' => 0,
        'Jul' => 0,
        'Aug' => 0,
        'Sep' => 0,
        'Oct' => 100000.0,
        'Nov' => 0,
        'Dec' => 100000.0,
        'Jan' => 0,
    ]);
});
