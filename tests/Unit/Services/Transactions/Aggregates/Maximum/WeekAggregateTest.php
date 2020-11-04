<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Services\Timestamp;
use App\Services\Transactions\Aggregates\Fees\Maximum\WeekAggregate;
use Carbon\Carbon;
use function Tests\configureExplorerDatabase;

beforeEach(fn () => configureExplorerDatabase());

it('should determine the average fee for the given date range', function () {
    Carbon::setTestNow(Carbon::now());

    $start = Transaction::factory(10)->create([
        'fee'       => '100000000',
        'timestamp' => Timestamp::now()->subWeek()->unix(),
    ])->sortByDesc('timestamp');

    $end = Transaction::factory(10)->create([
        'fee'       => '200000000',
        'timestamp' => Timestamp::now()->endOfDay()->unix(),
    ])->sortByDesc('timestamp');

    $result = (new WeekAggregate())->aggregate(
        Timestamp::fromGenesis($start->last()->timestamp)->startOfDay(),
        Timestamp::fromGenesis($end->last()->timestamp)->endOfDay()
    );

    expect($result)->toBeFloat();
    expect($result)->toBe(2.0);
});
