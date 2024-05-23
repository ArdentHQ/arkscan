<?php

declare(strict_types=1);

use App\Events\Statistics\DelegateDetails;
use App\Facades\Network;
use App\Models\Block;
use App\Models\Round;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Cache\StatisticsCache;
use Illuminate\Support\Facades\Event;

it('should cache delegate statistics', function () {
    Event::fake();

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

    Event::assertDispatchedTimes(DelegateDetails::class, 1);
});

it('should handle null scenarios for statistics', function () {
    Event::fake();

    $cache = new StatisticsCache();
    Round::factory()->create();

    $this->artisan('explorer:cache-delegate-statistics');

    expect($cache->getMostUniqueVoters())->toBeNull();
    expect($cache->getLeastUniqueVoters())->toBeNull();
    expect($cache->getOldestActiveDelegate())->toBeNull();
    expect($cache->getNewestActiveDelegate())->toBeNull();
    expect($cache->getMostBlocksForged())->toBeNull();

    Event::assertDispatchedTimes(DelegateDetails::class, 0);
});

it('should dispatch event if most unique votes changed', function () {
    Event::fake();

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

    Event::assertDispatchedTimes(DelegateDetails::class, 1);

    Event::fake();

    Wallet::factory()->count(5)->create([
        'attributes' => ['vote' => $mostVoters->public_key],
    ]);

    $this->artisan('explorer:cache-delegate-statistics');

    Event::assertDispatchedTimes(DelegateDetails::class, 1);

    Event::fake();

    $newMostVoters = Wallet::factory()->activeDelegate()->create();
    Wallet::factory()->count(20)->create([
        'attributes' => ['vote' => $newMostVoters->public_key],
    ]);

    $this->artisan('explorer:cache-delegate-statistics');

    Event::assertDispatchedTimes(DelegateDetails::class, 1);
});

it('should dispatch event if least unique votes changed', function () {
    Event::fake();

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

    Event::assertDispatchedTimes(DelegateDetails::class, 1);

    Event::fake();

    Wallet::factory()->count(1)->create([
        'attributes' => ['vote' => $leastVoters->public_key],
    ]);

    $this->artisan('explorer:cache-delegate-statistics');

    Event::assertDispatchedTimes(DelegateDetails::class, 1);

    Event::fake();

    $newLeastVoters = Wallet::factory()->activeDelegate()->create();

    Wallet::factory()->count(1)->create([
        'attributes' => ['vote' => $newLeastVoters->public_key],
    ]);

    $this->artisan('explorer:cache-delegate-statistics');

    Event::assertDispatchedTimes(DelegateDetails::class, 1);
});

it('should dispatch event if newest active delegate changed', function () {
    Event::fake();

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

    Event::assertDispatchedTimes(DelegateDetails::class, 1);

    Event::fake();

    $newestActive = Wallet::factory()->activeDelegate()->create();
    Round::factory()->create([
        'round'      => 2,
        'public_key' => $newestActive->public_key,
    ]);
    Transaction::factory()->delegateRegistration()->create([
        'timestamp'         => 1000,
        'sender_public_key' => $newestActive->public_key,
    ]);

    $this->artisan('explorer:cache-delegate-statistics');

    Event::assertDispatchedTimes(DelegateDetails::class, 1);
});

it('should dispatch event if delegate with most blocks forged changed', function () {
    Event::fake();

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

    Event::assertDispatchedTimes(DelegateDetails::class, 1);

    Event::fake();

    $newMostBlocks = Wallet::factory()->activeDelegate()->create();
    Block::factory()->count(15)->create([
        'generator_public_key' => $newMostBlocks->public_key,
    ]);

    $this->artisan('explorer:cache-delegate-statistics');

    Event::assertDispatchedTimes(DelegateDetails::class, 1);
});
