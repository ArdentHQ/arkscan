<?php

declare(strict_types=1);

use App\Models\Block;

use App\Services\Monitor\Aggregates\TotalAmountsByPublicKeysAggregate;

it('should aggregate the total amount forged by the given public keys', function () {
    Block::factory(10)->create([
        'generator_public_key' => 'generator',
        'total_amount'         => '100000000',
    ])->pluck('generator_public_key')->toArray();

    $result = (new TotalAmountsByPublicKeysAggregate())->aggregate(['generator']);

    expect($result)->toBeArray();
    expect($result)->toBe(['generator' => (string) 10e8]);
});
