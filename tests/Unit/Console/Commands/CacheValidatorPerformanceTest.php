<?php

declare(strict_types=1);

use App\Console\Commands\CacheValidatorPerformance;
use App\Facades\Network;
use App\Models\Block;
use App\Models\Wallet;
use App\Services\Cache\WalletCache;

use function Tests\createRealisticRound;
use function Tests\createRoundEntry;

it('should cache the past performance for a public key', function () {
    $currentRound = 16;
    $cache = new WalletCache();

    $wallets = Wallet::factory(Network::validatorCount())
        ->activeValidator()
        ->create();

    $publicKey = $wallets->first()->public_key;

    foreach (range($currentRound - 5, $currentRound - 1) as $round) {
        createRoundEntry($round, $round * Network::validatorCount(), $wallets);

        Block::factory()->create([
            'generator_public_key' => $publicKey,
            'height'               => $round * Network::validatorCount(),
        ]);
    }

    expect(Block::whereGeneratorPublicKey($publicKey)->count())->toBe(5);
    expect($cache->getCache()->has(md5('performance/'.$publicKey)))->toBeFalse();

    (new CacheValidatorPerformance())->handle();

    expect($cache->getCache()->has(md5('performance/'.$publicKey)))->toBeTrue();
    expect($cache->getPerformance($publicKey))->toBe([
        true,
        true,
    ]);
});

it('should cache end of a round missed blocks for a public key', function () {
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

    $publicKey = $validators->get(4)->public_key;

    $cache = new WalletCache();

    expect(Block::whereGeneratorPublicKey($publicKey)->count())->toBe(2);
    expect($cache->getCache()->has(md5('performance/'.$publicKey)))->toBeFalse();

    (new CacheValidatorPerformance())->handle();

    expect($cache->getCache()->has(md5('performance/'.$publicKey)))->toBeTrue();
    expect($cache->getPerformance($publicKey))->toBe([true, false]);
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

    $publicKey = $validators->get(4)->public_key;

    $cache = new WalletCache();

    expect(Block::whereGeneratorPublicKey($publicKey)->count())->toBe(1);
    expect($cache->getCache()->has(md5('performance/'.$publicKey)))->toBeFalse();

    (new CacheValidatorPerformance())->handle();

    expect($cache->getCache()->has(md5('performance/'.$publicKey)))->toBeTrue();
    expect($cache->getPerformance($publicKey))->toBe([
        false,
        false,
    ]);
});
