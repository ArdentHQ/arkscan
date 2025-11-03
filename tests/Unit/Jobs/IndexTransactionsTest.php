<?php

declare(strict_types=1);

use App\Jobs\IndexTransactions;
use App\Models\Transaction;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Laravel\Scout\Events\ModelsImported;
use Meilisearch\Client as MeilisearchClient;
use Meilisearch\Endpoints\Indexes;
use function Tests\mockTaggedCache;

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
    Event::fake();

    mockTaggedCache()->shouldReceive('get')
        ->with('latest-indexed-timestamp:transactions')
        ->andReturn(null)
        ->shouldReceive('put')
        ->with('latest-indexed-timestamp:transactions', 10)
        ->once();

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
            'hits' => [
                $lastIndexedTransaction->toSearchableArray(),
            ],
        ]),
    ]);

    IndexTransactions::dispatch();

    Event::assertDispatched(ModelsImported::class, function ($event) use ($newTransaction) {
        return $event->models->count() === 1
            && $event->models->first()->is($newTransaction);
    });
});

it('should not store any value on cache if no new transactions', function () {
    Event::fake();

    mockTaggedCache()->shouldReceive('get')
        ->with('latest-indexed-timestamp:transactions')
        ->andReturn(6);

    Transaction::factory()->create([
        'timestamp' => 5,
    ]);

    IndexTransactions::dispatch();

    Event::assertNotDispatched(ModelsImported::class);
});

it('should index new transactions using the timestamp from cache', function () {
    Event::fake();

    mockTaggedCache()->shouldReceive('get')
        ->with('latest-indexed-timestamp:transactions')
        ->andReturn(2) // so new ones are the one with timestamp 5 and 10
        ->shouldReceive('put')
        ->with('latest-indexed-timestamp:transactions', 10)
        ->once();

    Transaction::factory()->create([
        'timestamp' => 10,
    ]);

    Transaction::factory()->create([
        'timestamp' => 5,
    ]);

    Transaction::factory()->create([
        'timestamp' => 1,
    ]);

    IndexTransactions::dispatch();

    Event::assertDispatched(ModelsImported::class, function ($event) {
        return $event->models->count() === 2 &&
            $event->models->pluck('timestamp')->sort()->values()->toArray() === [5, 10];
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
