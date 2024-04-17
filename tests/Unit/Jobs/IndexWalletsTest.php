<?php

declare(strict_types=1);

use App\Jobs\IndexWallets;
use App\Models\Wallet;
use Carbon\Carbon;
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
        ->with('latest-indexed-timestamp:wallets', 1000)
        ->once();

    // New Wallet
    $newWallet = Wallet::factory()->create([
        'updated_at' => 1000,
    ]);

    // Latest indexed wallet timestamp
    Wallet::factory()->create([
        'updated_at' => 500,
    ]);

    // Old Wallet
    Wallet::factory()->create([
        'updated_at' => 100,
    ]);

    $url = sprintf(
        '%s/indexes/%s/search',
        config('scout.meilisearch.host'),
        'wallets'
    );

    Http::fake([
        $url => Http::response([
            'hits' => [
                ['timestamp' => 500],
            ],
        ]),
    ]);

    IndexWallets::dispatch();

    Event::assertDispatched(ModelsImported::class, function ($event) use ($newWallet) {
        return $event->models->count() === 1
            && $event->models->first()->is($newWallet);
    });
});

it('should not store any value on cache if no new wallets', function () {
    Event::fake();

    Cache::shouldReceive('get')
        ->with('latest-indexed-timestamp:wallets')
        ->andReturn(600);

    Wallet::factory()->create([
        'updated_at' => 500,
    ]);

    IndexWallets::dispatch();

    Event::assertNotDispatched(ModelsImported::class);
});

it('should index new wallets using the timestamp from cache', function () {
    Event::fake();

    Cache::shouldReceive('get')
        ->with('latest-indexed-timestamp:wallets')
        ->andReturn(200) // so new ones are the one with updated_at (timestamp) 500 and 1000
        ->shouldReceive('put')
        ->with('latest-indexed-timestamp:wallets', 1000)
        ->once();

    // New Wallet
    Wallet::factory()->create([
        'updated_at' => 1000,
    ]);

    // Relatively new Wallet
    Wallet::factory()->create([
        'updated_at' => 500,
    ]);

    // Old Wallet
    Wallet::factory()->create([
        'updated_at' => 100,
    ]);

    IndexWallets::dispatch();

    Event::assertDispatched(
        ModelsImported::class,
        fn ($event) => $event->models->count() === 2
        && $event->models->pluck('timestamp')->sort()->values()->toArray() === [500, 1000]
    );
});

it('should not index anything if meilisearch is empty', function () {
    Event::fake();

    Wallet::factory()->create();

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
