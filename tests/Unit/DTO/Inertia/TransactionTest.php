<?php

declare(strict_types=1);

use App\DTO\Inertia\Transaction as TransactionDTO;
use App\DTO\Inertia\Wallet as WalletDTO;
use App\Facades\Network;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Addresses\Legacy;
use App\Services\BigNumber;
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
        'url'                       => route('transaction', $transaction),
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
            'address'                           => $walletFrom->address,
            'attributes'                        => $walletFrom->attributes,
            'balance'                           => (string) $walletFrom->balance,
            'nonce'                             => (string) $walletFrom->nonce,
            'public_key'                        => $walletFrom->public_key,
            'isActive'                          => false,
            'isCold'                            => false,
            'isValidator'                       => false,
            'isLegacy'                          => true,
            'isDormant'                         => false,
            'legacyAddress'                     => Legacy::generateAddressFromPublicKey($walletFrom->public_key),
            'username'                          => 'joe.blogs',
            'vote'                              => null,
            'votes'                             => '0',
            'productivity'                      => 0.0,
            'formattedBalanceTwoDecimals'       => '100.34 DARK',
            'formattedBalanceFull'              => '100.34123 DARK',
            'formattedBalanceFullWithoutSuffix' => '100.34123',
            'fiatValue'                         => '$200.68',
            'totalForged'                       => '0',
            'hasUsername'                       => true,
            'isKnown'                           => false,
            'isOwnedByExchange'                 => false,
            'hasSecondSignature'                => false,
            'balancePercentage'                 => 0.0,
            'isResigned'                        => false,
            'voteUrl'                           => null,
            'votePercentage'                    => null,
        ],
        'recipient' => [
            'address'                           => $walletTo->address,
            'attributes'                        => $walletTo->attributes,
            'balance'                           => (string) $walletTo->balance,
            'nonce'                             => (string) $walletTo->nonce,
            'public_key'                        => $walletTo->public_key,
            'isActive'                          => false,
            'isCold'                            => false,
            'isValidator'                       => false,
            'isLegacy'                          => false,
            'isDormant'                         => false,
            'legacyAddress'                     => null,
            'username'                          => 'bill.ding',
            'vote'                              => null,
            'votes'                             => '0',
            'productivity'                      => 0.0,
            'formattedBalanceTwoDecimals'       => '50.34 DARK',
            'formattedBalanceFull'              => '50.34123 DARK',
            'formattedBalanceFullWithoutSuffix' => '50.34123',
            'fiatValue'                         => '$100.68',
            'totalForged'                       => '0',
            'hasUsername'                       => true,
            'isKnown'                           => false,
            'isOwnedByExchange'                 => false,
            'hasSecondSignature'                => false,
            'balancePercentage'                 => 0.0,
            'isResigned'                        => false,
            'voteUrl'                           => null,
            'votePercentage'                    => null,
        ],
        'votedForUsername'                => null,
        'isApprove'                       => false,
        'isApprovalRevoke'                => false,
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
        'url'                       => route('transaction', $transaction),
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
            'address'                           => $walletFrom->address,
            'attributes'                        => $walletFrom->attributes,
            'balance'                           => (string) $walletFrom->balance,
            'nonce'                             => (string) $walletFrom->nonce,
            'public_key'                        => $walletFrom->public_key,
            'isActive'                          => false,
            'isCold'                            => false,
            'isValidator'                       => false,
            'isLegacy'                          => true,
            'isDormant'                         => false,
            'legacyAddress'                     => Legacy::generateAddressFromPublicKey($walletFrom->public_key),
            'username'                          => 'joe.blogs',
            'vote'                              => null,
            'votes'                             => '0',
            'productivity'                      => 0.0,
            'formattedBalanceTwoDecimals'       => '100.34 DARK',
            'formattedBalanceFull'              => '100.34123 DARK',
            'formattedBalanceFullWithoutSuffix' => '100.34123',
            'fiatValue'                         => '$200.68',
            'totalForged'                       => '0',
            'hasUsername'                       => true,
            'isKnown'                           => false,
            'isOwnedByExchange'                 => false,
            'hasSecondSignature'                => false,
            'balancePercentage'                 => 0.0,
            'isResigned'                        => false,
            'voteUrl'                           => null,
            'votePercentage'                    => null,
        ],
        'recipient'                       => WalletDTO::stub(Network::knownContract('consensus'))->toArray(),
        'votedForUsername'                => 'bill.ding',
        'isApprove'                       => false,
        'isApprovalRevoke'                => false,
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
        'url'                       => route('transaction', $transaction),
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
            'url'                       => route('transaction', $registrationTransaction),
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
                'address'                           => $walletFrom->address,
                'attributes'                        => $walletFrom->attributes,
                'balance'                           => (string) $walletFrom->balance,
                'nonce'                             => (string) $walletFrom->nonce,
                'public_key'                        => $walletFrom->public_key,
                'isActive'                          => false,
                'isCold'                            => false,
                'isValidator'                       => false,
                'isLegacy'                          => true,
                'isDormant'                         => false,
                'legacyAddress'                     => Legacy::generateAddressFromPublicKey($walletFrom->public_key),
                'username'                          => 'joe.blogs',
                'vote'                              => null,
                'votes'                             => '0',
                'productivity'                      => 0.0,
                'formattedBalanceTwoDecimals'       => '100.34 DARK',
                'formattedBalanceFull'              => '100.34123 DARK',
                'formattedBalanceFullWithoutSuffix' => '100.34123',
                'fiatValue'                         => '$200.68',
                'totalForged'                       => '0',
                'hasUsername'                       => true,
                'isKnown'                           => false,
                'isOwnedByExchange'                 => false,
                'hasSecondSignature'                => false,
                'balancePercentage'                 => 0.0,
                'isResigned'                        => false,
                'voteUrl'                           => null,
                'votePercentage'                    => null,
            ],
            'recipient'                       => WalletDTO::stub(Network::knownContract('consensus'))->toArray(),
            'votedForUsername'                => null,
            'isApprove'                       => false,
            'isApprovalRevoke'                => false,
        ],
        'votedFor' => null,
        'sender'   => [
            'address'                           => $walletFrom->address,
            'attributes'                        => $walletFrom->attributes,
            'balance'                           => (string) $walletFrom->balance,
            'nonce'                             => (string) $walletFrom->nonce,
            'public_key'                        => $walletFrom->public_key,
            'isActive'                          => false,
            'isCold'                            => false,
            'isValidator'                       => false,
            'isLegacy'                          => true,
            'isDormant'                         => false,
            'legacyAddress'                     => Legacy::generateAddressFromPublicKey($walletFrom->public_key),
            'username'                          => 'joe.blogs',
            'vote'                              => null,
            'votes'                             => '0',
            'productivity'                      => 0.0,
            'formattedBalanceTwoDecimals'       => '100.34 DARK',
            'formattedBalanceFull'              => '100.34123 DARK',
            'formattedBalanceFullWithoutSuffix' => '100.34123',
            'fiatValue'                         => '$200.68',
            'totalForged'                       => '0',
            'hasUsername'                       => true,
            'isKnown'                           => false,
            'isOwnedByExchange'                 => false,
            'hasSecondSignature'                => false,
            'balancePercentage'                 => 0.0,
            'isResigned'                        => false,
            'voteUrl'                           => null,
            'votePercentage'                    => null,
        ],
        'recipient'                       => WalletDTO::stub(Network::knownContract('consensus'))->toArray(),
        'votedForUsername'                => null,
        'isApprove'                       => false,
        'isApprovalRevoke'                => false,
    ]);
});

it('should handle token transfer with non-existent recipient wallet', function () {
    $this->freezeTime();
    $this->travelTo('2025-09-11 12:00:00');

    $walletFrom = Wallet::factory()
        ->create([
            'balance'    => 100.34123 * 1e18,
            'attributes' => [
                'username' => 'joe.blogs',
            ],
        ]);

    // Use an address that does NOT exist in the wallets table
    $nonExistentRecipientAddress = '0x448c9672dc0DD62188064360c704822eCB6b9Fb4';
    $nonExistentContractAddress  = '0xTokenContractAddress000000000000000000000000';

    $transaction = Transaction::factory()
        ->tokenTransfer($nonExistentRecipientAddress, BigNumber::new(1000))
        ->create([
            'nonce'             => 123,
            'value'             => 0,
            'transaction_index' => 13,
            'sender_public_key' => $walletFrom->public_key,
            'from'              => $walletFrom->address,
            'to'                => $nonExistentContractAddress,
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

    // Should not throw an exception
    $subject = TransactionDTO::fromModel($transaction);

    expect($subject->isTokenTransfer)->toBeTrue();
    expect($subject->recipient)->not->toBeNull();
    expect($subject->recipient->address)->toBe($nonExistentContractAddress);
    expect($subject->sender)->not->toBeNull();
});

it('should handle transfer with non-existent recipient wallet', function () {
    $this->freezeTime();
    $this->travelTo('2025-09-11 12:00:00');

    $walletFrom = Wallet::factory()
        ->create([
            'balance'    => 100.34123 * 1e18,
            'attributes' => [
                'username' => 'joe.blogs',
            ],
        ]);

    // Use an address that does NOT exist in the wallets table
    $nonExistentRecipientAddress = '0x448c9672dc0DD62188064360c704822eCB6b9Fb4';

    $transaction = Transaction::factory()
        ->create([
            'nonce'             => 123,
            'value'             => 10 * 1e18,
            'transaction_index' => 13,
            'sender_public_key' => $walletFrom->public_key,
            'from'              => $walletFrom->address,
            'to'                => $nonExistentRecipientAddress,
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

    // Should not throw an exception
    $subject = TransactionDTO::fromModel($transaction);

    expect($subject->isTransfer)->toBeTrue();
    expect($subject->recipient)->not->toBeNull();
    expect($subject->recipient->address)->toBe($nonExistentRecipientAddress);
    expect($subject->sender)->not->toBeNull();
});

it('should determine an approval transaction', function () {
    $this->freezeTime();
    $this->travelTo('2025-09-11 12:00:00');

    $wallet = Wallet::factory()
        ->create([
            'address'    => '0x448c9672dc0DD62188064360c704822eCB6b9Fb4',
            'balance'    => 100.34123 * 1e18,
            'attributes' => [
                'username' => 'joe.blogs',
            ],
        ]);

    $transaction = Transaction::factory()
        ->approve($wallet->address, BigNumber::new(1000))
        ->create();

    $subject = TransactionDTO::fromModel($transaction);

    expect($subject->isApprove)->toBeTrue();
    expect($subject->isApprovalRevoke)->toBeFalse();
});

it('should determine an approval revoke transaction', function () {
    $this->freezeTime();
    $this->travelTo('2025-09-11 12:00:00');

    $wallet = Wallet::factory()
        ->create([
            'address'    => '0x448c9672dc0DD62188064360c704822eCB6b9Fb4',
            'balance'    => 100.34123 * 1e18,
            'attributes' => [
                'username' => 'joe.blogs',
            ],
        ]);

    $transaction = Transaction::factory()
        ->approve($wallet->address, BigNumber::zero())
        ->create();

    $subject = TransactionDTO::fromModel($transaction);

    expect($subject->isApprove)->toBeTrue();
    expect($subject->isApprovalRevoke)->toBeTrue();
});
