<?php

declare(strict_types=1);

use App\Jobs\IndexBlocks;
use App\Models\Block;
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

it('should index new blocks', function () {
    Event::fake();

    $latestIndexedBlock = Block::factory()->create([
        'timestamp' => 5,
    ]);

    $newBlock = Block::factory()->create([
        'timestamp' => 10,
    ]);

    $oldBlock = Block::factory()->create([
        'timestamp' => 1,
    ]);

    $url = sprintf(
        '%s/indexes/%s/search',
        config('scout.meilisearch.host'),
        'blocks'
    );

    Http::fake([
        $url => Http::response([
            'hits' => [
                $latestIndexedBlock->toSearchableArray(),
            ],
        ]),
    ]);

    IndexBlocks::dispatch();

    Event::assertDispatched(ModelsImported::class, function ($event) use ($newBlock) {
        return $event->models->count() === 1
            && $event->models->first()->is($newBlock);
    });
});

it('should not index anything if meilisearch is empty', function () {
    Event::fake();

    $latestIndexedBlock = Block::factory()->create([
        'timestamp' => 5,
    ]);

    $newBlock = Block::factory()->create([
        'timestamp' => 10,
    ]);

    $oldBlock = Block::factory()->create([
        'timestamp' => 1,
    ]);

    $url = sprintf(
        '%s/indexes/%s/search',
        config('scout.meilisearch.host'),
        'blocks'
    );

    Http::fake([
        $url => Http::response([
            // Empty results
            'hits' => [],
        ]),
    ]);

    IndexBlocks::dispatch();

    Event::assertNotDispatched(ModelsImported::class);
});
