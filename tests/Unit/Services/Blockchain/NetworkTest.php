<?php

declare(strict_types=1);

use App\Models\State;
use App\Services\BigNumber;
use App\Services\Blockchain\Network;
use ArkEcosystem\Crypto\Networks\Mainnet;
use ArkEcosystem\Crypto\Networks\Testnet;
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
    expect($subject->legacyExplorerUrl())->toBe($config['legacyExplorerUrl']);
    expect($subject->currency())->toBe($config['currency']);
    expect($subject->currencySymbol())->toBe($config['currencySymbol']);
    expect($subject->confirmations())->toBe($config['confirmations']);
    expect($subject->knownWallets())->toBeArray();
    expect($subject->knownContracts())->toBeArray();
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
        'legacyExplorerUrl'   => 'https://legacy.ark.io/',
        'contract_addresses'  => [],
    ]],
    [[
        'name'                => 'ARK Development Network',
        'alias'               => 'devnet',
        'api'                 => 'https://dwallets.ark.io/api',
        'currency'            => 'DARK',
        'currencySymbol'      => 'DѦ',
        'confirmations'       => 51,
        'canBeExchanged'      => false,
        'epoch'               => Testnet::new()->epoch(),
        'validatorCount'      => 51,
        'blockTime'           => 8,
        'blockReward'         => 2,
        'base58Prefix'        => 30,
        'mainnetExplorerUrl'  => 'https://mainnet.dark.io/',
        'testnetExplorerUrl'  => 'https://testnet.dark.io/',
        'legacyExplorerUrl'   => 'https://legacy.dark.io/',
        'contract_addresses'  => [],
    ]],
]);

it('should return supply from first state', function () {
    State::factory()->create(['supply' => BigNumber::new(123 * 1e18)]);

    $subject = new Network([]);

    expect($subject->supply())->toBeInstanceOf(BigNumber::class);

    expect($subject->supply()->__toString())->toBe('123000000000000000000');
});

it('should return 0 as supply if no state', function () {
    $subject = new Network([]);

    expect($subject->supply())->toBeInstanceOf(BigNumber::class);

    expect($subject->supply()->toInt())->toBe(0);
});
