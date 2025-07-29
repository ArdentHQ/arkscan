<?php

declare(strict_types=1);

use App\Models\Block;
use App\Services\Search\BlockSearch;
use Illuminate\Support\Collection;

it('should search for a block by hash', function (?string $modifier) {
    $block = Block::factory(10)->create()[0];

    $result = (new BlockSearch())->search($modifier ? $modifier($block->hash) : $block->hash, 5);

    expect($result)->toHaveCount(1);
})->with([null, 'strtolower', 'strtoupper']);

it('should query blocks by id', function () {
    Block::factory()->create([
        'hash' => 'aaaaaabbbbbbbccccccdddddd',
    ]);

    Block::factory()->create([
        'hash' => 'bbbbbbbddddd',
    ]);

    Block::factory()->create([
        'hash' => 'ccccccdddddd',
    ]);

    expect((new BlockSearch())->search('aaaaaa', 5))->toHaveCount(1);

    expect((new BlockSearch())->search('bbbbbb', 5))->toHaveCount(2);

    expect((new BlockSearch())->search('ddddd', 5))->toHaveCount(3);
});

it('limit the results', function () {
    Block::factory()->create([
        'hash' => 'aaaaaabbbbbbbccccccdddddd',
    ]);

    Block::factory()->create([
        'hash' => 'aaaaaabbbbbbbccccccdddddd2',
    ]);

    Block::factory()->create([
        'hash' => 'aaaaaabbbbbbbccccccdddddd3',
    ]);

    expect((new BlockSearch())->search('aaaaaa', 2))->toHaveCount(2);
});

it('should map meilisearch results array', function () {
    $block = Block::factory()->create();

    $result = BlockSearch::mapMeilisearchResults([$block->toSearchableArray()]);

    expect($result)->toBeInstanceOf(Collection::class);

    expect($result->first())->toBeInstanceOf(Block::class);

    expect($result->first()->id)->toBe($block->id);
});

it('should produce the right meilisearch query when possibly address', function () {
    $query = BlockSearch::buildSearchQueryForIndex('0x6E4C6817a95263B758bbC52e87Ce8e759eD0B084', 5);

    expect($query)->toBeNull();
});

it('should produce the right meilisearch query when possibly transaction id', function () {
    $query = BlockSearch::buildSearchQueryForIndex('13114381566690093367', 5);

    expect($query->toArray())->toMatchArray([
        'indexUid' => 'blocks',
        'filter'   => ['hash = "13114381566690093367"'],
        'limit'    => 5,
    ]);
});

it('should handle spaces in search query', function () {
    $query = BlockSearch::buildSearchQueryForIndex('a b', 5);

    expect($query->toArray())->toMatchArray([
        'indexUid' => 'blocks',
        'filter'   => ['hash = "a b"'],
        'limit'    => 5,
    ]);
});

it('should handle special characters in search query', function () {
    $query = BlockSearch::buildSearchQueryForIndex('a b \ ( "', 5);

    expect($query->toArray())->toMatchArray([
        'indexUid' => 'blocks',
        'filter'   => ['hash = "a b \\\\ ( \""'],
        'limit'    => 5,
    ]);
});
