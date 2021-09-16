<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Wallet;
use App\Services\Monitor\Aggregates\DelegateTotalAggregates;
use Illuminate\Database\Eloquent\Collection;

beforeEach(function () {
    Wallet::factory()->create([
        'public_key' => 'generator',
        'attributes' => [
            'delegate' => [
                'username' => 'generator',
            ],
        ],
    ]);
});

it('should aggregate the total amount forged', function () {
    Block::factory(10)->create([
        'generator_public_key' => 'generator',
        'total_amount'         => '100000000',
    ])->pluck('generator_public_key')->toArray();

    $result = (new DelegateTotalAggregates())->aggregate();

    expect($result)->toBeInstanceOf(Collection::class);
    expect($result)->toHaveCount(1);
    expect($result->toArray()[0]['generator_public_key'])->toBe('generator');
    expect($result->toArray()[0]['total_amount'])->toBe((string) 10e8);
});

it('should aggregate the total fee forged', function () {
    Block::factory(10)->create([
        'generator_public_key' => 'generator',
        'total_fee'            => '100000000',
    ])->pluck('generator_public_key')->toArray();

    $result = (new DelegateTotalAggregates())->aggregate();

    expect($result)->toBeInstanceOf(Collection::class);
    expect($result)->toHaveCount(1);
    expect($result->toArray()[0]['generator_public_key'])->toBe('generator');
    expect($result->toArray()[0]['total_fee'])->toBe((string) 10e8);
});

it('should aggregate the total count forged', function () {
    Block::factory(10)->create([
        'generator_public_key' => 'generator',
    ])->pluck('generator_public_key')->toArray();

    $result = (new DelegateTotalAggregates())->aggregate();

    expect($result)->toBeInstanceOf(Collection::class);
    expect($result)->toHaveCount(1);
    expect($result->toArray()[0]['generator_public_key'])->toBe('generator');
    expect($result->toArray()[0]['count'])->toBe(10);
});

it('should aggregate the total rewards forged', function () {
    Block::factory(10)->create([
        'generator_public_key' => 'generator',
        'reward'               => '100000000',
    ])->pluck('generator_public_key')->toArray();

    $result = (new DelegateTotalAggregates())->aggregate();

    expect($result)->toBeInstanceOf(Collection::class);
    expect($result)->toHaveCount(1);
    expect($result->toArray()[0]['generator_public_key'])->toBe('generator');
    expect($result->toArray()[0]['reward'])->toBe((string) 10e8);
});

it('should aggregate all the forged data', function () {
    Block::factory(10)->create([
        'generator_public_key' => 'generator',
        'total_fee'            => '100000000',
        'total_amount'         => '200000000',
        'reward'               => '300000000',
    ])->pluck('generator_public_key')->toArray();

    $result = (new DelegateTotalAggregates())->aggregate();

    expect($result)->toBeInstanceOf(Collection::class);
    expect($result)->toHaveCount(1);
    expect($result->toArray()[0]['generator_public_key'])->toBe('generator');
    expect($result->toArray()[0]['count'])->toBe(10);
    expect($result->toArray()[0]['total_fee'])->toBe((string) 10e8);
    expect($result->toArray()[0]['total_amount'])->toBe((string) 20e8);
    expect($result->toArray()[0]['reward'])->toBe((string) 30e8);
});
