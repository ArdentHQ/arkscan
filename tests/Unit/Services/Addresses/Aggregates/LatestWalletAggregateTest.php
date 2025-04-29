<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Addresses\Aggregates\LatestWalletAggregate;
use App\Services\Cache\StatisticsCache;
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
        'to'                => $walletA->address,
        'timestamp'         => 0,
    ]);

    $walletA->fill(['updated_at' => 0])->save();

    $result = $aggregate->aggregate();
    expect($result)->not->toBeNull();
    expect($result->address)->toBe($walletA->address);

    $genesisTimestamp = Carbon::parse('2021-01-01 13:24:44')->getTimestampMs();
    $this->travelTo('2021-01-01 13:24:45');

    Transaction::factory()->transfer()->create([
        'sender_public_key' => $walletA->public_key,
        'to'                => $walletB->address,
        'timestamp'         => $genesisTimestamp,
    ]);

    $walletA->fill(['updated_at' => $genesisTimestamp])->save();
    $walletB->fill(['updated_at' => $genesisTimestamp])->save();

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
        'to'                => $walletA->address,
        'timestamp'         => 0,
    ]);

    $walletA->fill(['updated_at' => 0])->save();

    $result = $aggregate->aggregate();
    expect($result)->not->toBeNull();
    expect($result->address)->toBe($walletA->address);

    $genesisTimestamp = Carbon::parse('2021-01-01 13:24:44')->getTimestampMs();
    $this->travelTo('2021-01-01 13:24:45');

    Transaction::factory()->transfer()->create([
        'sender_public_key' => $walletA->public_key,
        'to'                => $walletB->address,
        'timestamp'         => $genesisTimestamp,
    ]);

    $walletA->fill(['updated_at' => $genesisTimestamp])->save();
    $walletB->fill(['updated_at' => $genesisTimestamp])->save();

    $result = $aggregate->aggregate();
    expect($result)->not->toBeNull();
    expect($result->address)->toBe($walletB->address);

    expect($cache->getNewestAddress())->toBe([
        'address'   => $walletB->address,
        'timestamp' => $genesisTimestamp,
        'value'     => '01 Jan 2021',
    ]);

    $newestTimestamp = Carbon::parse('2021-01-01 14:24:44')->getTimestampMs();
    $this->travelTo('2021-01-01 14:24:45');

    Transaction::factory()->transfer()->create([
        'sender_public_key' => $walletB->public_key,
        'to'                => $walletC->address,
        'timestamp'         => $newestTimestamp,
    ]);

    $walletB->fill(['updated_at' => $newestTimestamp])->save();
    $walletC->fill(['updated_at' => $newestTimestamp])->save();

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

    // Wallet A Transaction
    Transaction::factory()->transfer()->create([
        'sender_public_key' => $walletA->public_key,
        'to'                => $walletA->address,
        'timestamp'         => 0,
    ]);

    $walletA->fill(['updated_at' => 0])->save();

    $result = $aggregate->aggregate();

    expect($result)->not->toBeNull();
    expect($result->address)->toBe($walletA->address);

    // Wallet C Transaction
    $existingTimestamp = Carbon::parse('2021-01-01 12:24:44')->getTimestampMs();
    $this->travelTo('2021-01-01 12:24:45');

    Transaction::factory()->transfer()->create([
        'sender_public_key' => $walletC->public_key,
        'to'                => $walletC->address,
        'timestamp'         => $existingTimestamp,
    ]);

    $walletC->fill(['updated_at' => $existingTimestamp])->save();

    // Wallet B transaction
    $genesisTimestamp = Carbon::parse('2021-01-01 13:24:44')->getTimestampMs();
    $this->travelTo('2021-01-01 13:24:45');

    Transaction::factory()->transfer()->create([
        'sender_public_key' => $walletA->public_key,
        'to'                => $walletB->address,
        'timestamp'         => $genesisTimestamp,
    ]);

    $walletA->fill(['updated_at' => $genesisTimestamp])->save();
    $walletB->fill(['updated_at' => $genesisTimestamp])->save();

    $result = $aggregate->aggregate();

    expect($result)->not->toBeNull();
    expect($result->address)->toBe($walletB->address);

    expect($cache->getNewestAddress())->toBe([
        'address'   => $walletB->address,
        'timestamp' => $genesisTimestamp,
        'value'     => '01 Jan 2021',
    ]);

    // Wallet C Transaction
    $newestTimestamp = Carbon::parse('2021-01-01 14:24:44')->getTimestampMs();
    $this->travelTo('2021-01-01 14:24:45');

    Transaction::factory()->transfer()->create([
        'sender_public_key' => $walletB->public_key,
        'to'                => $walletC->address,
        'timestamp'         => $newestTimestamp,
    ]);

    $walletB->fill(['updated_at' => $newestTimestamp])->save();
    $walletC->fill(['updated_at' => $newestTimestamp])->save();

    $result = $aggregate->aggregate();

    expect($result)->not->toBeNull();
    expect($result->address)->toBe($walletB->address);

    expect($cache->getNewestAddress())->toBe([
        'address'   => $walletB->address,
        'timestamp' => $genesisTimestamp,
        'value'     => '01 Jan 2021',
    ]);
});
