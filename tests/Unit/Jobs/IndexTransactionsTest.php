<?php

declare(strict_types=1);

use App\Jobs\IndexTransactions;
use App\Models\Transaction;
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

it('should index new Transactions', function () {
    $this->travelTo(Carbon::parse('2024-04-09 13:32:44'));

    Event::fake();

    Cache::shouldReceive('get')
        ->with('latest-indexed-timestamp:transactions')
        ->andReturn(null)
        ->shouldReceive('put')
        ->with('latest-indexed-timestamp:transactions', 1712583164)
        ->once();

    $lastIndexedTransaction = Transaction::factory()->create([
        'timestamp' => 1712237564000, // 5 days
    ]);

    $newTransaction = Transaction::factory()->create([
        'timestamp' => 1711805564000, // 10 days
    ]);

    $oldTransaction = Transaction::factory()->create([
        'timestamp' => 1712583164000, // 1 day
    ]);

    $url = sprintf(
        '%s/indexes/%s/search',
        config('scout.meilisearch.host'),
        'transactions'
    );

    Http::fake([
        $url => Http::response([
            'hits' => [
                $lastIndexedTransaction->toSearchableArray(),
            ],
        ]),
    ]);

    IndexTransactions::dispatch();

    Event::assertDispatched(ModelsImported::class, function ($event) use ($newTransaction) {
        return $event->models->count() === 3 &&
            $event->models->pluck('timestamp')->sort()->values()->toArray() === [
                Carbon::now()->subDays(10)->unix(),
                Carbon::now()->subDays(5)->unix(),
                Carbon::now()->subDays(1)->unix(),
            ];
    });
});

it('should not store any value on cache if no new transactions', function () {
    Event::fake();

    Cache::shouldReceive('get')
        ->with('latest-indexed-timestamp:transactions')
        ->andReturn(6);

    Transaction::factory()->create([
        'timestamp' => 5,
    ]);

    IndexTransactions::dispatch();

    Event::assertNotDispatched(ModelsImported::class);
});

it('should index transactions using the timestamp from cache', function () {
    $this->travelTo(Carbon::parse('2024-04-09 13:32:44'));

    Event::fake();

    Cache::shouldReceive('get')
        ->with('latest-indexed-timestamp:transactions')
        ->andReturn(1711978364000) // 8 days ago, so new ones are 1 and 5 days ago
        ->shouldReceive('put')
        ->with('latest-indexed-timestamp:transactions', 1712583164)
        ->once();

    Transaction::factory()->create([
        'timestamp' => 1712237564000, // 5 days
    ]);

    Transaction::factory()->create([
        'timestamp' => 1711805564000, // 10 days
    ]);

    Transaction::factory()->create([
        'timestamp' => 1712583164000, // 1 day
    ]);

    IndexTransactions::dispatch();

    Event::assertDispatched(ModelsImported::class, function ($event) {
        return $event->models->count() === 2 &&
            $event->models->pluck('timestamp')->sort()->values()->toArray() === [
                Carbon::now()->subDays(5)->unix(),
                Carbon::now()->subDays(1)->unix(),
            ];
    });
});

it('should not index anything if meilisearch is empty', function () {
    Event::fake();

    $lastIndexedTransaction = Transaction::factory()->create([
        'timestamp' => 5,
    ]);

    $newTransaction = Transaction::factory()->create([
        'timestamp' => 10,
    ]);

    $oldTransaction = Transaction::factory()->create([
        'timestamp' => 1,
    ]);

    $url = sprintf(
        '%s/indexes/%s/search',
        config('scout.meilisearch.host'),
        'transactions'
    );

    Http::fake([
        $url => Http::response([
            // Empty results
            'hits' => [],
        ]),
    ]);

    IndexTransactions::dispatch();

    Event::assertNotDispatched(ModelsImported::class);
});
