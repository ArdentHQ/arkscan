<?php

declare(strict_types=1);

use App\Console\Commands\CacheKnownWallets;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Cache\WalletCache;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

it('should cache the known wallet name if defined', function () {
    $knownWalletsUrl = 'https://knownwallets.com/known-wallets.json';

    Config::set('arkscan.networks.development.knownWallets', $knownWalletsUrl);

    Http::fake(Http::response([
        [
            'type'    => 'team',
            'name'    => 'Hot Wallet',
            'address' => '0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B',
        ],
    ], 200));

    $knownWallet = Wallet::factory()->create([
        'address' => '0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B',
    ]);

    (new CacheKnownWallets())->handle();

    expect(Cache::tags('wallet')->get(md5("name_by_address/$knownWallet->address")))->toBe('Hot Wallet');
});

it('should cache the username for the public key', function () {
    $wallet = Wallet::factory()->create([
        'attributes->username' => 'testuser',
    ]);

    expect(Cache::tags('wallet')->has(md5("username_by_address/$wallet->address")))->toBeFalse();
    expect(Cache::tags('wallet')->has(md5("username_by_public_key/$wallet->public_key")))->toBeFalse();

    (new CacheKnownWallets())->handle();

    expect(Cache::tags('wallet')->has(md5("username_by_address/$wallet->address")))->toBeTrue();
    expect(Cache::tags('wallet')->has(md5("username_by_public_key/$wallet->public_key")))->toBeTrue();

    expect(Cache::tags('wallet')->get(md5("username_by_address/$wallet->address")))->toBeString();
    expect(Cache::tags('wallet')->get(md5("username_by_public_key/$wallet->public_key")))->toBeString();
});

it('should cache the known wallet name if defined', function () {
    $knownWalletsUrl = 'https://knownwallets.com/known-wallets.json';

    Config::set('arkscan.networks.development.knownWallets', $knownWalletsUrl);

    Http::fake(Http::response([
        [
            'type'    => 'team',
            'name'    => 'Hot Wallet',
            'address' => '0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B',
        ],
    ], 200));

    $knownWallet = Wallet::factory()->create([
        'address' => '0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B',
    ]);

    $regularWallet = Wallet::factory()->create([
        'attributes->username' => 'regular',
    ]);

    (new CacheKnownWallets())->handle();

    expect(Cache::tags('wallet')->get(md5("username_by_address/$knownWallet->address")))->toBe('Hot Wallet');
    expect(Cache::tags('wallet')->get(md5("username_by_address/$regularWallet->address")))->toBe('regular');
});

it('should cache the known wallet name if doesnt have validator name', function () {
    $knownWalletsUrl = 'https://knownwallets.com/known-wallets.json';

    Config::set('arkscan.networks.development.knownWallets', $knownWalletsUrl);

    Http::fake(Http::response([
        [
            'type'    => 'team',
            'name'    => 'Hot Wallet',
            'address' => '0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B',
        ],
    ], 200));

    $wallet = Wallet::factory()->create([
        'address'              => '0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B',
        'attributes->username' => null,
    ]);

    (new CacheKnownWallets())->handle();

    expect(Cache::tags('wallet')->get(md5("username_by_address/$wallet->address")))->toBe('Hot Wallet');
});

it('should forget wallets with resigned usernames', function () {
    $cache  = new WalletCache();
    $wallet = Wallet::factory()->create([
        'address'              => '0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B',
        'attributes->username' => 'joeblogs',
    ]);

    (new CacheKnownWallets())->handle();

    expect($cache->getWalletNameByAddress($wallet->address))->toBe('joeblogs');

    Transaction::factory()->usernameResignation()->create([
        'sender_public_key' => $wallet->public_key,
    ]);

    (new CacheKnownWallets())->handle();

    expect($cache->getWalletNameByAddress($wallet->address))->toBeNull();
});
