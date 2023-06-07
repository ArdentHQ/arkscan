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
