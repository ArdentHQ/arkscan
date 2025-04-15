<?php

declare(strict_types=1);

use App\Console\Commands\CacheValidatorAggregates;
use App\Models\Block;
use App\Models\Wallet;
use App\Services\BigNumber;
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

    $wallet = Wallet::factory()
        ->activeValidator()
        ->create([
            'public_key' => 'public-key',
            'attributes' => [
                'username'                => 'validator_1',
                'validatorPublicKey'      => 'validator-public-key',
                'validatorVoteBalance'    => 1234037456742,
                'validatorProducedBlocks' => 12340,
            ],
        ]);

    Block::factory()->create([
        'proposer' => $wallet->address,
        'amount'      => 123 * 1e18,
        'fee'         => 3 * 1e18,
        'reward'            => 8 * 1e18,
    ]);

    expect($cache->getCache()->has(md5('total_amounts')))->toBeTrue();
    expect($cache->getTotalAmounts())->toBe([]);
    expect($cache->getTotalFees())->toBe([]);
    expect($cache->getTotalRewards())->toBe([]);
    expect($cache->getTotalBlocks())->toBe([]);

    (new CacheValidatorAggregates())->handle($cache = new ValidatorCache());

    expect($cache->getTotalAmounts())->toBe([$wallet->address => BigNumber::new(123)->valueOf()->multipliedBy(1e18)->__toString()]);
    expect($cache->getTotalFees())->toBe([$wallet->address => BigNumber::new(3)->valueOf()->multipliedBy(1e18)->__toString()]);
    expect($cache->getTotalRewards())->toBe([$wallet->address => BigNumber::new(8)->valueOf()->multipliedBy(1e18)->__toString()]);
    expect($cache->getTotalBlocks())->toBe([$wallet->address => 1]);

    expect($cache->getCache()->has(md5('total_amounts')))->toBeTrue();
    expect($cache->getCache()->has(md5('total_blocks')))->toBeTrue();
    expect($cache->getCache()->has(md5('total_fees')))->toBeTrue();
    expect($cache->getCache()->has(md5('total_rewards')))->toBeTrue();
});
