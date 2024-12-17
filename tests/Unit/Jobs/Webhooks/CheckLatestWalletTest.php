<?php

declare(strict_types=1);

use App\Events\Statistics\UniqueAddresses;
use App\Jobs\Webhooks\CheckLatestWallet;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Cache\StatisticsCache;
use ARKEcosystem\Foundation\UserInterface\Support\DateFormat;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;

it('should not dispatch unique addresses event if no change', function () {
    Event::fake();

    $this->travelTo('2024-04-19 00:15:44');

    $wallet = Wallet::factory()->create();
    $timestamp = Carbon::parse('2024-04-19 00:15:44')->getTimestampMs();
    $transaction = Transaction::factory()->transfer()->create([
        'sender_public_key' => $wallet->public_key,
        'timestamp' => Carbon::parse('2024-04-19 00:15:44')->getTimestampMs(),
    ]);

    $wallet->fill(['updated_at' => $timestamp])->save();

    (new CheckLatestWallet())->handle();

    $cache = new StatisticsCache();

    expect($cache->getNewestAddress())->toEqual([
        'address'   => $wallet->address,
        'timestamp' => $transaction->timestamp * 1000,
        'value'     => Carbon::createFromTimestamp((int) $transaction->timestamp)->format(DateFormat::DATE),
    ]);

    Event::fake();

    (new CheckLatestWallet())->handle();

    Event::assertDispatchedTimes(UniqueAddresses::class, 0);
});

it('should dispatch unique addresses event', function () {
    Event::fake();

    $this->travelTo('2024-04-19 00:15:43');

    $walletA = Wallet::factory()->create();
    $timestampA = Carbon::parse('2024-04-19 00:15:44')->getTimestampMs();
    $transaction = Transaction::factory()->transfer()->create([
        'sender_public_key' => $walletA->public_key,
        'timestamp' => $timestampA,
    ]);

    $walletA->fill(['updated_at' => $timestampA])->save();

    (new CheckLatestWallet())->handle();

    Event::assertDispatchedTimes(UniqueAddresses::class, 1);

    $cache = new StatisticsCache();

    expect($cache->getNewestAddress())->toEqual([
        'address'   => $walletA->address,
        'timestamp' => $transaction->timestamp * 1000,
        'value'     => Carbon::createFromTimestamp((int) $transaction->timestamp)->format(DateFormat::DATE),
    ]);

    Event::fake();

    $walletB = Wallet::factory()->create();
    $timestampB = Carbon::parse('2024-04-20 00:15:44')->getTimestampMs();
    $transaction = Transaction::factory()->create([
        'sender_public_key' => $walletB->public_key,
        'timestamp' => $timestampB,
    ]);

    $walletB->fill(['updated_at' => $timestampB])->save();

    (new CheckLatestWallet())->handle();

    Event::assertDispatchedTimes(UniqueAddresses::class, 1);
});
