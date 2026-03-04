<?php

declare(strict_types=1);

use App\Models\Block;
use App\Repositories\BlockRepository;
use App\Repositories\BlockRepositoryWithCache;
use Illuminate\Support\Facades\Cache;
use Tests\Stubs\BlockRepositoryStub;

beforeEach(function () {
    Cache::tags('blocks')->flush();

    $this->subject = new BlockRepositoryWithCache(new BlockRepository());
});

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

it('should cache the block lookups', function () {
    $repository = new BlockRepositoryStub(Block::factory()->make());
    $subject    = new BlockRepositoryWithCache($repository);

    $subject->findByHash('block-hash');
    $subject->findByHash('block-hash');
    $subject->findByHeight(42);
    $subject->findByHeight(42);
    $subject->findByIdentifier('identifier');
    $subject->findByIdentifier('identifier');

    expect($repository->findByHashCalls)->toBe(1)
        ->and($repository->findByHeightCalls)->toBe(1)
        ->and($repository->findByIdentifierCalls)->toBe(1);
});
