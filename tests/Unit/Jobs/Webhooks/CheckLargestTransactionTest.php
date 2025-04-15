<?php

declare(strict_types=1);

use App\Events\Statistics\TransactionDetails;
use App\Jobs\Webhooks\CheckLargestTransaction;
use App\Models\Transaction;
use App\Services\Cache\TransactionCache;
use App\Services\Timestamp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;

it('should not dispatch transaction details event if no change', function () {
    Event::fake();

    $cache = new TransactionCache();

    $this->travelTo('2024-04-19 00:15:44');

    $transaction = Transaction::factory()->transfer()->create([
        'amount'    => 1 * 1e8,
        'gas_price' => 0.1,
        'timestamp' => Timestamp::fromUnix(Carbon::parse('2024-04-19 00:15:44')->unix())->unix(),
    ]);

    $cache->setLargestIdByAmount($transaction->hash);

    expect($cache->getLargestIdByAmount())->toEqual($transaction->hash);

    (new CheckLargestTransaction())->handle();

    Event::assertDispatchedTimes(TransactionDetails::class, 0);

    (new CheckLargestTransaction())->handle();

    Event::assertDispatchedTimes(TransactionDetails::class, 0);
});

it('should dispatch transaction details event', function () {
    Event::fake();

    $cache = new TransactionCache();

    $this->travelTo('2024-04-19 00:15:44');

    $transaction = Transaction::factory()->transfer()->create([
        'amount'    => 1 * 1e8,
        'gas_price' => 0.1,
        'timestamp' => Timestamp::fromUnix(Carbon::parse('2024-04-19 00:15:44')->unix())->unix(),
    ]);

    $cache->setLargestIdByAmount($transaction->hash);

    expect($cache->getLargestIdByAmount())->toEqual($transaction->hash);

    (new CheckLargestTransaction())->handle();

    Event::assertDispatchedTimes(TransactionDetails::class, 0);

    $transaction = Transaction::factory()->transfer()->create([
        'amount'    => 20 * 1e8,
        'gas_price' => 0.2,
        'timestamp' => Timestamp::fromUnix(Carbon::parse('2024-04-20 00:15:44')->unix())->unix(),
    ]);

    (new CheckLargestTransaction())->handle();

    Event::assertDispatchedTimes(TransactionDetails::class, 1);
});
