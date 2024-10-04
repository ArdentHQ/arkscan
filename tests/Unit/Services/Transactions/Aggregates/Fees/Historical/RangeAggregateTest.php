<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Services\Transactions\Aggregates\Fees\Historical\RangeAggregate;
use Carbon\Carbon;
use Illuminate\Support\Collection;

it('should aggregate the fees for the given range', function () {
    $this->travelTo(Carbon::parse('2020-10-19 04:54:16'));

    $startTime = Carbon::now();
    $endTime   = Carbon::now()->addDays(200);

    $start = Transaction::factory(10)->create([
        'fee'       => 1 * 1e18,
        'timestamp' => $startTime->getTimestampMs(),
    ])->sortByDesc('timestamp');

    $end = Transaction::factory(10)->create([
        'fee'       => 1 * 1e18,
        'timestamp' => $endTime->getTimestampMs(),
    ])->sortByDesc('timestamp');

    $result = (new RangeAggregate())->aggregate(
        Carbon::createFromTimestamp($start->last()->timestamp)->startOfDay(),
        Carbon::createFromTimestamp($end->last()->timestamp)->endOfDay(),
        'YYYY-MM-DD'
    );

    expect($result)->toBeInstanceOf(Collection::class);
    expect($result->toArray())->toEqual([
        $startTime->format('Y-m-d') => 10,
        $endTime->format('Y-m-d')   => 10,
    ]);
});
