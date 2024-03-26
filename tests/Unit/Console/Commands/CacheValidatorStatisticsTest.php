<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Models\Block;
use App\Models\Round;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Cache\StatisticsCache;

it('should cache validator statistics', function () {
    $cache = new StatisticsCache();

    $mostVoters   = Wallet::factory()->activeValidator()->create();
    $leastVoters  = Wallet::factory()->activeValidator()->create();
    $oldestActive = Wallet::factory()->activeValidator()->create();
    $newestActive = Wallet::factory()->activeValidator()->create();
    $mostBlocks   = Wallet::factory()->activeValidator()->create();

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

    Transaction::factory()->validatorRegistration()->create([
        'timestamp'         => 1,
        'sender_public_key' => $oldestActive->public_key,
    ]);

    Transaction::factory()->validatorRegistration()->create([
        'timestamp'         => 100,
        'sender_public_key' => $newestActive->public_key,
    ]);

    Block::factory()->count(10)->create([
        'generator_public_key' => $mostBlocks->public_key,
    ]);

    Block::factory()->count(6)->create([
        'generator_public_key' => $newestActive->public_key,
    ]);

    $this->artisan('explorer:cache-validator-statistics');

    expect($cache->getMostUniqueVoters())->toBe($mostVoters->public_key);
    expect($cache->getLeastUniqueVoters())->toBe($leastVoters->public_key);
    expect($cache->getOldestActiveValidator())->toBe(['publicKey' => $oldestActive->public_key, 'timestamp' => Network::epoch()->timestamp + 1]);
    expect($cache->getNewestActiveValidator())->toBe(['publicKey' => $newestActive->public_key, 'timestamp' => Network::epoch()->timestamp + 100]);
    expect($cache->getMostBlocksForged())->toBe($mostBlocks->public_key);
});

it('should handle null scenarios for statistics', function () {
    $cache = new StatisticsCache();
    Round::factory()->create();

    $this->artisan('explorer:cache-validator-statistics');

    expect($cache->getMostUniqueVoters())->toBeNull();
    expect($cache->getLeastUniqueVoters())->toBeNull();
    expect($cache->getOldestActiveValidator())->toBeNull();
    expect($cache->getNewestActiveValidator())->toBeNull();
    expect($cache->getMostBlocksForged())->toBeNull();
});
