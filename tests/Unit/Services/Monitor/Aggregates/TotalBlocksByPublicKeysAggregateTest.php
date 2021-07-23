<?php

declare(strict_types=1);

use App\Models\Block;

use App\Services\Monitor\Aggregates\TotalBlocksByPublicKeysAggregate;

it('should aggregate the total amount forged by the given public keys', function () {
    Block::factory(10)->create([
        'generator_public_key' => 'generator',
    ])->pluck('generator_public_key')->toArray();

    Block::factory(10)->create([
        'generator_public_key' => 'another-generator',
    ]);

    $result = (new TotalBlocksByPublicKeysAggregate())->aggregate(['generator']);

    expect($result)->toBeArray();
    expect($result)->toBe(['generator' => 10]);
});
