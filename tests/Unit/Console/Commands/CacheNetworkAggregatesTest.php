<?php

declare(strict_types=1);

use App\Console\Commands\CacheNetworkAggregates;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\Cache\NetworkCache;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

it('should execute the command', function () {
    $cache = new NetworkCache();
    $cache->setSupply(fn () => strval(100 * 1e18));

    (new CacheNetworkAggregates())->handle($cache);

    expect($cache->getVolume())->toBe(0.0);
    expect($cache->getTransactionsCount())->toBe(0);
    expect($cache->getVotesCount())->toBe(0);
    expect($cache->getVotesPercentage())->toBe(0.0);
    expect($cache->getValidatorRegistrationCount())->toBe(0);
    expect($cache->getFeesCollected())->toBe(0.0);
});

it('should execute the command with data', function () {
    $this->freezeTime();

    $cache = new NetworkCache();
    $cache->setSupply(fn () => strval(20000 * 1e18));

    Transaction::factory(10)
        ->create([
            'value'     => BigNumber::new(1000 * 1e18),
            'gas_price' => 12,
            'gas'       => 21000,
            'timestamp' => Carbon::now()->getTimestampMs(),
        ]);

    Transaction::factory(2)
        ->validatorRegistration()
        ->create([
            'value'     => BigNumber::new(25 * 1e18),
            'gas_price' => 12,
            'gas'       => 21000,
            'timestamp' => Carbon::now()->getTimestampMs(),
        ]);

    Wallet::factory(10)->create([
        'balance'          => BigNumber::new(200 * 1e18),
        'attributes->vote' => 'some_vote_value',
    ]);

    Block::factory(10)->create([
        'timestamp' => Carbon::now()->getTimestampMs(),
        'fee'       => BigNumber::new(12 * 1e18),
    ]);

    $fees = BigNumber::new(DB::connection('explorer')->table('blocks')->sum('fee'))->toFloat();

    (new CacheNetworkAggregates())->handle($cache);

    expect($cache->getVolume())->toBe(BigNumber::new(1000)->multipliedBy(10)->plus(BigNumber::new(25)->multipliedBy(2)->valueOf())->valueOf()->toFloat());
    expect($cache->getTransactionsCount())->toBe(12);
    expect($cache->getVotesCount())->toBe(BigNumber::new(200)->multipliedBy(10)->valueOf()->toInt());
    expect($cache->getVotesPercentage())->toBe(10.0);
    expect($cache->getValidatorRegistrationCount())->toBe(2);
    expect($cache->getFeesCollected())->toBe($fees);
});
