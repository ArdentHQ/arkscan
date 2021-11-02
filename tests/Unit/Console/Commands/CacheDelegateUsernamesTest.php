<?php

declare(strict_types=1);

use App\Console\Commands\CacheDelegateUsernames;
use App\Models\Wallet;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

it('should cache the username for the public key', function () {
    $wallet = Wallet::factory()->create();

    expect(Cache::tags('wallet')->has(md5("username_by_address/$wallet->address")))->toBeFalse();
    expect(Cache::tags('wallet')->has(md5("username_by_public_key/$wallet->public_key")))->toBeFalse();

    (new CacheDelegateUsernames())->handle();

    expect(Cache::tags('wallet')->has(md5("username_by_address/$wallet->address")))->toBeTrue();
    expect(Cache::tags('wallet')->has(md5("username_by_public_key/$wallet->public_key")))->toBeTrue();

    expect(Cache::tags('wallet')->get(md5("username_by_address/$wallet->address")))->toBeString();
    expect(Cache::tags('wallet')->get(md5("username_by_public_key/$wallet->public_key")))->toBeString();
});

it('should cache the known wallet name if defined', function () {
    $knownWalletsUrl = 'https://knownwallets.com/known-wallets.json';

    Config::set('explorer.networks.development.knownWallets', $knownWalletsUrl);

    Http::fake(Http::response([
        [
            'type'    => 'team',
            'name'    => 'Hot Wallet',
            'address' => 'AagJoLEnpXYkxYdYkmdDSNMLjjBkLJ6T67',
        ],
    ], 200));

    $knownWallet = Wallet::factory()->create([
        'address' => 'AagJoLEnpXYkxYdYkmdDSNMLjjBkLJ6T67',
    ]);

    $regularWallet = Wallet::factory()->create([
        'attributes->delegate->username' => 'regular',
    ]);

    (new CacheDelegateUsernames())->handle();

    expect(Cache::tags('wallet')->get(md5("username_by_address/$knownWallet->address")))->toBe('Hot Wallet');
    expect(Cache::tags('wallet')->get(md5("username_by_address/$regularWallet->address")))->toBe('regular');
});

it('should cache the known wallet name if doesnt have delegate name', function () {
    $knownWalletsUrl = 'https://knownwallets.com/known-wallets.json';

    Config::set('explorer.networks.development.knownWallets', $knownWalletsUrl);

    Http::fake(Http::response([
        [
            'type'    => 'team',
            'name'    => 'Hot Wallet',
            'address' => 'AagJoLEnpXYkxYdYkmdDSNMLjjBkLJ6T67',
        ],
    ], 200));

    $wallet = Wallet::factory()->create([
        'address'                        => 'AagJoLEnpXYkxYdYkmdDSNMLjjBkLJ6T67',
        'attributes->delegate->username' => null,
    ]);

    (new CacheDelegateUsernames())->handle();

    expect(Cache::tags('wallet')->get(md5("username_by_address/$wallet->address")))->toBe('Hot Wallet');
});
