<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Addresses\Aggregates\LatestWalletAggregate;
use App\Services\Cache\StatisticsCache;
use App\Services\Timestamp;
use Carbon\Carbon;

it('should refresh the latest wallet', function () {
    $cache = new StatisticsCache();

    expect($cache->getNewestAddress())->toBeNull();

    $genesisWallet = Wallet::factory()->create();
    $newestWallet  = Wallet::factory()->create();

    $genesisTimestamp = Timestamp::fromUnix(Carbon::parse('2021-01-01 13:24:44')->unix())->unix();

    Transaction::factory()->transfer()->create([
        'sender_public_key' => $genesisWallet->public_key,
        'recipient_id'      => $newestWallet->address,
        'timestamp'         => $genesisTimestamp,
    ]);

    (new LatestWalletAggregate())->aggregate();

    expect($cache->getNewestAddress())->toBe([
        'address'   => $newestWallet->address,
        'timestamp' => $genesisTimestamp,
        'value'     => '01 Jan 2021',
    ]);

    $newestTimestamp = Timestamp::fromUnix(Carbon::parse('2021-01-01 14:24:44')->unix())->unix();

    Transaction::factory()->transfer()->create([
        'sender_public_key' => $newestWallet->public_key,
        'recipient_id'      => $newestWallet->address,
        'timestamp'         => $newestTimestamp,
    ]);

    (new LatestWalletAggregate())->aggregate();

    expect($cache->getNewestAddress())->toBe([
        'address'   => $newestWallet->address,
        'timestamp' => $genesisTimestamp,
        'value'     => '01 Jan 2021',
    ]);
});
