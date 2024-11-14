<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Addresses\Aggregates\LatestWalletAggregate;
use App\Services\Cache\StatisticsCache;
use App\Services\Timestamp;
use Carbon\Carbon;

it('should refresh the latest wallet - A > B', function () {
    $this->travelTo('2021-01-01 11:24:44');

    $cache     = new StatisticsCache();
    $aggregate = new LatestWalletAggregate();

    expect($cache->getNewestAddress())->toBeNull();

    $walletA  = Wallet::factory()->create(['address' => 'wallet-a']);
    $walletB  = Wallet::factory()->create(['address' => 'wallet-b']);

    Transaction::factory()->transfer()->create([
        'sender_public_key' => $walletA->public_key,
        'recipient_id'      => $walletA->address,
        'timestamp'         => 0,
    ]);

    $result = $aggregate->aggregate();
    expect($result)->not->toBeNull();
    expect($result->address)->toBe($walletA->address);

    $genesisTimestamp = Timestamp::fromUnix(Carbon::parse('2021-01-01 13:24:44')->unix())->unix();
    $this->travelTo('2021-01-01 13:24:45');

    Transaction::factory()->transfer()->create([
        'sender_public_key' => $walletA->public_key,
        'recipient_id'      => $walletB->address,
        'timestamp'         => $genesisTimestamp,
    ]);

    $result = $aggregate->aggregate();
    expect($result)->not->toBeNull();
    expect($result->address)->toBe($walletB->address);

    expect($cache->getNewestAddress())->toBe([
        'address'   => $walletB->address,
        'timestamp' => $genesisTimestamp,
        'value'     => '01 Jan 2021',
    ]);
});

it('should refresh the latest wallet - A > B > C', function () {
    $this->travelTo('2021-01-01 11:24:44');

    $cache     = new StatisticsCache();
    $aggregate = new LatestWalletAggregate();

    expect($cache->getNewestAddress())->toBeNull();

    $walletA  = Wallet::factory()->create(['address' => 'wallet-a']);
    $walletB  = Wallet::factory()->create(['address' => 'wallet-b']);
    $walletC  = Wallet::factory()->create(['address' => 'wallet-c']);

    Transaction::factory()->transfer()->create([
        'sender_public_key' => $walletA->public_key,
        'recipient_id'      => $walletA->address,
        'timestamp'         => 0,
    ]);

    $result = $aggregate->aggregate();
    expect($result)->not->toBeNull();
    expect($result->address)->toBe($walletA->address);

    $genesisTimestamp = Timestamp::fromUnix(Carbon::parse('2021-01-01 13:24:44')->unix())->unix();
    $this->travelTo('2021-01-01 13:24:45');

    Transaction::factory()->transfer()->create([
        'sender_public_key' => $walletA->public_key,
        'recipient_id'      => $walletB->address,
        'timestamp'         => $genesisTimestamp,
    ]);

    $result = $aggregate->aggregate();
    expect($result)->not->toBeNull();
    expect($result->address)->toBe($walletB->address);

    expect($cache->getNewestAddress())->toBe([
        'address'   => $walletB->address,
        'timestamp' => $genesisTimestamp,
        'value'     => '01 Jan 2021',
    ]);

    $newestTimestamp = Timestamp::fromUnix(Carbon::parse('2021-01-01 14:24:44')->unix())->unix();
    $this->travelTo('2021-01-01 14:24:45');

    Transaction::factory()->transfer()->create([
        'sender_public_key' => $walletB->public_key,
        'recipient_id'      => $walletC->address,
        'timestamp'         => $newestTimestamp,
    ]);

    $result = $aggregate->aggregate();
    expect($result)->not->toBeNull();
    expect($result->address)->toBe($walletC->address);

    expect($cache->getNewestAddress())->toBe([
        'address'   => $walletC->address,
        'timestamp' => $newestTimestamp,
        'value'     => '01 Jan 2021',
    ]);
});

it('should refresh the latest wallet - A > B > existing C', function () {
    $this->travelTo('2021-01-01 11:24:44');

    $cache     = new StatisticsCache();
    $aggregate = new LatestWalletAggregate();

    expect($cache->getNewestAddress())->toBeNull();

    $walletA  = Wallet::factory()->create(['address' => 'wallet-a']);
    $walletB  = Wallet::factory()->create(['address' => 'wallet-b']);
    $walletC  = Wallet::factory()->create(['address' => 'wallet-c']);

    Transaction::factory()->transfer()->create([
        'sender_public_key' => $walletA->public_key,
        'recipient_id'      => $walletA->address,
        'timestamp'         => 0,
    ]);

    $result = $aggregate->aggregate();
    expect($result)->not->toBeNull();
    expect($result->address)->toBe($walletA->address);

    $existingTimestamp = Timestamp::fromUnix(Carbon::parse('2021-01-01 12:24:44')->unix())->unix();
    $this->travelTo('2021-01-01 12:24:45');

    Transaction::factory()->transfer()->create([
        'sender_public_key' => $walletC->public_key,
        'recipient_id'      => $walletC->address,
        'timestamp'         => $existingTimestamp,
    ]);

    $genesisTimestamp = Timestamp::fromUnix(Carbon::parse('2021-01-01 13:24:44')->unix())->unix();
    $this->travelTo('2021-01-01 13:24:45');

    Transaction::factory()->transfer()->create([
        'sender_public_key' => $walletA->public_key,
        'recipient_id'      => $walletB->address,
        'timestamp'         => $genesisTimestamp,
    ]);

    $result = $aggregate->aggregate();
    expect($result)->not->toBeNull();
    expect($result->address)->toBe($walletB->address);

    expect($cache->getNewestAddress())->toBe([
        'address'   => $walletB->address,
        'timestamp' => $genesisTimestamp,
        'value'     => '01 Jan 2021',
    ]);

    $newestTimestamp = Timestamp::fromUnix(Carbon::parse('2021-01-01 14:24:44')->unix())->unix();
    $this->travelTo('2021-01-01 14:24:45');

    Transaction::factory()->transfer()->create([
        'sender_public_key' => $walletB->public_key,
        'recipient_id'      => $walletC->address,
        'timestamp'         => $newestTimestamp,
    ]);

    $result = $aggregate->aggregate();
    expect($result)->not->toBeNull();
    expect($result->address)->toBe($walletB->address);

    expect($cache->getNewestAddress())->toBe([
        'address'   => $walletB->address,
        'timestamp' => $genesisTimestamp,
        'value'     => '01 Jan 2021',
    ]);
});
