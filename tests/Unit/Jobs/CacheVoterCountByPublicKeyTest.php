<?php

declare(strict_types=1);

use App\Jobs\CacheVoterCountByPublicKey;
use App\Models\Wallet;
use Illuminate\Support\Facades\Cache;
use function Tests\configureExplorerDatabase;

it('should cache the voter count for the public key', function () {
    configureExplorerDatabase();

    $wallet = Wallet::factory()->create([
        'attributes' => [
            'vote' => Wallet::factory()->create()->public_key,
        ],
    ]);
    $vote = $wallet->attributes['vote'];

    expect(Cache::tags('wallet')->has(md5("voter_count/$vote")))->toBeFalse();

    (new CacheVoterCountByPublicKey($vote))->handle();

    expect(Cache::tags('wallet')->has(md5("voter_count/$vote")))->toBeTrue();
});
