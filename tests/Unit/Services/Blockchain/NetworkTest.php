<?php

declare(strict_types=1);

use App\Services\Blockchain\Network;
use ArkEcosystem\Crypto\Networks\Devnet;
use ArkEcosystem\Crypto\Networks\Mainnet;
use BitWasp\Bitcoin\Network\Network as Bitwasp;
use Carbon\Carbon;
use function Tests\fakeKnownWallets;

it('should have all required properties', function (array $config) {
    fakeKnownWallets();

    $nethash = config('arkscan.networks.production.nethash');
    if ($config['alias'] === 'devnet') {
        $nethash = config('arkscan.networks.development.nethash');
    }

    $config['nethash'] = $nethash;

    $subject = new Network($config);

    expect($subject->name())->toBe($config['name']);
    expect($subject->alias())->toBe($config['alias']);
    expect($subject->api())->toBe($config['api']);
    expect($subject->explorerTitle())->toBe(config('app.name'));
    expect($subject->mainnetExplorerUrl())->toBe($config['mainnetExplorerUrl']);
    expect($subject->testnetExplorerUrl())->toBe($config['testnetExplorerUrl']);
    expect($subject->currency())->toBe($config['currency']);
    expect($subject->currencySymbol())->toBe($config['currencySymbol']);
    expect($subject->confirmations())->toBe($config['confirmations']);
    expect($subject->knownWallets())->toBeArray();
    expect($subject->canBeExchanged())->toBe($config['canBeExchanged']);
    expect($subject->epoch())->toBeInstanceOf(Carbon::class);
    expect($subject->validatorCount())->toBe($config['validatorCount']);
    expect($subject->blockTime())->toBe($config['blockTime']);
    expect($subject->blockReward())->toBe($config['blockReward']);
    expect($subject->config())->toBeInstanceOf(Bitwasp::class);
    expect($subject->nethash())->toBe($config['nethash']);
})->with([
    [[
        'name'                => 'ARK Public Network',
        'alias'               => 'mainnet',
        'currency'            => 'ARK',
        'api'                 => 'https://wallets.ark.io/api',
        'currencySymbol'      => 'Ѧ',
        'confirmations'       => 51,
        'knownWallets'        => 'https://raw.githubusercontent.com/ArkEcosystem/common/master/mainnet/known-wallets-extended.json',
        'canBeExchanged'      => true,
        'epoch'               => Mainnet::new()->epoch(),
        'validatorCount'      => 51,
        'blockTime'           => 8,
        'blockReward'         => 2,
        'base58Prefix'        => 23,
        'mainnetExplorerUrl'  => 'https://mainnet.ark.io/',
        'testnetExplorerUrl'  => 'https://testnet.ark.io/',
    ]],
    [[
        'name'                => 'ARK Development Network',
        'alias'               => 'devnet',
        'api'                 => 'https://dwallets.ark.io/api',
        'currency'            => 'DARK',
        'currencySymbol'      => 'DѦ',
        'confirmations'       => 51,
        'canBeExchanged'      => false,
        'epoch'               => Devnet::new()->epoch(),
        'validatorCount'      => 51,
        'blockTime'           => 8,
        'blockReward'         => 2,
        'base58Prefix'        => 30,
        'mainnetExplorerUrl'  => 'https://mainnet.dark.io/',
        'testnetExplorerUrl'  => 'https://testnet.dark.io/',
    ]],
]);
