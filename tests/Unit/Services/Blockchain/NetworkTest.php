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
    expect($subject->delegateCount())->toBe($config['delegateCount']);
    expect($subject->blockTime())->toBe($config['blockTime']);
    expect($subject->blockReward())->toBe($config['blockReward']);
    expect($subject->config())->toBeInstanceOf(Bitwasp::class);
    expect($subject->nethash())->toBe($config['nethash']);
})->with([
    [[
        'name'             => 'ARK Public Network',
        'alias'            => 'mainnet',
        'currency'         => 'ARK',
        'api'              => 'https://wallets.ark.io/api',
        'currencySymbol'   => 'Ѧ',
        'confirmations'    => 51,
        'knownWallets'     => 'https://raw.githubusercontent.com/ArkEcosystem/common/master/mainnet/known-wallets-extended.json',
        'canBeExchanged'   => true,
        'epoch'            => Mainnet::new()->epoch(),
        'delegateCount'    => 51,
        'blockTime'        => 8,
        'blockReward'      => 2,
        'base58Prefix'     => 23,
        'nethash'          => '6e84d08bd299ed97c212c886c98a57e36545c8f5d645ca7eeae63a8bd62d8988',
        'mainnetExplorerUrl' => 'https://mainnet.ark.io/',
        'testnetExplorerUrl' => 'https://testnet.ark.io/',
    ]],
    [[
        'name'             => 'ARK Development Network',
        'alias'            => 'devnet',
        'api'              => 'https://dwallets.ark.io/api',
        'currency'         => 'DARK',
        'currencySymbol'   => 'DѦ',
        'confirmations'    => 51,
        'canBeExchanged'   => false,
        'epoch'            => Devnet::new()->epoch(),
        'delegateCount'    => 51,
        'blockTime'        => 8,
        'blockReward'      => 2,
        'base58Prefix'     => 30,
        'nethash'          => '2a44f340d76ffc3df204c5f38cd355b7496c9065a1ade2ef92071436bd72e867',
        'mainnetExplorerUrl' => 'https://mainnet.dark.io/',
        'testnetExplorerUrl' => 'https://testnet.dark.io/',
    ]],
]);
