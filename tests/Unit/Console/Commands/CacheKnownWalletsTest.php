<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Cache\WalletCache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

it('should cache the known wallet name if defined', function () {
    Config::set('arkscan.networks.development.knownWallets', 'https://knownwallets.com/known-wallets.json');

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

    Artisan::call('explorer:cache-known-wallets');

    expect(Cache::tags('wallet')->get(md5("name_by_address/$knownWallet->address")))->toBe('Hot Wallet');
    expect(Cache::tags('wallet')->get(md5("name_by_address/$regularWallet->address")))->toBe('regular');
});

it('should cache the known wallet name if doesnt have validator name', function () {
    Config::set('arkscan.networks.development.knownWallets', 'https://knownwallets.com/known-wallets.json');

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

    Artisan::call('explorer:cache-known-wallets');

    expect(Cache::tags('wallet')->get(md5("name_by_address/$wallet->address")))->toBe('Hot Wallet');
});

it('should forget wallets with resigned usernames', function () {
    Config::set('arkscan.networks.development.knownWallets', 'https://knownwallets.com/known-wallets.json');

    Http::fake(Http::response([
        [],
    ], 200));

    $cache  = new WalletCache();
    $wallet = Wallet::factory()->create([
        'address'    => '0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B',

        'attributes' => [
            'username' => 'joeblogs',
        ],
    ]);

    Artisan::call('explorer:cache-known-wallets');

    expect($cache->getWalletNameByAddress($wallet->address))->toBe('joeblogs');

    $wallet->fresh()->forceFill([
        'attributes' => [
            'username' => null,
        ],
    ])->save();

    Artisan::call('explorer:cache-known-wallets');

    expect($cache->getWalletNameByAddress($wallet->address))->toBe('joeblogs');

    Transaction::factory()->usernameResignation()->create([
        'from' => $wallet->address,
    ]);

    Artisan::call('explorer:cache-known-wallets');

    expect($cache->getWalletNameByAddress($wallet->address))->toBeNull();
});

it('should force update of known wallets', function () {
    Config::set('arkscan.networks.development.knownWallets', 'https://knownwallets.com/known-wallets.json');

    $cache = new WalletCache();
    $cache->setKnown(fn () => [
        '0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B',
    ]);

    Http::fake(Http::response([], 200));

    Artisan::call('explorer:cache-known-wallets');

    expect($cache->getKnown())->toBe([
        '0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B',
    ]);

    Artisan::call('explorer:cache-known-wallets', ['--force' => true]);

    expect($cache->getKnown())->toBe([]);
});
