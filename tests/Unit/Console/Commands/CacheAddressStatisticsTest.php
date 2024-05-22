<?php

declare(strict_types=1);

use App\Events\Statistics\AddressHoldings;
use App\Events\Statistics\StatisticsUpdate;
use App\Events\Statistics\UniqueAddresses;
use App\Facades\Network;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\Cache\StatisticsCache;
use App\Services\Timestamp;
use ARKEcosystem\Foundation\UserInterface\Support\DateFormat;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;

it('should cache address holdings', function () {
    Event::fake();

    $cache = new StatisticsCache();

    Wallet::factory()->create([
        'balance' => 1.1 * 1e8,
    ]);
    Wallet::factory()->count(1)->create([
        'balance' => 1 * 1e8,
    ]);
    Wallet::factory()->count(4)->create([
        'balance' => 0.9 * 1e8,
    ]);

    $this->artisan('explorer:cache-address-statistics');

    expect($cache->getAddressHoldings())->toBe([
        ['grouped' => 0, 'count' => 5],
        ['grouped' => 1, 'count' => 1],
    ]);

    Wallet::factory()->count(5)->create([
        'balance' => 10.1 * 1e8,
    ]);
    Wallet::factory()->count(3)->create([
        'balance' => 1000.1 * 1e8,
    ]);
    Wallet::factory()->count(2)->create([
        'balance' => BigNumber::new(1000000.1 * 1e8),
    ]);

    $this->artisan('explorer:cache-address-statistics');

    expect($cache->getAddressHoldings())->toBe([
        ['grouped' => 0, 'count' => 5],
        ['grouped' => 1, 'count' => 6],
        ['grouped' => 1000, 'count' => 3],
        ['grouped' => 1000000, 'count' => 2],
    ]);

    Event::assertDispatchedTimes(AddressHoldings::class, 2);
    Event::assertDispatchedTimes(UniqueAddresses::class, 2);
});

it('should cache unique addresses', function () {
    Event::fake();

    $cache = new StatisticsCache();

    $genesisWallet = Wallet::factory()->create([
        'address'    => 'genesis-address',
        'public_key' => 'genesis-public_key',
    ]);
    Transaction::factory()->transfer()->create([
        'block_height'      => 1,
        'sender_public_key' => $genesisWallet->public_key,
        'recipient_id'      => $genesisWallet->address,
        'timestamp'         => Timestamp::fromUnix(Carbon::parse('2023-04-01 15:04:13')->unix())->unix(),
    ]);

    $largest = Wallet::factory()->create([
        'balance' => BigNumber::new(1000000 * 1e8),
    ]);

    $newest = Wallet::factory()->create([
        'balance' => BigNumber::new(10 * 1e8),
        'address'    => 'newest-address',
        'public_key' => 'newest-public_key',
    ]);

    $newestTimestamp = Timestamp::fromUnix(Carbon::parse('2024-04-01 15:04:13')->unix())->unix();
    Transaction::factory()->transfer()->create([
        'block_height'      => 143,
        'sender_public_key' => $newest->public_key,
        'recipient_id'      => $newest->address,
        'timestamp'         => $newestTimestamp,
    ]);
    Transaction::factory()->transfer()->create([
        'block_height'      => 144,
        'sender_public_key' => $newest->public_key,
        'recipient_id'      => $newest->address,
        'timestamp'         => $newestTimestamp + 1,
    ]);

    $this->artisan('explorer:cache-address-statistics');

    expect($cache->getGenesisAddress())->toBe([
        'address' => $genesisWallet->address,
        'value'   => Carbon::createFromTimestamp(Network::epoch()->timestamp)->format(DateFormat::DATE),
    ]);

    expect($cache->getNewestAddress())->toBe([
        'address'   => $newest->address,
        'timestamp' => $newestTimestamp,
        'value'     => '01 Apr 2024',
    ]);

    expect($cache->getMostTransactions())->toBe([
        'address' => $newest->address,
        'value'   => 2,
    ]);

    expect($cache->getLargestAddress())->toBe([
        'address' => $largest->address,
        'value'   => $largest->balance->toFloat(),
    ]);

    Event::assertDispatchedTimes(AddressHoldings::class, 1);
    Event::assertDispatchedTimes(UniqueAddresses::class, 1);
});

it('should handle null scenarios for unique addresses', function () {
    Event::fake();

    $cache = new StatisticsCache();

    $this->artisan('explorer:cache-address-statistics');

    expect($cache->getGenesisAddress())->toBeNull();
    expect($cache->getNewestAddress())->toBeNull();
    expect($cache->getMostTransactions())->toBeNull();
    expect($cache->getLargestAddress())->toBeNull();

    Event::assertDispatchedTimes(AddressHoldings::class, 0);
    Event::assertDispatchedTimes(UniqueAddresses::class, 0);
});

it('should not dispatch events if nothing changed', function () {
    Event::fake();

    $genesisWallet = Wallet::factory()->create([
        'address'    => 'genesis-address',
        'public_key' => 'genesis-public_key',
    ]);
    Transaction::factory()->transfer()->create([
        'block_height'      => 1,
        'sender_public_key' => $genesisWallet->public_key,
        'recipient_id'      => $genesisWallet->address,
        'timestamp'         => Timestamp::fromUnix(Carbon::parse('2023-04-01 15:04:13')->unix())->unix(),
    ]);

    Wallet::factory()->create([
        'balance' => BigNumber::new(1000000 * 1e8),
    ]);

    $newest = Wallet::factory()->create([
        'balance' => BigNumber::new(10 * 1e8),
        'address'    => 'newest-address',
        'public_key' => 'newest-public_key',
    ]);

    $newestTimestamp = Timestamp::fromUnix(Carbon::parse('2024-04-01 15:04:13')->unix())->unix();
    Transaction::factory()->transfer()->create([
        'block_height'      => 143,
        'sender_public_key' => $newest->public_key,
        'recipient_id'      => $newest->address,
        'timestamp'         => $newestTimestamp,
    ]);
    Transaction::factory()->transfer()->create([
        'block_height'      => 144,
        'sender_public_key' => $newest->public_key,
        'recipient_id'      => $newest->address,
        'timestamp'         => $newestTimestamp + 1,
    ]);

    $this->artisan('explorer:cache-address-statistics');

    Event::assertDispatchedTimes(AddressHoldings::class, 1);
    Event::assertDispatchedTimes(UniqueAddresses::class, 1);

    Event::fake();

    $this->artisan('explorer:cache-address-statistics');

    Event::assertDispatchedTimes(AddressHoldings::class, 0);
    Event::assertDispatchedTimes(UniqueAddresses::class, 0);
});
