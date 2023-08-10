<?php

declare(strict_types=1);

use App\Console\Commands\CacheDelegateVoterCounts;
use App\Models\Wallet;
use App\Services\Cache\DelegateCache;
use Illuminate\Support\Facades\Cache;

it('should cache the voter count for the public key', function () {
    $wallet = Wallet::factory()->create([
        'attributes' => [
            'vote' => Wallet::factory()->create()->public_key,
        ],
    ]);

    $vote = $wallet->attributes['vote'];

    expect(Cache::tags('wallet')->has(md5("voter_count/$vote")))->toBeFalse();

    (new CacheDelegateVoterCounts())->handle();

    expect(Cache::tags('wallet')->has(md5("voter_count/$vote")))->toBeTrue();
});

it('should cache voter counts', function () {
    $wallet = Wallet::factory()->create([
        'balance' => 123 * 1e8,
        'attributes' => [
            'vote' => Wallet::factory()->create()->public_key,
        ],
    ]);

    $wallet->attributes['vote'];

    $delegateCache = new DelegateCache();

    expect($delegateCache->getTotalVoted())->toBe([0, 0]);

    (new CacheDelegateVoterCounts())->handle();

    expect($delegateCache->getTotalVoted())->toEqual([1, 123]);
});
