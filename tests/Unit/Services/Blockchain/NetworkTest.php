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
    ]],
]);
