<?php

declare(strict_types=1);

use App\DTO\Inertia\Transaction as TransactionDTO;
use App\Facades\Network;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Addresses\Legacy;
use App\Services\Cache\CryptoDataCache;
use App\Services\Cache\NetworkStatusBlockCache;
use App\ViewModels\TransactionViewModel;
use Carbon\Carbon;

it('should make an instance', function () {
    $this->freezeTime();
    $this->travelTo('2025-09-11 12:00:00');

    $walletFrom = Wallet::factory()
        ->create([
            'balance'    => 100.34123 * 1e18,
            'attributes' => [
                'username' => 'joe.blogs',
                'isLegacy' => true,
            ],
        ]);

    $walletTo = Wallet::factory()
        ->create([
            'balance'    => 50.34123 * 1e18,
            'attributes' => [
                'username' => 'bill.ding',
            ],
        ]);

    $transaction = Transaction::factory()
        ->create([
            'nonce'             => 123,
            'value'             => 10 * 1e18,
            'transaction_index' => 13,
            'sender_public_key' => $walletFrom->public_key,
            'from'              => $walletFrom->address,
            'to'                => $walletTo->address,
            'gas_price'         => 20,
            'gas'               => 21000,
            'gas_used'          => 21000,
            'gas_refunded'      => 0,
            'status'            => true,
            'block_number'      => 54321,
            'block_hash'        => '0000000000000000000000000000000000000000000000000000000000054321',
            'timestamp'         => 1603083256000,
        ]);

    $viewModel = new TransactionViewModel($transaction);

    (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 2.0);
    (new CryptoDataCache())->setPrices('USD.week', collect([
        Carbon::parse($viewModel->timestamp())->format('Y-m-d') => 2.0,
    ]));

    $subject = TransactionDTO::fromModel($transaction);

    expect($subject->toArray())->toEqual([
        'hash'                      => $transaction->hash,
        'block_hash'                => '0000000000000000000000000000000000000000000000000000000000054321',
        'block_number'              => 54321,
        'transaction_index'         => 13,
        'timestamp'                 => 1603083256,
        'nonce'                     => 123,
        'sender_public_key'         => $walletFrom->public_key,
        'from'                      => $walletFrom->address,
        'to'                        => $walletTo->address,
        'value'                     => '10000000000000000000',
        'gas_price'                 => '20',
        'gas'                       => '21000',
        'status'                    => true,
        'gas_used'                  => '21000',
        'gas_refunded'              => '0',
        'deployed_contract_address' => null,
        'decoded_error'             => null,
        'multi_payment_recipients'  => [],
        'amount'                    => 10.0,
        'amountForItself'           => 0.0,
        'amountExcludingItself'     => 0.0,
        'amountWithFee'             => 10.00000000000042,
        'amountReceived'            => 10.0,
        'amountFiat'                => '$20.00',
        'amountReceivedFiat'        => '$20.00',
        'fee'                       => 0.00000000000042,
        'feeFiat'                   => '$0.0000',
        'type'                      => 'Transfer',
        'isTransfer'                => true,
        'isTokenTransfer'           => false,
        'isVote'                    => false,
        'isUnvote'                  => false,
        'isValidatorRegistration'   => false,
        'isValidatorResignation'    => false,
        'isValidatorUpdate'         => false,
        'isUsernameRegistration'    => false,
        'isUsernameResignation'     => false,
        'isContractDeployment'      => false,
        'isMultiPayment'            => false,
        'isSelfReceiving'           => false,
        'isSent'                    => true,
        'isSentToSelf'              => false,
        'isReceived'                => false,
        'hasFailedStatus'           => false,
        'validatorRegistration'     => null,
        'votedFor'                  => null,
        'sender'                    => [
            'address'                     => $walletFrom->address,
            'attributes'                  => $walletFrom->attributes,
            'balance'                     => (string) $walletFrom->balance,
            'nonce'                       => (string) $walletFrom->nonce,
            'public_key'                  => $walletFrom->public_key,
            'isActive'                    => false,
            'isCold'                      => false,
            'isValidator'                 => false,
            'isLegacy'                    => true,
            'isDormant'                   => false,
            'legacyAddress'               => Legacy::generateAddressFromPublicKey($walletFrom->public_key),
            'username'                    => 'joe.blogs',
            'vote'                        => null,
            'votes'                       => '0',
            'productivity'                => 0.0,
            'formattedBalanceTwoDecimals' => '100.34 DARK',
            'formattedBalanceFull'        => '100.34123 DARK',
            'fiatValue'                   => '$200.68',
            'totalForged'                 => '0',
            'hasUsername'                 => true,
            'isResigned'                  => false,
        ],
        'recipient' => [
            'address'                     => $walletTo->address,
            'attributes'                  => $walletTo->attributes,
            'balance'                     => (string) $walletTo->balance,
            'nonce'                       => (string) $walletTo->nonce,
            'public_key'                  => $walletTo->public_key,
            'isActive'                    => false,
            'isCold'                      => false,
            'isValidator'                 => false,
            'isLegacy'                    => false,
            'isDormant'                   => false,
            'legacyAddress'               => null,
            'username'                    => 'bill.ding',
            'vote'                        => null,
            'votes'                       => '0',
            'productivity'                => 0.0,
            'formattedBalanceTwoDecimals' => '50.34 DARK',
            'formattedBalanceFull'        => '50.34123 DARK',
            'fiatValue'                   => '$100.68',
            'totalForged'                 => '0',
            'hasUsername'                 => true,
            'isResigned'                  => false,
        ],
    ]);
});

it('should make an instance for a vote transaction', function () {
    $this->freezeTime();
    $this->travelTo('2025-09-11 12:00:00');

    $walletFrom = Wallet::factory()
        ->create([
            'balance'    => 100.34123 * 1e18,
            'attributes' => [
                'username' => 'joe.blogs',
                'isLegacy' => true,
            ],
        ]);

    $walletTo = Wallet::factory()
        ->activeValidator()
        ->create([
            'balance'    => 50.34123 * 1e18,
            'attributes' => [
                'username' => 'bill.ding',
            ],
        ]);

    $transaction = Transaction::factory()
        ->vote($walletTo->address)
        ->create([
            'nonce'             => 123,
            'value'             => 0,
            'transaction_index' => 13,
            'sender_public_key' => $walletFrom->public_key,
            'from'              => $walletFrom->address,
            'gas_price'         => 20,
            'gas'               => 21000,
            'gas_used'          => 21000,
            'gas_refunded'      => 0,
            'status'            => true,
            'block_number'      => 54321,
            'block_hash'        => '0000000000000000000000000000000000000000000000000000000000054321',
            'timestamp'         => 1603083256000,
        ]);

    $viewModel = new TransactionViewModel($transaction);

    (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 2.0);
    (new CryptoDataCache())->setPrices('USD.week', collect([
        Carbon::parse($viewModel->timestamp())->format('Y-m-d') => 2.0,
    ]));

    $subject = TransactionDTO::fromModel($transaction);

    expect($subject->toArray())->toEqual([
        'hash'                      => $transaction->hash,
        'block_hash'                => '0000000000000000000000000000000000000000000000000000000000054321',
        'block_number'              => 54321,
        'transaction_index'         => 13,
        'timestamp'                 => 1603083256,
        'nonce'                     => 123,
        'sender_public_key'         => $walletFrom->public_key,
        'from'                      => $walletFrom->address,
        'to'                        => Network::knownContract('consensus'),
        'value'                     => '0',
        'gas_price'                 => '20',
        'gas'                       => '21000',
        'status'                    => true,
        'gas_used'                  => '21000',
        'gas_refunded'              => '0',
        'deployed_contract_address' => null,
        'decoded_error'             => null,
        'multi_payment_recipients'  => [],
        'amount'                    => 0.0,
        'amountForItself'           => 0.0,
        'amountExcludingItself'     => 0.0,
        'amountWithFee'             => 0.00000000000042,
        'amountReceived'            => 0.0,
        'amountFiat'                => '$0.0000',
        'amountReceivedFiat'        => '$0.00',
        'fee'                       => 0.00000000000042,
        'feeFiat'                   => '$0.0000',
        'type'                      => 'Vote',
        'isTransfer'                => false,
        'isTokenTransfer'           => false,
        'isVote'                    => true,
        'isUnvote'                  => false,
        'isValidatorRegistration'   => false,
        'isValidatorResignation'    => false,
        'isValidatorUpdate'         => false,
        'isUsernameRegistration'    => false,
        'isUsernameResignation'     => false,
        'isContractDeployment'      => false,
        'isMultiPayment'            => false,
        'isSelfReceiving'           => true,
        'isSent'                    => true,
        'isSentToSelf'              => false,
        'isReceived'                => false,
        'hasFailedStatus'           => false,
        'validatorRegistration'     => null,
        'votedFor'                  => $walletTo->address,
        'sender'                    => [
            'address'                     => $walletFrom->address,
            'attributes'                  => $walletFrom->attributes,
            'balance'                     => (string) $walletFrom->balance,
            'nonce'                       => (string) $walletFrom->nonce,
            'public_key'                  => $walletFrom->public_key,
            'isActive'                    => false,
            'isCold'                      => false,
            'isValidator'                 => false,
            'isLegacy'                    => true,
            'isDormant'                   => false,
            'legacyAddress'               => Legacy::generateAddressFromPublicKey($walletFrom->public_key),
            'username'                    => 'joe.blogs',
            'vote'                        => null,
            'votes'                       => '0',
            'productivity'                => 0.0,
            'formattedBalanceTwoDecimals' => '100.34 DARK',
            'formattedBalanceFull'        => '100.34123 DARK',
            'fiatValue'                   => '$200.68',
            'totalForged'                 => '0',
            'hasUsername'                 => true,
            'isResigned'                  => false,
        ],
        'recipient' => null,
    ]);
});

it('should make an instance for a validator resignation transaction', function () {
    $this->freezeTime();
    $this->travelTo('2025-09-11 12:00:00');

    $walletFrom = Wallet::factory()
        ->create([
            'balance'    => 100.34123 * 1e18,
            'attributes' => [
                'username' => 'joe.blogs',
                'isLegacy' => true,
            ],
        ]);

    $registrationTransaction = Transaction::factory()
        ->validatorRegistration()
        ->create([
            'nonce'             => 122,
            'value'             => 250 * 1e18,
            'transaction_index' => 2,
            'sender_public_key' => $walletFrom->public_key,
            'from'              => $walletFrom->address,
            'gas_price'         => 20,
            'gas'               => 21000,
            'gas_used'          => 21000,
            'gas_refunded'      => 0,
            'status'            => true,
            'block_number'      => 54320,
            'block_hash'        => '0000000000000000000000000000000000000000000000000000000000054320',
            'timestamp'         => 1603083256000,
        ]);

    $transaction = Transaction::factory()
        ->validatorResignation()
        ->create([
            'nonce'             => 123,
            'value'             => 0,
            'transaction_index' => 13,
            'sender_public_key' => $walletFrom->public_key,
            'from'              => $walletFrom->address,
            'gas_price'         => 20,
            'gas'               => 21000,
            'gas_used'          => 21000,
            'gas_refunded'      => 0,
            'status'            => true,
            'block_number'      => 54321,
            'block_hash'        => '0000000000000000000000000000000000000000000000000000000000054321',
            'timestamp'         => 1603083256000,
        ]);

    $viewModel = new TransactionViewModel($transaction);

    (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 2.0);
    (new CryptoDataCache())->setPrices('USD.week', collect([
        Carbon::parse($viewModel->timestamp())->format('Y-m-d') => 2.0,
    ]));

    $subject = TransactionDTO::fromModel($transaction);

    expect($subject->toArray())->toEqual([
        'hash'                      => $transaction->hash,
        'block_hash'                => '0000000000000000000000000000000000000000000000000000000000054321',
        'block_number'              => 54321,
        'transaction_index'         => 13,
        'timestamp'                 => 1603083256,
        'nonce'                     => 123,
        'sender_public_key'         => $walletFrom->public_key,
        'from'                      => $walletFrom->address,
        'to'                        => Network::knownContract('consensus'),
        'value'                     => '0',
        'gas_price'                 => '20',
        'gas'                       => '21000',
        'status'                    => true,
        'gas_used'                  => '21000',
        'gas_refunded'              => '0',
        'deployed_contract_address' => null,
        'decoded_error'             => null,
        'multi_payment_recipients'  => [],
        'amount'                    => 0.0,
        'amountForItself'           => 0.0,
        'amountExcludingItself'     => 0.0,
        'amountWithFee'             => 0.00000000000042,
        'amountReceived'            => 0.0,
        'amountFiat'                => '$0.0000',
        'amountReceivedFiat'        => '$0.00',
        'fee'                       => 0.00000000000042,
        'feeFiat'                   => '$0.0000',
        'type'                      => 'Validator Resignation',
        'isTransfer'                => false,
        'isTokenTransfer'           => false,
        'isVote'                    => false,
        'isUnvote'                  => false,
        'isValidatorRegistration'   => false,
        'isValidatorResignation'    => true,
        'isValidatorUpdate'         => false,
        'isUsernameRegistration'    => false,
        'isUsernameResignation'     => false,
        'isContractDeployment'      => false,
        'isMultiPayment'            => false,
        'isSelfReceiving'           => true,
        'isSent'                    => true,
        'isSentToSelf'              => false,
        'isReceived'                => false,
        'hasFailedStatus'           => false,
        'validatorRegistration'     => [
            'hash'                      => $registrationTransaction->hash,
            'block_hash'                => '0000000000000000000000000000000000000000000000000000000000054320',
            'block_number'              => 54320,
            'transaction_index'         => 2,
            'timestamp'                 => 1603083256,
            'nonce'                     => 122,
            'sender_public_key'         => $walletFrom->public_key,
            'from'                      => $walletFrom->address,
            'to'                        => Network::knownContract('consensus'),
            'value'                     => '250000000000000000000',
            'gas_price'                 => '20',
            'gas'                       => '21000',
            'status'                    => true,
            'gas_used'                  => '21000',
            'gas_refunded'              => '0',
            'deployed_contract_address' => null,
            'decoded_error'             => null,
            'multi_payment_recipients'  => [],
            'amount'                    => 250.0,
            'amountForItself'           => 0.0,
            'amountExcludingItself'     => 0.0,
            'amountWithFee'             => 250.00000000000042,
            'amountReceived'            => 250.0,
            'amountFiat'                => '$500.00',
            'amountReceivedFiat'        => '$500.00',
            'fee'                       => 0.00000000000042,
            'feeFiat'                   => '$0.0000',
            'type'                      => 'Validator Registration',
            'isTransfer'                => false,
            'isTokenTransfer'           => false,
            'isVote'                    => false,
            'isUnvote'                  => false,
            'isValidatorRegistration'   => true,
            'isValidatorResignation'    => false,
            'isValidatorUpdate'         => false,
            'isUsernameRegistration'    => false,
            'isUsernameResignation'     => false,
            'isContractDeployment'      => false,
            'isMultiPayment'            => false,
            'isSelfReceiving'           => true,
            'isSent'                    => true,
            'isSentToSelf'              => false,
            'isReceived'                => false,
            'hasFailedStatus'           => false,
            'validatorRegistration'     => null,
            'votedFor'                  => null,
            'sender'                    => [
                'address'                     => $walletFrom->address,
                'attributes'                  => $walletFrom->attributes,
                'balance'                     => (string) $walletFrom->balance,
                'nonce'                       => (string) $walletFrom->nonce,
                'public_key'                  => $walletFrom->public_key,
                'isActive'                    => false,
                'isCold'                      => false,
                'isValidator'                 => false,
                'isLegacy'                    => true,
                'isDormant'                   => false,
                'legacyAddress'               => Legacy::generateAddressFromPublicKey($walletFrom->public_key),
                'username'                    => 'joe.blogs',
                'vote'                        => null,
                'votes'                       => '0',
                'productivity'                => 0.0,
                'formattedBalanceTwoDecimals' => '100.34 DARK',
                'formattedBalanceFull'        => '100.34123 DARK',
                'fiatValue'                   => '$200.68',
                'totalForged'                 => '0',
                'hasUsername'                 => true,
                'isResigned'                  => false,
            ],
            'recipient' => null,
        ],
        'votedFor' => null,
        'sender'   => [
            'address'                     => $walletFrom->address,
            'attributes'                  => $walletFrom->attributes,
            'balance'                     => (string) $walletFrom->balance,
            'nonce'                       => (string) $walletFrom->nonce,
            'public_key'                  => $walletFrom->public_key,
            'isActive'                    => false,
            'isCold'                      => false,
            'isValidator'                 => false,
            'isLegacy'                    => true,
            'isDormant'                   => false,
            'legacyAddress'               => Legacy::generateAddressFromPublicKey($walletFrom->public_key),
            'username'                    => 'joe.blogs',
            'vote'                        => null,
            'votes'                       => '0',
            'productivity'                => 0.0,
            'formattedBalanceTwoDecimals' => '100.34 DARK',
            'formattedBalanceFull'        => '100.34123 DARK',
            'fiatValue'                   => '$200.68',
            'totalForged'                 => '0',
            'hasUsername'                 => true,
            'isResigned'                  => false,
        ],
        'recipient' => null,
    ]);
});
