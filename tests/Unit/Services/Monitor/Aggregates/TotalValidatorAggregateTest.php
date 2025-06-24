<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\Monitor\Aggregates\ValidatorTotalAggregates;
use Illuminate\Database\Eloquent\Collection;

beforeEach(function () {
    Wallet::factory()->create([
        'address'    => 'generator',
        'attributes' => [
            'validatorPublicKey' => 'publickey',
            'username'           => 'generator',
        ],
    ]);
});

it('should aggregate the total amount forged', function () {
    $blocks = Block::factory(10)->create([
        'proposer' => 'generator',
    ]);

    foreach ($blocks as $block) {
        Transaction::factory()->create([
            'value' => 1 * 1e18,
            'block_hash' => $block->hash,
        ]);
    }

    $result = (new ValidatorTotalAggregates())->aggregate();

    expect($result)->toBeInstanceOf(Collection::class);
    expect($result)->toHaveCount(1);
    expect($result->toArray()[0]['proposer'])->toBe('generator');
    expect($result->toArray()[0]['amount'])->toBe(BigNumber::new(10)->valueOf()->multipliedBy(1e18)->__toString());
});

it('should aggregate the total fee forged', function () {
    $blocks = Block::factory(10)->create([
        'proposer' => 'generator',
        'fee'      => 1 * 1e18,
    ]);

    foreach ($blocks as $block) {
        Transaction::factory()->create([
            'value' => 1 * 1e18,
            'block_hash' => $block->hash,
        ]);
    }

    $result = (new ValidatorTotalAggregates())->aggregate();

    expect($result)->toBeInstanceOf(Collection::class);
    expect($result)->toHaveCount(1);
    expect($result->toArray()[0]['proposer'])->toBe('generator');
    expect($result->toArray()[0]['fee'])->toBe(BigNumber::new(10)->valueOf()->multipliedBy(1e18)->__toString());
});

it('should aggregate the total count forged', function () {
    $blocks = Block::factory(10)->create([
        'proposer' => 'generator',
    ]);

    foreach ($blocks as $block) {
        Transaction::factory()->create([
            'value' => 1 * 1e18,
            'block_hash' => $block->hash,
        ]);
    }

    $result = (new ValidatorTotalAggregates())->aggregate();

    expect($result)->toBeInstanceOf(Collection::class);
    expect($result)->toHaveCount(1);
    expect($result->toArray()[0]['proposer'])->toBe('generator');
    expect($result->toArray()[0]['count'])->toBe(10);
});

it('should aggregate the total rewards forged', function () {
    $blocks = Block::factory(10)->create([
        'proposer' => 'generator',
        'reward'   => 1 * 1e18,
    ]);

    foreach ($blocks as $block) {
        Transaction::factory()->create([
            'value' => 1 * 1e18,
            'block_hash' => $block->hash,
        ]);
    }

    $result = (new ValidatorTotalAggregates())->aggregate();

    expect($result)->toBeInstanceOf(Collection::class);
    expect($result)->toHaveCount(1);
    expect($result->toArray()[0]['proposer'])->toBe('generator');
    expect($result->toArray()[0]['reward'])->toBe(BigNumber::new(10)->valueOf()->multipliedBy(1e18)->__toString());
});

it('should aggregate all the forged data', function () {
    $blocks = Block::factory(10)->create([
        'proposer' => 'generator',
        'fee'      => 1 * 1e18,
        'reward'   => 3 * 1e18,
    ]);

    foreach ($blocks as $block) {
        Transaction::factory()->create([
            'value' => 2 * 1e18,
            'block_hash' => $block->hash,
        ]);
    }

    $result = (new ValidatorTotalAggregates())->aggregate();

    expect($result)->toBeInstanceOf(Collection::class);
    expect($result)->toHaveCount(1);
    expect($result->toArray()[0]['proposer'])->toBe('generator');
    expect($result->toArray()[0]['count'])->toBe(10);
    expect($result->toArray()[0]['fee'])->toBe(BigNumber::new(10)->valueOf()->multipliedBy(1e18)->__toString());
    expect($result->toArray()[0]['amount'])->toBe(BigNumber::new(20)->valueOf()->multipliedBy(1e18)->__toString());
    expect($result->toArray()[0]['reward'])->toBe(BigNumber::new(30)->valueOf()->multipliedBy(1e18)->__toString());
});
