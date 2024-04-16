<?php

declare(strict_types=1);

use App\Console\Commands\CacheValidatorAggregates;
use App\Models\Block;
use App\Models\Wallet;
use App\Services\Cache\ValidatorCache;
use Carbon\Carbon;

it('should execute the command', function () {
    (new CacheValidatorAggregates())->handle($cache = new ValidatorCache());

    expect($cache->getTotalAmounts())->toBe([]);
    expect($cache->getTotalFees())->toBe([]);
    expect($cache->getTotalRewards())->toBe([]);
    expect($cache->getTotalBlocks())->toBe([]);
});

it('should update cache on each run', function () {
    $this->travelTo(Carbon::parse('2024-04-08 13:24:03'));

    $cache = new ValidatorCache();

    expect($cache->getCache()->has(md5('total_amounts')))->toBeFalse();
    expect($cache->getCache()->has(md5('total_blocks')))->toBeFalse();
    expect($cache->getCache()->has(md5('total_fees')))->toBeFalse();
    expect($cache->getCache()->has(md5('total_rewards')))->toBeFalse();

    (new CacheValidatorAggregates())->handle($cache);

    expect($cache->getTotalAmounts())->toBe([]);
    expect($cache->getTotalFees())->toBe([]);
    expect($cache->getTotalRewards())->toBe([]);
    expect($cache->getTotalBlocks())->toBe([]);

    $wallet = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'username'       => 'delegate_1',
                'voteBalance'    => 1234037456742,
                'producedBlocks' => 12340,
            ],
        ],
    ]);

    Block::factory()->create([
        'generator_public_key' => $wallet->public_key,
        'total_amount'         => 123 * 1e8,
        'total_fee'            => 3 * 1e8,
        'reward'               => 8 * 1e8,
    ]);

    expect($cache->getCache()->has(md5('total_amounts')))->toBeTrue();
    expect($cache->getTotalAmounts())->toBe([]);
    expect($cache->getTotalFees())->toBe([]);
    expect($cache->getTotalRewards())->toBe([]);
    expect($cache->getTotalBlocks())->toBe([]);

    (new CacheValidatorAggregates())->handle($cache = new ValidatorCache());

    expect($cache->getTotalAmounts())->toBe([$wallet->public_key => (string) intval(123 * 1e8)]);
    expect($cache->getTotalFees())->toBe([$wallet->public_key => (string) intval(3 * 1e8)]);
    expect($cache->getTotalRewards())->toBe([$wallet->public_key => (string) intval(8 * 1e8)]);
    expect($cache->getTotalBlocks())->toBe([$wallet->public_key => 1]);

    expect($cache->getCache()->has(md5('total_amounts')))->toBeTrue();
    expect($cache->getCache()->has(md5('total_blocks')))->toBeTrue();
    expect($cache->getCache()->has(md5('total_fees')))->toBeTrue();
    expect($cache->getCache()->has(md5('total_rewards')))->toBeTrue();
});
