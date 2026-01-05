<?php

declare(strict_types=1);

use App\Events\Statistics\AddressHoldings;
use App\Events\Statistics\UniqueAddresses;
use App\Facades\Network;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\Cache\StatisticsCache;
use App\Services\Timestamp;
use ARKEcosystem\Foundation\UserInterface\Support\DateFormat;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;

it('should cache address holdings', function () {
    Event::fake();

    $cache = new StatisticsCache();

    Wallet::factory()->create([
        'balance' => 1.1 * 1e18,
    ]);
    Wallet::factory()->count(1)->create([
        'balance' => 1 * 1e18,
    ]);
    Wallet::factory()->count(4)->create([
        'balance' => 0.9 * 1e18,
    ]);

    $this->artisan('explorer:cache-address-statistics');

    expect($cache->getAddressHoldings())->toBe([
        ['grouped' => 0, 'count' => 5],
        ['grouped' => 1, 'count' => 1],
    ]);

    Wallet::factory()->count(5)->create([
        'balance' => 10.1 * 1e18,
    ]);
    Wallet::factory()->count(3)->create([
        'balance' => 1000.1 * 1e18,
    ]);
    Wallet::factory()->count(2)->create([
        'balance' => BigNumber::new(1000000.1 * 1e18),
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

    $newestWallet1 = Wallet::factory()->create([
        'address'    => 'address1',
        'updated_at' => 153,
    ]);
    $transactionTimestamp = Carbon::parse('2024-03-03 13:24:44')->getTimestampMs();
    $transaction          = Transaction::factory()->create([
        'block_number'      => 1,
        'timestamp'         => $transactionTimestamp,
        'sender_public_key' => $newestWallet1->public_key,
        'to'                => $newestWallet1->address,
    ]);

    $largest = Wallet::factory()->create([
        'balance' => BigNumber::new(1000000 * 1e18),
    ]);

    $newest = Wallet::factory()->create([
        'balance'    => BigNumber::new(10 * 1e18),
        'address'    => 'newest-address',
        'public_key' => 'newest-public_key',
    ]);

    $newestTimestamp = Timestamp::fromUnix(Carbon::parse('2024-04-01 15:04:13')->unix())->unix();
    Transaction::factory()->transfer()->create([
        'block_number'      => 143,
        'sender_public_key' => $newest->public_key,
        'to'                => $newest->address,
        'timestamp'         => $newestTimestamp,
    ]);
    Transaction::factory()->transfer()->create([
        'block_number'      => 144,
        'sender_public_key' => $newest->public_key,
        'to'                => $newest->address,
        'timestamp'         => $newestTimestamp + 1,
    ]);

    $this->artisan('explorer:cache-address-statistics');

    expect($cache->getGenesisAddress())->toBe([
        'address' => $transaction->sender->address,
        'value'   => Carbon::createFromTimestamp(Network::epoch()->timestamp)->format(DateFormat::DATE),
    ]);

    expect($cache->getNewestAddress())->toBe([
        'address'   => $transaction->sender->address,
        'timestamp' => $transactionTimestamp,
        'value'     => Carbon::parse('2024-03-03 13:24:44')->format(DateFormat::DATE),
    ]);

    expect($cache->getMostTransactions())->toBe([
        'address' => $newest->address,
        'value'   => 2,
    ]);

    expect($cache->getLargestAddress())->toBe([
        'address' => $largest->address,
        'value'   => $largest->balance->toFloat(),
    ]);

    $newestWallet2 = Wallet::factory()->create([
        'address'    => 'address2',
        'updated_at' => 154,
    ]);
    $newestTransactionTimestamp = Carbon::parse('2024-03-04 13:24:44')->getTimestampMs();
    $newestTransaction          = Transaction::factory()->create([
        'timestamp'         => $newestTransactionTimestamp,
        'sender_public_key' => $newestWallet2->public_key,
        'to'                => $newestWallet2->address,
    ]);

    $this->artisan('explorer:cache-address-statistics');

    expect($cache->getNewestAddress())->toBe([
        'address'   => $newestTransaction->sender->address,
        'timestamp' => $newestTransactionTimestamp,
        'value'     => Carbon::parse('2024-03-04 13:24:44')->format(DateFormat::DATE),
    ]);
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

it('should dispatch event if most transactions has changed', function () {
    // Set a high "last_updated_at_height" so the LatestWalletAggregate query
    // requires updated_at > this large number. We'll ensure all wallets have
    // updated_at below that, forcing the query to return null which prevents
    // marking the cache as having changes early and causing a flaky test.
    Cache::put(
        'commands:cache_address_statistics/last_updated_at_height',
        999999999
    );

    Event::fake();

    $genesisWallet = Wallet::factory()->create([
        'address'    => 'genesis-address',
        'public_key' => 'genesis_public_key',
        'updated_at' => 999999998,
    ]);

    Transaction::factory()->transfer()->create([
        'block_number'      => 1,
        'sender_public_key' => $genesisWallet->public_key,
        'to'                => $genesisWallet->address,
        'timestamp'         => Timestamp::fromUnix(Carbon::parse('2023-04-01 15:04:13')->unix())->unix(),
    ]);

    Wallet::factory()->create([
        'balance'    => BigNumber::new(1000000 * 1e18),
        'updated_at' => 999999998,
    ]);

    $newest = Wallet::factory()->create([
        'balance'    => BigNumber::new(10 * 1e18),
        'address'    => 'newest-address',
        'public_key' => 'newest-public_key',
        'updated_at' => 999999998,
    ]);

    $newestTimestamp = Timestamp::fromUnix(Carbon::parse('2024-04-01 15:04:13')->unix())->unix();

    Transaction::factory()->transfer()->create([
        'block_number'      => 143,
        'sender_public_key' => $newest->public_key,
        'to'                => $newest->address,
        'timestamp'         => $newestTimestamp,
    ]);
    Transaction::factory()->transfer()->create([
        'block_number'      => 144,
        'sender_public_key' => $newest->public_key,
        'to'                => $newest->address,
        'timestamp'         => $newestTimestamp + 1,
    ]);

    $this->artisan('explorer:cache-address-statistics');

    Event::assertDispatchedTimes(AddressHoldings::class, 1);
    Event::assertDispatchedTimes(UniqueAddresses::class, 1);

    Event::fake();

    Transaction::factory()->transfer()->create([
        'block_number'      => 145,
        'sender_public_key' => $newest->public_key,
        'to'                => $newest->address,
        'timestamp'         => $newestTimestamp + 2,
    ]);

    $this->artisan('explorer:cache-address-statistics');

    Event::assertDispatchedTimes(AddressHoldings::class, 1);
    Event::assertDispatchedTimes(UniqueAddresses::class, 1);

    Event::fake();

    $mostTransactionsWallet = Wallet::factory()->create([
        'balance'    => BigNumber::new(10 * 1e18),
        'address'    => 'most-transactions_address',
        'public_key' => 'most-transactions_public_key',
        'updated_at' => 999999998,
    ]);

    Transaction::factory(5)->transfer()->create([
        'block_number'      => 146,
        'sender_public_key' => $mostTransactionsWallet->public_key,
        'to'                => $mostTransactionsWallet->address,
        'timestamp'         => $newestTimestamp,
    ]);

    $this->artisan('explorer:cache-address-statistics');

    Event::assertDispatchedTimes(AddressHoldings::class, 1);
    Event::assertDispatchedTimes(UniqueAddresses::class, 1);
});

it('should should dispatch event if largest has changed', function () {
    Event::fake();

    $genesisWallet = Wallet::factory()->create([
        'address'    => 'genesis-address',
        'public_key' => 'genesis-public_key',
    ]);
    Transaction::factory()->transfer()->create([
        'block_number'      => 1,
        'sender_public_key' => $genesisWallet->public_key,
        'to'                => $genesisWallet->address,
        'timestamp'         => Timestamp::fromUnix(Carbon::parse('2023-04-01 15:04:13')->unix())->unix(),
    ]);

    $largest = Wallet::factory()->create([
        'balance' => BigNumber::new(1000000 * 1e18),
    ]);

    $newest = Wallet::factory()->create([
        'balance'    => BigNumber::new(10 * 1e18),
        'address'    => 'newest-address',
        'public_key' => 'newest-public_key',
    ]);

    $newestTimestamp = Timestamp::fromUnix(Carbon::parse('2024-04-01 15:04:13')->unix())->unix();
    Transaction::factory()->transfer()->create([
        'block_number'      => 143,
        'sender_public_key' => $newest->public_key,
        'to'                => $newest->address,
        'timestamp'         => $newestTimestamp,
    ]);
    Transaction::factory()->transfer()->create([
        'block_number'      => 144,
        'sender_public_key' => $newest->public_key,
        'to'                => $newest->address,
        'timestamp'         => $newestTimestamp + 1,
    ]);

    $this->artisan('explorer:cache-address-statistics');

    Event::assertDispatchedTimes(AddressHoldings::class, 1);
    Event::assertDispatchedTimes(UniqueAddresses::class, 1);

    Event::fake();

    $largest->balance = BigNumber::new(2000000 * 1e18);
    $largest->save();

    $this->artisan('explorer:cache-address-statistics');

    Event::assertDispatchedTimes(AddressHoldings::class, 1);
    Event::assertDispatchedTimes(UniqueAddresses::class, 1);

    Event::fake();

    Wallet::factory()->create([
        'balance' => BigNumber::new(4000000 * 1e18),
    ]);

    $this->artisan('explorer:cache-address-statistics');

    Event::assertDispatchedTimes(AddressHoldings::class, 1);
    Event::assertDispatchedTimes(UniqueAddresses::class, 1);
});

it('should not dispatch events if nothing changed', function () {
    Event::fake();

    $genesisWallet = Wallet::factory()->create([
        'address'    => 'genesis-address',
        'public_key' => 'genesis-public_key',
    ]);
    Transaction::factory()->transfer()->create([
        'block_number'      => 1,
        'sender_public_key' => $genesisWallet->public_key,
        'to'                => $genesisWallet->address,
        'timestamp'         => Timestamp::fromUnix(Carbon::parse('2023-04-01 15:04:13')->unix())->unix(),
    ]);

    Wallet::factory()->create([
        'balance' => BigNumber::new(1000000 * 1e18),
    ]);

    $newest = Wallet::factory()->create([
        'balance'    => BigNumber::new(10 * 1e18),
        'address'    => 'newest-address',
        'public_key' => 'newest-public_key',
    ]);

    $newestTimestamp = Timestamp::fromUnix(Carbon::parse('2024-04-01 15:04:13')->unix())->unix();
    Transaction::factory()->transfer()->create([
        'block_number'      => 143,
        'sender_public_key' => $newest->public_key,
        'to'                => $newest->address,
        'timestamp'         => $newestTimestamp,
    ]);
    Transaction::factory()->transfer()->create([
        'block_number'      => 144,
        'sender_public_key' => $newest->public_key,
        'to'                => $newest->address,
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

it('should cache newest address only since last run', function () {
    $cache = new StatisticsCache();

    $wallet1 = Wallet::factory()->create([
        'address'    => 'address1',
        'updated_at' => 0,
    ]);

    Transaction::factory()->create([
        'to'                => $wallet1->address,
        'sender_public_key' => $wallet1->public_key,
    ]);

    $this->freezeTime();
    $this->travelTo(Carbon::parse('2024-04-17 13:23:44'));

    expect(Cache::get('commands:cache_address_statistics/last_updated_at_height'))->toBeNull();

    $this->artisan('explorer:cache-address-statistics');

    expect(Cache::get('commands:cache_address_statistics/last_updated_at_height'))->toEqual($wallet1->updated_at);

    expect($cache->getNewestAddress()['address'])->toBe($wallet1->address);

    $wallet2 = Wallet::factory()->create([
        'address'    => 'address2',
        'updated_at' => 153,
    ]);

    Transaction::factory()->create([
        'to'                => $wallet2->address,
        'sender_public_key' => $wallet2->public_key,
        'timestamp'         => Carbon::parse('2024-04-18 13:23:44')->getTimestampMs(),
    ]);

    $this->artisan('explorer:cache-address-statistics');

    expect(Cache::get('commands:cache_address_statistics/last_updated_at_height'))->toEqual(153);

    expect($cache->getNewestAddress()['address'])->toBe($wallet2->address);

    $wallet3 = Wallet::factory()->create([
        'address'    => 'address3',
        'updated_at' => 13, // prior to `last_updated_at_height` value
    ]);

    Transaction::factory()->create([
        'to'                => $wallet3->address,
        'sender_public_key' => $wallet3->public_key,
        'timestamp'         => Carbon::parse('2024-04-20 13:23:44')->getTimestampMs(),
    ]);

    $this->artisan('explorer:cache-address-statistics');

    expect($cache->getNewestAddress()['address'])->toBe($wallet2->address);

    $wallet4 = Wallet::factory()->create([
        'address'    => 'address4',
        'updated_at' => 200,
    ]);

    Transaction::factory()->create([
        'to'                => $wallet4->address,
        'sender_public_key' => $wallet4->public_key,
        'timestamp'         => Carbon::parse('2024-04-15 13:23:44')->getTimestampMs(), // prior to most recent wallet's timestamp
    ]);

    $this->artisan('explorer:cache-address-statistics');

    expect($cache->getNewestAddress()['address'])->toBe($wallet2->address);

    $wallet5 = Wallet::factory()->create([
        'address'    => 'address5',
        'updated_at' => 200,
    ]);

    Transaction::factory()->create([
        'to'                => $wallet5->address,
        'sender_public_key' => $wallet5->public_key,
        'timestamp'         => Carbon::parse('2024-04-20 13:23:44')->getTimestampMs(),
    ]);

    $this->artisan('explorer:cache-address-statistics');

    expect($cache->getNewestAddress()['address'])->toBe($wallet5->address);
});

it('should cache wallet with most transactions', function () {
    $walletWithMostTransactions = Wallet::factory()->create();

    $otherWallet = Wallet::factory()->create();

    Transaction::factory()
        ->transfer()
        ->count(3)
        ->create([
            'sender_public_key' => $walletWithMostTransactions->public_key,
        ]);

    Transaction::factory()
        ->transfer()
        ->count(9)
        ->create([
            'to' => $otherWallet->address,
        ]);

    Transaction::factory()
        ->transfer()
        ->count(2)
        ->create([
            'to' => $walletWithMostTransactions->address,
        ]);

    Transaction::factory()
        ->multiPayment(
            [$walletWithMostTransactions->address],
            [BigNumber::new(10 * 1e18)],
        )
        ->count(5)
        ->create();

    $this->artisan('explorer:cache-address-statistics');

    expect((new StatisticsCache())->getMostTransactions())->toBe([
        'address' => $walletWithMostTransactions->address,
        'value'   => 10,
    ]);
});
