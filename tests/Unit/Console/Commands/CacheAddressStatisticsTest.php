<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\Cache\StatisticsCache;
use ARKEcosystem\Foundation\UserInterface\Support\DateFormat;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

it('should cache address holdings', function () {
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
});

it('should cache unique addresses', function () {
    $cache = new StatisticsCache();

    $newestWallet1 = Wallet::factory()->create([
        'address'    => 'address1',
        'updated_at' => 153,
    ]);
    $transactionTimestamp = Carbon::parse('2024-03-03 13:24:44')->getTimestampMs();
    $transaction          = Transaction::factory()->create([
        'timestamp' => $transactionTimestamp,
        'sender_public_key' => $newestWallet1->public_key,
        'recipient_id' => $newestWallet1->address,
    ]);

    $largest = Wallet::factory()->create([
        'balance' => BigNumber::new(1000000 * 1e8),
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
        'address' => $transaction->sender->address,
        'value'   => 1,
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
        'timestamp' => $newestTransactionTimestamp,
        'sender_public_key' => $newestWallet2->public_key,
        'recipient_id' => $newestWallet2->address,
    ]);

    $this->artisan('explorer:cache-address-statistics');

    expect($cache->getNewestAddress())->toBe([
        'address'   => $newestTransaction->sender->address,
        'timestamp' => $newestTransactionTimestamp,
        'value'     => Carbon::parse('2024-03-04 13:24:44')->format(DateFormat::DATE),
    ]);
});

it('should handle null scenarios for unique addresses', function () {
    $cache = new StatisticsCache();

    $this->artisan('explorer:cache-address-statistics');

    expect($cache->getGenesisAddress())->toBeNull();
    expect($cache->getNewestAddress())->toBeNull();
    expect($cache->getMostTransactions())->toBeNull();
    expect($cache->getLargestAddress())->toBeNull();
});

it('should cache newest address only since last run', function () {
    $cache = new StatisticsCache();

    $wallet1 = Wallet::factory()->create([
        'address'    => 'address1',
        'updated_at' => 0,
    ]);

    Transaction::factory()->create([
        'recipient_id'      => $wallet1->address,
        'sender_public_key' => $wallet1->public_key,
    ]);

    $this->freezeTime();
    $this->travelTo(Carbon::parse('2024-04-17 13:23:44'));

    expect(Cache::get('commands:cache_address_statistics/last_run'))->toBeNull();

    $this->artisan('explorer:cache-address-statistics');

    expect(Cache::get('commands:cache_address_statistics/last_run'))->toEqual($wallet1->updated_at);

    expect($cache->getNewestAddress()['address'])->toBe($wallet1->address);

    $wallet2 = Wallet::factory()->create([
        'address'    => 'address2',
        'updated_at' => 153,
    ]);

    Transaction::factory()->create([
        'recipient_id'      => $wallet2->address,
        'sender_public_key' => $wallet2->public_key,
        'timestamp'         => Carbon::parse('2024-04-18 13:23:44')->getTimestampMs(),
    ]);

    $this->artisan('explorer:cache-address-statistics');

    expect(Cache::get('commands:cache_address_statistics/last_run'))->toEqual(153);

    expect($cache->getNewestAddress()['address'])->toBe($wallet2->address);

    $wallet3 = Wallet::factory()->create([
        'address'    => 'address3',
        'updated_at' => 13, // prior to `last_run` value
    ]);

    Transaction::factory()->create([
        'recipient_id'      => $wallet3->address,
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
        'recipient_id'      => $wallet4->address,
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
        'recipient_id'      => $wallet5->address,
        'sender_public_key' => $wallet5->public_key,
        'timestamp'         => Carbon::parse('2024-04-20 13:23:44')->getTimestampMs(),
    ]);

    $this->artisan('explorer:cache-address-statistics');

    expect($cache->getNewestAddress()['address'])->toBe($wallet5->address);
});
