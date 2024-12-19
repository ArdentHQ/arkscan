<?php

declare(strict_types=1);

use App\Jobs\IndexBlocks;
use App\Models\Block;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
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

it('should index new blocks', function () {
    $this->travelTo(Carbon::parse('2024-04-09 13:32:44'));

    Event::fake();

    mockTaggedCache()->shouldReceive('get')
        ->with('latest-indexed-timestamp:blocks')
        ->andReturn(null)
        ->shouldReceive('put')
        ->with('latest-indexed-timestamp:blocks', 1712583164) // 1 day
        ->once();

    $latestIndexedBlock = Block::factory()->create([
        'timestamp' => 1712583164000, // 1 day
    ]);

    Block::factory()->create([
        'timestamp' => 1712237564000, // 5 days
    ]);

    Block::factory()->create([
        'timestamp' => 1711805564000, // 10 days
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

    Event::assertDispatched(ModelsImported::class, function ($event) {
        return $event->models->count() === 3 &&
            $event->models->pluck('timestamp')->sort()->values()->toArray() === [
                Carbon::now()->subDays(10)->unix(),
                Carbon::now()->subDays(5)->unix(),
                Carbon::now()->subDays(1)->unix(),
            ];
    });
});

it('should index blocks using the timestamp from cache', function () {
    $this->travelTo(Carbon::parse('2024-04-09 13:32:44'));

    Event::fake();

    $taggedCache = Cache::tags('tags');

    mockTaggedCache()->shouldReceive('get')
        ->with('latest-indexed-timestamp:blocks')
        ->andReturn(1711978364000) // 8 days ago, so new ones are 1 and 5 days ago
        ->shouldReceive('put')
        ->with('latest-indexed-timestamp:blocks', 1712583164) // 1 day
        ->once();

    Block::factory()->create([
        'timestamp' => 1712583164000, // 1 day
    ]);

    Block::factory()->create([
        'timestamp' => 1712237564000, // 5 days
    ]);

    Block::factory()->create([
        'timestamp' => 1711805564000, // 10 days
    ]);

    IndexBlocks::dispatch();

    Event::assertDispatched(ModelsImported::class, function ($event) {
        return $event->models->count() === 2 &&
            $event->models->pluck('timestamp')->sort()->values()->toArray() === [
                Carbon::now()->subDays(5)->unix(),
                Carbon::now()->subDays(1)->unix(),
            ];
    });
});

it('should not store any value on cache if no new transactions', function () {
    Event::fake();

    mockTaggedCache()->shouldReceive('get')
        ->with('latest-indexed-timestamp:blocks')
        ->andReturn(6);

    Block::factory()->create([
        'timestamp' => 5,
    ]);

    IndexBlocks::dispatch();

    Event::assertNotDispatched(ModelsImported::class);
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
