<?php

declare(strict_types=1);

use App\Models\Block;
use App\Repositories\BlockRepository;

beforeEach(fn () => $this->subject = new BlockRepository());

it('should find a block by its id', function () {
    $block = Block::factory()->create();

    expect($this->subject->findById($block->id))->toBeInstanceOf(Block::class);
});

it('should find a block by its height', function () {
    $block = Block::factory()->create();

    expect($this->subject->findByHeight($block->height->toNumber()))->toBeInstanceOf(Block::class);
});

it('should find a block by its id or height', function () {
    $block = Block::factory()->create();

    expect($this->subject->findByIdentifier($block->id))->toBeInstanceOf(Block::class);
    expect($this->subject->findByIdentifier($block->height->toNumber()))->toBeInstanceOf(Block::class);
});
