<?php

declare(strict_types=1);

use App\Jobs\CacheProductivityByAddress;
use App\Models\Block;
use Illuminate\Support\Facades\Cache;

// @TODO: add tests for different scenarios (missed for days, dropped out for days and got back in)
it('should cache the productivity for the public key', function () {
    $block = Block::factory()->create();

    expect(Cache::tags('wallet')->has(md5("productivity/$block->proposer")))->toBeFalse();

    (new CacheProductivityByAddress($block->proposer))->handle();

    expect(Cache::tags('wallet')->has(md5("productivity/$block->proposer")))->toBeTrue();
    expect(Cache::tags('wallet')->get(md5("productivity/$block->proposer")))->toBeFloat();
});
