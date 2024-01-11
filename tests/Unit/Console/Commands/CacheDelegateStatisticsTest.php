<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Models\Block;
use App\Models\Round;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Cache\StatisticsCache;

it('should cache delegate statistics', function () {
    $cache = new StatisticsCache();

    $mostVoters   = Wallet::factory()->activeDelegate()->create();
    $leastVoters  = Wallet::factory()->activeDelegate()->create();
    $oldestActive = Wallet::factory()->activeDelegate()->create();
    $newestActive = Wallet::factory()->activeDelegate()->create();
    $mostBlocks   = Wallet::factory()->activeDelegate()->create();

    Round::factory()->create([
        'round'      => 1,
        'public_key' => $oldestActive->public_key,
    ]);
    Round::factory()->create([
        'round'      => 1,
        'public_key' => $newestActive->public_key,
    ]);

    Wallet::factory()->count(5)->create([
        'attributes' => ['vote' => $mostVoters->public_key],
    ]);

    Wallet::factory()->count(1)->create([
        'attributes' => ['vote' => $leastVoters->public_key],
    ]);

    Transaction::factory()->delegateRegistration()->create([
        'timestamp'         => 1,
        'sender_public_key' => $oldestActive->public_key,
    ]);

    Transaction::factory()->delegateRegistration()->create([
        'timestamp'         => 100,
        'sender_public_key' => $newestActive->public_key,
    ]);

    Block::factory()->count(10)->create([
        'generator_public_key' => $mostBlocks->public_key,
    ]);

    Block::factory()->count(6)->create([
        'generator_public_key' => $newestActive->public_key,
    ]);

    $this->artisan('explorer:cache-delegate-statistics');

    expect($cache->getMostUniqueVoters())->toBe($mostVoters->public_key);
    expect($cache->getLeastUniqueVoters())->toBe($leastVoters->public_key);
    expect($cache->getOldestActiveDelegate())->toBe(['publicKey' => $oldestActive->public_key, 'timestamp' => Network::epoch()->timestamp + 1]);
    expect($cache->getNewestActiveDelegate())->toBe(['publicKey' => $newestActive->public_key, 'timestamp' => Network::epoch()->timestamp + 100]);
    expect($cache->getMostBlocksForged())->toBe($mostBlocks->public_key);
});

it('should handle null scenarios for statistics', function () {
    $cache = new StatisticsCache();
    Round::factory()->create();

    $this->artisan('explorer:cache-delegate-statistics');

    expect($cache->getMostUniqueVoters())->toBeNull();
    expect($cache->getLeastUniqueVoters())->toBeNull();
    expect($cache->getOldestActiveDelegate())->toBeNull();
    expect($cache->getNewestActiveDelegate())->toBeNull();
    expect($cache->getMostBlocksForged())->toBeNull();
});
