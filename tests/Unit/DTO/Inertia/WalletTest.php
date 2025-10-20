<?php

declare(strict_types=1);

use App\DTO\Inertia\Wallet as WalletDTO;
use App\Models\Wallet;
use App\Services\Addresses\Legacy;
use App\Services\BigNumber;
use App\Services\Cache\NetworkStatusBlockCache;
use App\Services\Cache\ValidatorCache;
use App\Services\Cache\WalletCache;

it('should make an instance for non-validators', function () {
    $this->freezeTime();
    $this->travelTo('2025-09-11 12:00:00');

    $wallet = Wallet::factory()
        ->create([
            'balance'    => 100.34123 * 1e18,
            'attributes' => [
                'username' => 'joe.blogs',
                'isLegacy' => true,
            ],
        ]);

    $subject = WalletDTO::fromModel($wallet);

    (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 2.0);

    expect($subject->toArray())->toEqual([
        'address'                     => $wallet->address,
        'attributes'                  => $wallet->attributes,
        'balance'                     => (string) $wallet->balance,
        'nonce'                       => (string) $wallet->nonce,
        'public_key'                  => $wallet->public_key,
        'isActive'                    => false,
        'isCold'                      => false,
        'isValidator'                 => false,
        'isLegacy'                    => true,
        'isDormant'                   => false,
        'legacyAddress'               => Legacy::generateAddressFromPublicKey($wallet->public_key),
        'username'                    => 'joe.blogs',
        'vote'                        => null,
        'votes'                       => '0',
        'productivity'                => 0.0,
        'formattedBalanceTwoDecimals' => '100.34 DARK',
        'formattedBalanceFull'        => '100.34123 DARK',
        'fiatValue'                   => '$200.68',
        'totalForged'                 => '0',
        'isResigned'                  => false,
    ]);
});

it('should make an instance for active validators', function () {
    $this->freezeTime();
    $this->travelTo('2025-09-11 12:00:00');

    $wallet = Wallet::factory()
        ->activeValidator()
        ->create([
            'balance' => 100.34123 * 1e18,
        ]);

    $subject = WalletDTO::fromModel($wallet);

    (new WalletCache())->setProductivity($wallet->address, 1.23);
    (new ValidatorCache())->setTotalFees([$wallet->address => 1.23 * 1e18]);
    (new ValidatorCache())->setTotalRewards([$wallet->address => 1.23 * 1e18]);
    (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 2.0);

    expect($subject->toArray())->toEqual([
        'address'                     => $wallet->address,
        'attributes'                  => $wallet->attributes,
        'balance'                     => (string) $wallet->balance,
        'nonce'                       => (string) $wallet->nonce,
        'public_key'                  => $wallet->public_key,
        'isActive'                    => true,
        'isCold'                      => false,
        'isValidator'                 => true,
        'isLegacy'                    => false,
        'isDormant'                   => false,
        'legacyAddress'               => null,
        'username'                    => null,
        'vote'                        => null,
        'votes'                       => (string) BigNumber::new($wallet->attributes['validatorVoteBalance'] ?? 0)->toFloat(),
        'productivity'                => 1.23,
        'formattedBalanceTwoDecimals' => '100.34 DARK',
        'formattedBalanceFull'        => '100.34123 DARK',
        'fiatValue'                   => '$200.68',
        'totalForged'                 => '2.46',
        'isResigned'                  => false,
    ]);
});

it('should make an instance for standby validators', function () {
    $this->freezeTime();
    $this->travelTo('2025-09-11 12:00:00');

    $wallet = Wallet::factory()
        ->standbyValidator()
        ->create([
            'balance' => 100.34123 * 1e18,
        ]);

    $subject = WalletDTO::fromModel($wallet);

    (new WalletCache())->setProductivity($wallet->address, 1.23);
    (new ValidatorCache())->setTotalFees([$wallet->address => 1.23 * 1e18]);
    (new ValidatorCache())->setTotalRewards([$wallet->address => 1.23 * 1e18]);
    (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 2.0);

    expect($subject->toArray())->toEqual([
        'address'                     => $wallet->address,
        'attributes'                  => $wallet->attributes,
        'balance'                     => (string) $wallet->balance,
        'nonce'                       => (string) $wallet->nonce,
        'public_key'                  => $wallet->public_key,
        'isActive'                    => false,
        'isCold'                      => false,
        'isValidator'                 => true,
        'isLegacy'                    => false,
        'isDormant'                   => false,
        'legacyAddress'               => null,
        'username'                    => null,
        'vote'                        => null,
        'votes'                       => (string) BigNumber::new($wallet->attributes['validatorVoteBalance'] ?? 0)->toFloat(),
        'productivity'                => 1.23,
        'formattedBalanceTwoDecimals' => '100.34 DARK',
        'formattedBalanceFull'        => '100.34123 DARK',
        'fiatValue'                   => '$200.68',
        'totalForged'                 => '2.46',
        'isResigned'                  => true,
    ]);
});

it('should make an instance for a voting wallet', function () {
    $this->freezeTime();
    $this->travelTo('2025-09-11 12:00:00');

    $votedWallet = Wallet::factory()
        ->activeValidator()
        ->create([
            'balance' => 100.34123 * 1e18,
        ]);

    $wallet = Wallet::factory()
        ->create([
            'balance'    => 100.34123 * 1e18,
            'attributes' => [
                'username' => 'joe.blogs',
                'vote'     => $votedWallet->address,
            ],
        ]);

    $subject = WalletDTO::fromModel($wallet);

    (new WalletCache())->setVote($votedWallet->address, $votedWallet);
    (new WalletCache())->setProductivity($votedWallet->address, 1.23);
    (new ValidatorCache())->setTotalFees([$votedWallet->address => 1.23 * 1e18]);
    (new ValidatorCache())->setTotalRewards([$votedWallet->address => 1.23 * 1e18]);
    (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 2.0);

    expect($subject->toArray())->toEqual([
        'address'                     => $wallet->address,
        'attributes'                  => $wallet->attributes,
        'balance'                     => (string) $wallet->balance,
        'nonce'                       => (string) $wallet->nonce,
        'public_key'                  => $wallet->public_key,
        'isActive'                    => false,
        'isCold'                      => false,
        'isValidator'                 => false,
        'isLegacy'                    => false,
        'isDormant'                   => false,
        'legacyAddress'               => null,
        'username'                    => 'joe.blogs',
        'vote'                        => [
            'address'                     => $votedWallet->address,
            'attributes'                  => $votedWallet->attributes,
            'balance'                     => (string) $votedWallet->balance,
            'nonce'                       => (string) $votedWallet->nonce,
            'public_key'                  => $votedWallet->public_key,
            'isActive'                    => true,
            'isCold'                      => false,
            'isValidator'                 => true,
            'isLegacy'                    => false,
            'isDormant'                   => false,
            'legacyAddress'               => null,
            'username'                    => null,
            'vote'                        => null,
            'votes'                       => (string) BigNumber::new($votedWallet->attributes['validatorVoteBalance'] ?? 0)->toFloat(),
            'productivity'                => 1.23,
            'formattedBalanceTwoDecimals' => '100.34 DARK',
            'formattedBalanceFull'        => '100.34123 DARK',
            'fiatValue'                   => '$200.68',
            'totalForged'                 => '2.46',
            'isResigned'                  => false,
        ],
        'votes'                       => '0',
        'productivity'                => 0.0,
        'formattedBalanceTwoDecimals' => '100.34 DARK',
        'formattedBalanceFull'        => '100.34123 DARK',
        'fiatValue'                   => '$200.68',
        'totalForged'                 => '0',
        'isResigned'                  => false,
    ]);
});
