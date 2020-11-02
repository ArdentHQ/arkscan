<?php

declare(strict_types=1);

use App\Jobs\CacheProductivityByPublicKey;
use App\Models\Block;

use Illuminate\Support\Facades\Cache;
use function Tests\configureExplorerDatabase;

// @TODO: add tests for different scenarios (missed for days, dropped out for days and got back in)
it('should cache the productivity for the public key', function () {
    configureExplorerDatabase();

    $block = Block::factory()->create();

    expect(Cache::tags('wallet')->has(md5("productivity/$block->generator_public_key")))->toBeFalse();

    (new CacheProductivityByPublicKey($block->generator_public_key))->handle();

    expect(Cache::tags('wallet')->has(md5("productivity/$block->generator_public_key")))->toBeTrue();
    expect(Cache::tags('wallet')->get(md5("productivity/$block->generator_public_key")))->toBeFloat();
});
