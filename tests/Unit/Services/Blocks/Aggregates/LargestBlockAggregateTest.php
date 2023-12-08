<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Transaction;
use App\Services\Blocks\Aggregates\LargestBlockAggregate;

it('should get largest block', function () {
    $largestBlock = Block::factory()->create();
    $otherBlock   = Block::factory()->create();

    Transaction::factory()->transfer()->create([
        'amount'   => 20000 * 1e8,
        'fee'      => 10 * 1e8,
        'block_id' => $otherBlock->id,
    ]);

    Transaction::factory(200)->transfer()->create([
        'amount'   => 1000 * 1e8,
        'fee'      => 10 * 1e8,
        'block_id' => $largestBlock->id,
    ]);

    Transaction::factory(20)->transfer()->create([
        'amount'   => 60 * 1e8,
        'fee'      => 10 * 1e8,
        'block_id' => $otherBlock->id,
    ]);

    expect((new LargestBlockAggregate())->aggregate()->id)->toBe($largestBlock->id);
});

it('should get largest block for multipayments', function () {
    $largestBlock = Block::factory()->create();
    $otherBlock = Block::factory()->create();

    Transaction::factory()->transfer()->create([
        'block_id' => $otherBlock->id,
        'amount' => 2000 * 1e8,
        'fee'    => 10 * 1e8,
    ]);
    Transaction::factory()->multipayment()->create([
        'block_id' => $largestBlock->id,
        'amount' => 0,
        'fee'    => 10 * 1e8,
        'asset' => [
            'payments' => [
                [
                    'recipientId' => 'test-address',
                    'amount' => 300 * 1e8,
                ],
                [
                    'recipientId' => 'test-address',
                    'amount' => 300 * 1e8,
                ],
                [
                    'recipientId' => 'test-address',
                    'amount' => 5000 * 1e8,
                ],
                [
                    'recipientId' => 'test-address',
                    'amount' => 300 * 1e8,
                ],
                [
                    'recipientId' => 'test-address',
                    'amount' => 300 * 1e8,
                ],
            ],
        ],
    ]);
    Transaction::factory()->transfer()->create([
        'block_id' => $otherBlock->id,
        'amount' => 3000 * 1e8,
        'fee'    => 10 * 1e8,
    ]);

    expect((new LargestBlockAggregate())->aggregate()->id)->toBe($largestBlock->id);
});

it('should get largest block for transfers and multipayments', function () {
    $largestBlock = Block::factory()->create();
    $otherBlock = Block::factory()->create();

    Transaction::factory(2)->transfer()->create([
        'block_id' => $otherBlock->id,
        'amount' => 2000 * 1e8,
        'fee'    => 10 * 1e8,
    ]);
    Transaction::factory()->multipayment()->create([
        'block_id' => $largestBlock->id,
        'amount' => 0,
        'fee'    => 10 * 1e8,
        'asset' => [
            'payments' => [
                [
                    'recipientId' => 'test-address',
                    'amount' => 300 * 1e8,
                ],
                [
                    'recipientId' => 'test-address',
                    'amount' => 300 * 1e8,
                ],
                [
                    'recipientId' => 'test-address',
                    'amount' => 5000 * 1e8,
                ],
                [
                    'recipientId' => 'test-address',
                    'amount' => 300 * 1e8,
                ],
                [
                    'recipientId' => 'test-address',
                    'amount' => 300 * 1e8,
                ],
            ],
        ],
    ]);
    Transaction::factory(3)->transfer()->create([
        'block_id' => $otherBlock->id,
        'amount' => 3000 * 1e8,
        'fee'    => 10 * 1e8,
    ]);
    Transaction::factory()->transfer()->create([
        'block_id' => $largestBlock->id,
        'amount' => 7000 * 1e8,
        'fee'    => 10 * 1e8,
    ]);

    expect((new LargestBlockAggregate())->aggregate()->id)->toBe($largestBlock->id);
});

it('should return null if no records', function () {
    expect((new LargestBlockAggregate())->aggregate())->toBeNull();
});
