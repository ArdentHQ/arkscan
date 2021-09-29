<?php

declare(strict_types=1);

use App\Console\Commands\CacheDelegateVoterCounts;
use App\Models\Wallet;
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
