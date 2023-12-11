<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\Cache\Statistics;
use ARKEcosystem\Foundation\UserInterface\Support\DateFormat;
use Carbon\Carbon;

it('should cache address holdings', function () {
    $cache = new Statistics();

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
    $cache = new Statistics();

    $transaction = Transaction::factory()->create();

    $largest = Wallet::factory()->create([
        'balance' => BigNumber::new(1000000 * 1e8),
    ]);

    $this->artisan('explorer:cache-address-statistics');

    expect($cache->getGenesisAddress())->toBe(['address' => $transaction->sender->address, 'value' => Carbon::createFromTimestamp(Network::epoch()->timestamp)->format(DateFormat::DATE)]);
    //expect($cache->getNewestAddress())->toBe(['address' => $wallet->address, 'value' => '0']); // TODO: handle once implemented
    expect($cache->getMostTransactions())->toBe(['address' => $transaction->sender->address, 'value' => 1]);
    expect($cache->getLargestAddress())->toBe(['address' => $largest->address, 'value' => $largest->balance->toFloat()]);
});

it('should handle null scenarios for unique addresses', function () {
    $cache = new Statistics();

    $this->artisan('explorer:cache-address-statistics');

    expect($cache->getGenesisAddress())->toBeNull();
    expect($cache->getNewestAddress())->toBeNull();
    expect($cache->getMostTransactions())->toBeNull();
    expect($cache->getLargestAddress())->toBeNull();
});
