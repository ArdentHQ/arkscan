<?php

declare(strict_types=1);

use App\Models\Block;

use App\Services\Monitor\Aggregates\TotalFeesByPublicKeysAggregate;

it('should aggregate the total fee forged by the given public keys', function () {
    Block::factory(10)->create([
        'generator_public_key' => 'generator',
        'total_fee'            => '100000000',
    ])->pluck('generator_public_key')->toArray();

    $result = (new TotalFeesByPublicKeysAggregate())->aggregate(['generator']);

    expect($result)->toBeArray();
    expect($result)->toBe(['generator' => (string) 10e8]);
});
