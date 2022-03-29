<?php

declare(strict_types=1);

use App\Console\Commands\CacheDelegatePerformance;
use App\Facades\Network;
use App\Models\Block;
use App\Models\Round;
use App\Services\Cache\WalletCache;
use Illuminate\Support\Facades\Cache;

it('should cache the past performance for a public key', function () {
    $round = 16;

    $publicKey = 'generator';

    Round::factory()->create([
        'round'      => $round,
        'public_key' => $publicKey,
    ]);

    foreach (range($round - 5, $round - 1) as $round) {
        Block::factory()->create([
            'generator_public_key' => $publicKey,
            'height'               => $round * Network::delegateCount(),
        ]);
    }

    expect(Block::whereGeneratorPublicKey($publicKey)->count())->toBe(5);
    expect(Cache::tags('wallet')->has(md5("performance/$publicKey")))->toBeFalse();

    (new CacheDelegatePerformance())->handle();

    expect(Cache::tags('wallet')->has(md5("performance/$publicKey")))->toBeTrue();
    expect((new WalletCache())->getPerformance($publicKey))->toBe([
        true,
        true,
        true,
        true,
        true,
    ]);
});

it('should cache end of a round missed blocks for a public key ', function () {
    $round = 16;

    $publicKey = 'generator';

    Round::factory()->create([
        'round'      => $round,
        'public_key' => $publicKey,
    ]);

    foreach (range($round - 5, $round - 2) as $round) {
        Block::factory()->create([
            'generator_public_key' => $publicKey,
            'height'               => $round * Network::delegateCount(),
        ]);
    }

    expect(Block::whereGeneratorPublicKey($publicKey)->count())->toBe(4);
    expect(Cache::tags('wallet')->has(md5("performance/$publicKey")))->toBeFalse();

    (new CacheDelegatePerformance())->handle();

    expect(Cache::tags('wallet')->has(md5("performance/$publicKey")))->toBeTrue();
    expect((new WalletCache())->getPerformance($publicKey))->toBe([
        true,
        true,
        true,
        true,
        false,
    ]);
});

it('uses the 1st block to set the performance on the first range', function () {
    $round     = 16;
    $publicKey = 'generator';

    Round::factory()->create([
        'round'      => $round,
        'public_key' => $publicKey,
    ]);

    // First block in range 1
    Block::factory()->create([
        'generator_public_key' => $publicKey,
        'height'               => (($round - 6) * Network::delegateCount()) + 1, // 511
    ]);

    expect(Block::whereGeneratorPublicKey($publicKey)->count())->toBe(1);
    expect(Cache::tags('wallet')->has(md5("performance/$publicKey")))->toBeFalse();

    (new CacheDelegatePerformance())->handle();

    expect(Cache::tags('wallet')->has(md5("performance/$publicKey")))->toBeTrue();
    expect((new WalletCache())->getPerformance($publicKey))->toBe([
        true,
        false,
        false,
        false,
        false,
    ]);
});

it('uses the 51st block to set the performance on the first range', function () {
    $round     = 16;
    $publicKey = 'generator';

    Round::factory()->create([
        'round'      => $round,
        'public_key' => $publicKey,
    ]);

    // Last blocks in range 1
    Block::factory()->create([
        'generator_public_key' => $publicKey,
        'height'               => ($round - 5) * Network::delegateCount(), // 561
    ]);

    expect(Block::whereGeneratorPublicKey($publicKey)->count())->toBe(1);
    expect(Cache::tags('wallet')->has(md5("performance/$publicKey")))->toBeFalse();

    (new CacheDelegatePerformance())->handle();

    expect(Cache::tags('wallet')->has(md5("performance/$publicKey")))->toBeTrue();
    expect((new WalletCache())->getPerformance($publicKey))->toBe([
        true,
        false,
        false,
        false,
        false,
    ]);
});

it('uses the 52st block to set the performance on the second range', function () {
    $round     = 16;
    $publicKey = 'generator';

    Round::factory()->create([
        'round'      => $round,
        'public_key' => $publicKey,
    ]);

    // First blocks in range 2
    Block::factory()->create([
        'generator_public_key' => $publicKey,
        'height'               => ($round - 5) * Network::delegateCount() + 1, // 562
    ]);

    expect(Block::whereGeneratorPublicKey($publicKey)->count())->toBe(1);
    expect(Cache::tags('wallet')->has(md5("performance/$publicKey")))->toBeFalse();

    (new CacheDelegatePerformance())->handle();

    expect(Cache::tags('wallet')->has(md5("performance/$publicKey")))->toBeTrue();
    expect((new WalletCache())->getPerformance($publicKey))->toBe([
        false,
        true,
        false,
        false,
        false,
    ]);
});
