<?php

declare(strict_types=1);

use App\Console\Commands\CacheKnownWallets;
use App\Models\Wallet;
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
