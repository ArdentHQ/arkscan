<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Models\Block;
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

    $transactionTimestamp = Carbon::parse('2024-03-03 13:24:44')->getTimestampMs();
    $transaction          = Transaction::factory()->create([
        'timestamp' => $transactionTimestamp,
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

    $newestTransactionTimestamp = Carbon::parse('2024-03-04 13:24:44')->getTimestampMs();
    $newestTransaction          = Transaction::factory()->create([
        'timestamp' => $newestTransactionTimestamp,
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
        'address' => 'address1',
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

    expect(Cache::get('commands:cache_address_statistics/last_run'))->toEqual(Carbon::parse('2024-04-17 13:23:44'));

    expect($cache->getNewestAddress()['address'])->toBe($wallet1->address);

    $block = Block::factory()->create([
        'generator_public_key' => $wallet1->public_key,
        'height' => 153,
        'timestamp' => Carbon::parse('2024-02-17 13:23:44')->getTimestampMs(),
    ]);

    $wallet2 = Wallet::factory()->create([
        'address' => 'address2',
        'updated_at' => 153,
    ]);

    // Transaction which occurs in the future, but it isn't used to check the cache `last_run` value
    Transaction::factory()->create([
        'recipient_id'      => $wallet2->address,
        'sender_public_key' => $wallet2->public_key,
        'timestamp'         => Carbon::parse('2024-04-18 13:23:44')->getTimestampMs(),
    ]);

    $this->artisan('explorer:cache-address-statistics');

    expect($cache->getNewestAddress()['address'])->toBe($wallet1->address);

    // Change block to have a timestamp after the `last_run` value
    $block->timestamp = Carbon::parse('2024-04-18 13:23:44')->getTimestampMs();
    $block->save();

    $this->artisan('explorer:cache-address-statistics');

    expect($cache->getNewestAddress()['address'])->toBe($wallet2->address);
});
