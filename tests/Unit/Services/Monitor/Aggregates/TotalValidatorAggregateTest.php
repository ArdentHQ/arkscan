<?php

declare(strict_types=1);

use App\Models\Block;
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
    Block::factory(10)->create([
        'generator_address'    => 'generator',
        'amount'         => 1 * 1e18,
    ])->pluck('generator_address')->toArray();

    $result = (new ValidatorTotalAggregates())->aggregate();

    expect($result)->toBeInstanceOf(Collection::class);
    expect($result)->toHaveCount(1);
    expect($result->toArray()[0]['generator_address'])->toBe('generator');
    expect($result->toArray()[0]['amount'])->toBe(BigNumber::new(10)->valueOf()->multipliedBy(1e18)->__toString());
});

it('should aggregate the total fee forged', function () {
    Block::factory(10)->create([
        'generator_address'    => 'generator',
        'fee'            => 1 * 1e18,
    ])->pluck('generator_address')->toArray();

    $result = (new ValidatorTotalAggregates())->aggregate();

    expect($result)->toBeInstanceOf(Collection::class);
    expect($result)->toHaveCount(1);
    expect($result->toArray()[0]['generator_address'])->toBe('generator');
    expect($result->toArray()[0]['fee'])->toBe(BigNumber::new(10)->valueOf()->multipliedBy(1e18)->__toString());
});

it('should aggregate the total count forged', function () {
    Block::factory(10)->create([
        'generator_address' => 'generator',
    ])->pluck('generator_address')->toArray();

    $result = (new ValidatorTotalAggregates())->aggregate();

    expect($result)->toBeInstanceOf(Collection::class);
    expect($result)->toHaveCount(1);
    expect($result->toArray()[0]['generator_address'])->toBe('generator');
    expect($result->toArray()[0]['count'])->toBe(10);
});

it('should aggregate the total rewards forged', function () {
    Block::factory(10)->create([
        'generator_address'    => 'generator',
        'reward'               => 1 * 1e18,
    ])->pluck('generator_address')->toArray();

    $result = (new ValidatorTotalAggregates())->aggregate();

    expect($result)->toBeInstanceOf(Collection::class);
    expect($result)->toHaveCount(1);
    expect($result->toArray()[0]['generator_address'])->toBe('generator');
    expect($result->toArray()[0]['reward'])->toBe(BigNumber::new(10)->valueOf()->multipliedBy(1e18)->__toString());
});

it('should aggregate all the forged data', function () {
    Block::factory(10)->create([
        'generator_address'    => 'generator',
        'fee'            => 1 * 1e18,
        'amount'         => 2 * 1e18,
        'reward'               => 3 * 1e18,
    ])->pluck('generator_address')->toArray();

    $result = (new ValidatorTotalAggregates())->aggregate();

    expect($result)->toBeInstanceOf(Collection::class);
    expect($result)->toHaveCount(1);
    expect($result->toArray()[0]['generator_address'])->toBe('generator');
    expect($result->toArray()[0]['count'])->toBe(10);
    expect($result->toArray()[0]['fee'])->toBe(BigNumber::new(10)->valueOf()->multipliedBy(1e18)->__toString());
    expect($result->toArray()[0]['amount'])->toBe(BigNumber::new(20)->valueOf()->multipliedBy(1e18)->__toString());
    expect($result->toArray()[0]['reward'])->toBe(BigNumber::new(30)->valueOf()->multipliedBy(1e18)->__toString());
});
