<?php

declare(strict_types=1);

use App\Jobs\CacheLastBlockByPublicKey;
use App\Models\Block;

use Illuminate\Support\Facades\Cache;
use function Tests\configureExplorerDatabase;

it('should cache the last block forged by the public key', function () {
    configureExplorerDatabase();

    $block = Block::factory()->create();

    expect(Cache::tags('wallet')->has(md5("last_block/$block->generator_public_key")))->toBeFalse();

    (new CacheLastBlockByPublicKey($block->generator_public_key))->handle();

    expect(Cache::tags('wallet')->has(md5("last_block/$block->generator_public_key")))->toBeTrue();
});
