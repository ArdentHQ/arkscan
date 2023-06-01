<?php

declare(strict_types=1);

use App\Models\Block;
use App\Services\Search\BlockSearch;
use Illuminate\Support\Collection;

it('should search for a block by id', function (?string $modifier) {
    $block = Block::factory(10)->create()[0];

    $result = (new BlockSearch())->search($modifier ? $modifier($block->id) : $block->id, 5);

    expect($result)->toHaveCount(1);
})->with([null, 'strtolower', 'strtoupper']);

it('should search for a block by height', function () {
    Block::factory()->create([
        'height' => 2147483646,
    ]);

    $result = (new BlockSearch())->search('2147483646', 5);

    expect($result)->toHaveCount(1);
});

it('should query blocks by id', function () {
    Block::factory()->create([
        'id' => 'aaaaaabbbbbbbccccccdddddd',
    ]);

    Block::factory()->create([
        'id' => 'bbbbbbbddddd',
    ]);

    Block::factory()->create([
        'id' => 'ccccccdddddd',
    ]);

    expect((new BlockSearch())->search('aaaaaa', 5))->toHaveCount(1);

    expect((new BlockSearch())->search('bbbbbb', 5))->toHaveCount(2);

    expect((new BlockSearch())->search('ddddd', 5))->toHaveCount(3);
});

it('limit the results', function () {
    Block::factory()->create([
        'id' => 'aaaaaabbbbbbbccccccdddddd',
    ]);

    Block::factory()->create([
        'id' => 'aaaaaabbbbbbbccccccdddddd2',
    ]);

    Block::factory()->create([
        'id' => 'aaaaaabbbbbbbccccccdddddd3',
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
