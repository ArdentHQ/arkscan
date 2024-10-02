<?php

declare(strict_types=1);

use App\Events\Statistics\UniqueAddresses;
use App\Facades\Network;
use App\Jobs\Webhooks\CheckLatestWallet;
use App\Models\Transaction;
use App\Services\Cache\StatisticsCache;
use App\Services\Timestamp;
use ARKEcosystem\Foundation\UserInterface\Support\DateFormat;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;

it('should not dispatch unique addresses event if no change', function () {
    Event::fake();

    $this->travelTo('2024-04-19 00:15:44');

    $transaction = Transaction::factory()->transfer()->create([
        'timestamp' => Timestamp::fromUnix(Carbon::parse('2024-04-19 00:15:44')->unix())->unix(),
    ]);

    (new CheckLatestWallet())->handle();

    $cache = new StatisticsCache();

    expect($cache->getNewestAddress())->toEqual([
        'address'   => $transaction->sender->address,
        'timestamp' => $transaction->timestamp,
        'value'     => Carbon::createFromTimestamp((int) $transaction->timestamp + (int) Network::epoch()->timestamp)->format(DateFormat::DATE),
    ]);

    Event::fake();

    (new CheckLatestWallet())->handle();

    Event::assertDispatchedTimes(UniqueAddresses::class, 0);
});

it('should dispatch unique addresses event', function () {
    Event::fake();

    $this->travelTo('2024-04-19 00:15:44');

    $transaction = Transaction::factory()->transfer()->create([
        'timestamp' => Timestamp::fromUnix(Carbon::parse('2024-04-19 00:15:44')->unix())->unix(),
    ]);

    (new CheckLatestWallet())->handle();

    $cache = new StatisticsCache();

    expect($cache->getNewestAddress())->toEqual([
        'address'   => $transaction->sender->address,
        'timestamp' => $transaction->timestamp,
        'value'     => Carbon::createFromTimestamp((int) $transaction->timestamp + (int) Network::epoch()->timestamp)->format(DateFormat::DATE),
    ]);

    Event::fake();

    $transaction = Transaction::factory()->create([
        'timestamp' => Timestamp::fromUnix(Carbon::parse('2024-04-20 00:15:44')->unix())->unix(),
    ]);

    (new CheckLatestWallet())->handle();

    Event::assertDispatchedTimes(UniqueAddresses::class, 1);
});