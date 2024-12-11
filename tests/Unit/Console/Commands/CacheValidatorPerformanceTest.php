<?php

declare(strict_types=1);

use App\Console\Commands\CacheValidatorPerformance;
use App\Facades\Network;
use App\Models\Block;
use App\Models\Wallet;
use App\Services\Cache\WalletCache;
use function Tests\createRealisticRound;
use function Tests\createRoundEntry;

it('should cache the past performance for an address', function () {
    $currentRound = 16;
    $cache        = new WalletCache();

    $wallets = Wallet::factory(Network::validatorCount())
        ->activeValidator()
        ->create();

    $address = $wallets->first()->address;

    foreach (range($currentRound - 5, $currentRound - 1) as $round) {
        createRoundEntry($round, $round * Network::validatorCount(), $wallets);

        Block::factory()->create([
            'generator_address'    => $address,
            'height'               => $round * Network::validatorCount(),
        ]);
    }

    expect(Block::whereGeneratorAddress($address)->count())->toBe(5);
    expect($cache->getCache()->has(md5('performance/'.$address)))->toBeFalse();

    (new CacheValidatorPerformance())->handle();

    expect($cache->getCache()->has(md5('performance/'.$address)))->toBeTrue();
    expect($cache->getPerformance($address))->toBe([
        true,
        true,
    ]);
});

it('should cache end of a round missed blocks for an address', function () {
    $this->freezeTime();

    [0 => $validators] = createRealisticRound([
        array_fill(0, 53, true),
        [
            ...array_fill(0, 4, true),
            false,
            ...array_fill(0, 48, true),
        ],
        [ // Doesn't use data from the last round
            ...array_fill(0, 4, true),
            false,
            ...array_fill(0, 48, true),
        ],
    ], $this, false);

    $address = $validators->get(4)->address;

    $cache = new WalletCache();

    expect(Block::whereGeneratorAddress($address)->count())->toBe(2);
    expect($cache->getCache()->has(md5('performance/'.$address)))->toBeFalse();

    (new CacheValidatorPerformance())->handle();

    expect($cache->getCache()->has(md5('performance/'.$address)))->toBeTrue();
    expect($cache->getPerformance($address))->toBe([true, false]);
});

it('should cache concurrent failures', function () {
    $this->freezeTime();

    [0 => $validators] = createRealisticRound([
        [
            ...array_fill(0, 4, true),
            false,
            ...array_fill(0, 48, true),
        ],
        [
            ...array_fill(0, 4, true),
            false,
            ...array_fill(0, 48, true),
        ],
        [ // Doesn't use data from the last round
            ...array_fill(0, 4, true),
            false,
            ...array_fill(0, 48, true),
        ],
    ], $this, false);

    $address = $validators->get(4)->address;

    $cache = new WalletCache();

    expect(Block::whereGeneratorAddress($address)->count())->toBe(1);
    expect($cache->getCache()->has(md5('performance/'.$address)))->toBeFalse();

    (new CacheValidatorPerformance())->handle();

    expect($cache->getCache()->has(md5('performance/'.$address)))->toBeTrue();
    expect($cache->getPerformance($address))->toBe([
        false,
        false,
    ]);
});
