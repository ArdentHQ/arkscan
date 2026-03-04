<?php

declare(strict_types=1);

use App\Jobs\CacheProductivityByAddress;
use App\Models\Block;
use App\Models\ForgingStats;
use App\Models\Wallet;
use Illuminate\Support\Facades\Cache;

it('should cache the productivity for the public key', function () {
    $block = Block::factory()->create();

    expect(Cache::tags('wallet')->has(md5("productivity/$block->proposer")))->toBeFalse();

    (new CacheProductivityByAddress($block->proposer))->handle();

    expect(Cache::tags('wallet')->has(md5("productivity/$block->proposer")))->toBeTrue();
    expect(Cache::tags('wallet')->get(md5("productivity/$block->proposer")))->toBeFloat();
});

it('should cache zero productivity when no forging stats exist', function () {
    $address = Wallet::factory()->create()->address;

    (new CacheProductivityByAddress($address))->handle();

    expect(Cache::tags('wallet')->get(md5("missed_blocks/$address")))->toBe(0)
        ->and(Cache::tags('wallet')->get(md5("productivity/$address")))->toBe(0.0);
});

it('should cache missed blocks and mixed productivity values', function () {
    $address = Wallet::factory()->create()->address;

    ForgingStats::factory()->create([
        'address'       => $address,
        'timestamp'     => 1,
        'forged'        => true,
        'missed_height' => null,
    ]);

    ForgingStats::factory()->create([
        'address'       => $address,
        'timestamp'     => 2,
        'forged'        => true,
        'missed_height' => null,
    ]);

    ForgingStats::factory()->create([
        'address'       => $address,
        'timestamp'     => 3,
        'forged'        => true,
        'missed_height' => null,
    ]);

    ForgingStats::factory()->create([
        'address'       => $address,
        'timestamp'     => 4,
        'forged'        => false,
        'missed_height' => 4,
    ]);

    (new CacheProductivityByAddress($address))->handle();

    expect(Cache::tags('wallet')->get(md5("missed_blocks/$address")))->toBe(1)
        ->and(Cache::tags('wallet')->get(md5("productivity/$address")))->toBe(75.0);
});

it('should refresh cached productivity when the forging stats change', function () {
    $address = Wallet::factory()->create()->address;

    ForgingStats::factory()->create([
        'address'       => $address,
        'timestamp'     => 1,
        'forged'        => true,
        'missed_height' => null,
    ]);

    (new CacheProductivityByAddress($address))->handle();

    expect(Cache::tags('wallet')->get(md5("missed_blocks/$address")))->toBe(0)
        ->and(Cache::tags('wallet')->get(md5("productivity/$address")))->toBe(100.0);

    ForgingStats::factory()->create([
        'address'       => $address,
        'timestamp'     => 2,
        'forged'        => false,
        'missed_height' => 2,
    ]);

    (new CacheProductivityByAddress($address))->handle();

    expect(Cache::tags('wallet')->get(md5("missed_blocks/$address")))->toBe(1)
        ->and(Cache::tags('wallet')->get(md5("productivity/$address")))->toBe(50.0);
});
