<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Services\Timestamp;
use App\Services\Transactions\Aggregates\Fees\Historical\WeekAggregate;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use function Spatie\Snapshots\assertMatchesSnapshot;

it('should aggregate the fees for 7 days', function () {
    Carbon::setTestNow('2021-01-01 00:00:00');

    Transaction::factory(10)->create([
        'fee'       => '100000000',
        'timestamp' => Timestamp::now()->subWeek()->unix(),
    ])->sortByDesc('timestamp');

    Transaction::factory(10)->create([
        'fee'       => '100000000',
        'timestamp' => Timestamp::now()->subMinutes(10)->unix(),
    ])->sortByDesc('timestamp');

    $result = (new WeekAggregate())->aggregate();

    expect($result)->toBeInstanceOf(Collection::class);
    assertMatchesSnapshot($result);
});
