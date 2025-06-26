<?php

declare(strict_types=1);

// @TODO: assert that cache has been called

use App\Models\Block;
use App\Repositories\BlockRepository;
use App\Repositories\BlockRepositoryWithCache;

beforeEach(fn () => $this->subject = new BlockRepositoryWithCache(new BlockRepository()));

it('should find a block by its id', function () {
    $block = Block::factory()->create();

    expect($this->subject->findByHash($block->hash))->toBeInstanceOf(Block::class);
});

it('should find a block by its number', function () {
    $block = Block::factory()->create();

    expect($this->subject->findByHeight($block->number->toNumber()))->toBeInstanceOf(Block::class);
});

it('should find a block by its id or number', function () {
    $block = Block::factory()->create();

    expect($this->subject->findByIdentifier($block->hash))->toBeInstanceOf(Block::class);
    expect($this->subject->findByIdentifier($block->number->toNumber()))->toBeInstanceOf(Block::class);
});
