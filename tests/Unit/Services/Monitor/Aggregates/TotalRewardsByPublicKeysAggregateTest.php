<?php

declare(strict_types=1);

use App\Models\Block;

use App\Services\Monitor\Aggregates\TotalRewardsByPublicKeysAggregate;

it('should aggregate the total rewards forged by the given public keys', function () {
    Block::factory(10)->create([
        'generator_public_key' => 'generator',
        'reward'               => '100000000',
    ])->pluck('generator_public_key')->toArray();

    $result = (new TotalRewardsByPublicKeysAggregate())->aggregate(['generator']);

    expect($result)->toBeArray();
    expect($result)->toBe(['generator' => (string) 10e8]);
});
