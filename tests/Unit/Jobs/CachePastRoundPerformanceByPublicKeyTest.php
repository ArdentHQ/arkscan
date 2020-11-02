<?php

declare(strict_types=1);

use App\Jobs\CachePastRoundPerformanceByPublicKey;
use App\Models\Block;

use Illuminate\Support\Facades\Cache;
use function Tests\configureExplorerDatabase;

it('should cache the past performance for the given public key', function () {
    configureExplorerDatabase();

    $block = Block::factory(5)->create([
        'generator_public_key' => 'generator',
    ])[0];

    expect(Cache::tags('wallet')->has(md5("performance/$block->generator_public_key")))->toBeFalse();

    (new CachePastRoundPerformanceByPublicKey(10, $block->generator_public_key))->handle();

    expect(Cache::tags('wallet')->has(md5("performance/$block->generator_public_key")))->toBeTrue();
});
