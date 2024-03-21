<?php

declare(strict_types=1);

use App\Console\Commands\CacheValidatorVoterCounts;
use App\Models\Wallet;
use App\Services\Cache\ValidatorCache;
use Illuminate\Support\Facades\Cache;

it('should cache the voter count for the public key', function () {
    $wallet = Wallet::factory()->create([
        'attributes' => [
            'vote' => Wallet::factory()->create()->public_key,
        ],
    ]);

    $vote = $wallet->attributes['vote'];

    expect(Cache::tags('wallet')->has(md5("voter_count/$vote")))->toBeFalse();

    (new CacheValidatorVoterCounts())->handle();

    expect(Cache::tags('wallet')->has(md5("voter_count/$vote")))->toBeTrue();
});

it('should cache voter counts', function () {
    $wallet = Wallet::factory()->create([
        'balance'    => 123 * 1e8,
        'attributes' => [
            'vote' => Wallet::factory()->create()->public_key,
        ],
    ]);

    $wallet->attributes['vote'];

    $validatorCache = new ValidatorCache();

    expect($validatorCache->getTotalWalletsVoted())->toBe(0);
    expect($validatorCache->getTotalBalanceVoted())->toBe(0.0);

    (new CacheValidatorVoterCounts())->handle();

    expect($validatorCache->getTotalWalletsVoted())->toBe(1);
    expect($validatorCache->getTotalBalanceVoted())->toBe(123.0);
});

it('should not cache voter counts if no voting wallets found', function () {
    $validatorCache = new ValidatorCache();

    $validatorCache->setTotalWalletsVoted(1);
    $validatorCache->setTotalBalanceVoted(123);

    expect($validatorCache->getTotalWalletsVoted())->toBe(1);
    expect($validatorCache->getTotalBalanceVoted())->toBe(123.0);

    $walletCount = Wallet::select('balance')
        ->whereRaw("\"attributes\"->>'vote' is not null")
        ->count();

    expect($walletCount)->toBe(0);

    (new CacheValidatorVoterCounts())->handle();

    expect($validatorCache->getTotalWalletsVoted())->toBe(1);
    expect($validatorCache->getTotalBalanceVoted())->toBe(123.0);
});
