<?php

declare(strict_types=1);

use App\Jobs\CacheUsername;
use App\Models\Wallet;
use App\Services\Cache\WalletCache;
use Illuminate\Support\Facades\Cache;

// @TODO: add tests for different scenarios (missed for days, dropped out for days and got back in)
it('should cache the username for the public key', function () {
    $wallet = Wallet::factory()->create();

    expect(Cache::tags('wallet')->has(md5("username_by_address/$wallet->address")))->toBeFalse();
    expect(Cache::tags('wallet')->has(md5("username_by_public_key/$wallet->public_key")))->toBeFalse();

    (new CacheUsername($wallet->toArray()))->handle(new WalletCache());

    expect(Cache::tags('wallet')->has(md5("username_by_address/$wallet->address")))->toBeTrue();
    expect(Cache::tags('wallet')->has(md5("username_by_public_key/$wallet->public_key")))->toBeTrue();

    expect(Cache::tags('wallet')->get(md5("username_by_address/$wallet->address")))->toBeString();
    expect(Cache::tags('wallet')->get(md5("username_by_public_key/$wallet->public_key")))->toBeString();
});
