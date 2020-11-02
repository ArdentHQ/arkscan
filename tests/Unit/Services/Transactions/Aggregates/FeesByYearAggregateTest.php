<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Services\Timestamp;
use App\Services\Transactions\Aggregates\FeesByYearAggregate;
use Carbon\Carbon;

use Illuminate\Support\Collection;
use function Spatie\Snapshots\assertMatchesSnapshot;
use function Tests\configureExplorerDatabase;

beforeEach(fn () => configureExplorerDatabase());

it('should aggregate the fees for 12 months', function () {
    Carbon::setTestNow(Carbon::now());

    $start = Transaction::factory(10)->create([
        'fee'       => '100000000',
        'timestamp' => 112982056,
    ])->sortByDesc('timestamp');

    $end = Transaction::factory(10)->create([
        'fee'       => '100000000',
        'timestamp' => Timestamp::now()->endOfDay()->unix(),
    ])->sortByDesc('timestamp');

    $result = (new FeesByYearAggregate())->aggregate(
        Timestamp::fromGenesis($start->last()->timestamp)->startOfDay(),
        Timestamp::fromGenesis($end->last()->timestamp)->endOfDay()
    );

    expect($result)->toBeInstanceOf(Collection::class);
    // assertMatchesSnapshot($result);
});
