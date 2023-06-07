<?php

declare(strict_types=1);

use App\Jobs\IndexWallets;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Laravel\Scout\Events\ModelsImported;
use Meilisearch\Client as MeilisearchClient;
use Meilisearch\Endpoints\Indexes;

beforeEach(function () {
    // Default value, overriden in phpunit.xml for the tests
    Config::set('scout.driver', 'meilisearch');

    // Mock the Meilisearch client and indexes
    $mock    = $this->mock(MeilisearchClient::class);
    $indexes = $this->mock(Indexes::class);
    $mock->shouldReceive('index')->andReturn($indexes);
    $indexes->shouldReceive('addDocuments');
});

it('should index new Wallets', function () {
    Event::fake();

    Cache::shouldReceive('get')
        ->with('latest-indexed-timestamp:wallets')
        ->andReturn(null)
        ->shouldReceive('put')
        ->with('latest-indexed-timestamp:wallets', 10)
        ->once();

    $lastIndexedWallet = Wallet::factory()->create();
    Transaction::factory()->create([
        'recipient_id' => $lastIndexedWallet->address,
        'timestamp'    => 5,
    ]);

    $newWallet = Wallet::factory()->create();
    Transaction::factory()->create([
        'recipient_id' => $newWallet->address,
        'timestamp'    => 10,
    ]);

    $oldWallet = Wallet::factory()->create();
    Transaction::factory()->create([
        'recipient_id' => $oldWallet->address,
        'timestamp'    => 1,
    ]);

    $url = sprintf(
        '%s/indexes/%s/search',
        config('scout.meilisearch.host'),
        'wallets'
    );

    Http::fake([
        $url => Http::response([
            'hits' => [
                ['timestamp' => 5],
            ],
        ]),
    ]);

    IndexWallets::dispatch();

    Event::assertDispatched(ModelsImported::class, function ($event) use ($newWallet) {
        return $event->models->count() === 1
            && $event->models->first()->is($newWallet);
    });
});

it('should index new Wallets using the timestamp from cache', function () {
    Event::fake();

    Cache::shouldReceive('get')
        ->with('latest-indexed-timestamp:wallets')
        ->andReturn(2) // so new ones are the one with timestamp 5 and 10
        ->shouldReceive('put')
        ->with('latest-indexed-timestamp:wallets', 10)
        ->once();

    $newWallet = Wallet::factory()->create();
    Transaction::factory()->create([
        'recipient_id' => $newWallet->address,
        'timestamp'    => 10,
    ]);

    $relativelyNewWallet = Wallet::factory()->create();
    Transaction::factory()->create([
        'recipient_id' => $relativelyNewWallet->address,
        'timestamp'    => 5,
    ]);

    $oldWallet = Wallet::factory()->create();
    Transaction::factory()->create([
        'recipient_id' => $oldWallet->address,
        'timestamp'    => 1,
    ]);

    IndexWallets::dispatch();

    Event::assertDispatched(ModelsImported::class, function ($event) use ($newWallet) {
        return $event->models->count() === 2;
    });
});

it('should not index anything if meilisearch is empty', function () {
    Event::fake();

    $lastIndexedWallet = Wallet::factory()->create();
    Transaction::factory()->create([
        'recipient_id' => $lastIndexedWallet->address,
        'timestamp'    => 5,
    ]);

    $newWallet = Wallet::factory()->create();
    Transaction::factory()->create([
        'recipient_id' => $newWallet->address,
        'timestamp'    => 10,
    ]);

    $oldWallet = Wallet::factory()->create();
    Transaction::factory()->create([
        'recipient_id' => $oldWallet->address,
        'timestamp'    => 1,
    ]);

    $url = sprintf(
        '%s/indexes/%s/search',
        config('scout.meilisearch.host'),
        'wallets'
    );

    Http::fake([
        $url => Http::response([
            // Empty results
            'hits' => [],
        ]),
    ]);

    IndexWallets::dispatch();

    Event::assertNotDispatched(ModelsImported::class);
});
