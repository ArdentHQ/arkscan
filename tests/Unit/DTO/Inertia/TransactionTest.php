<?php

declare(strict_types=1);

use App\DTO\Inertia\Transaction as TransactionDTO;
use App\DTO\Inertia\WalletReference;
use App\Enums\ContractMethod;
use App\Facades\Network;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\Cache\CryptoDataCache;
use App\Services\Cache\NetworkStatusBlockCache;
use App\Services\ExchangeRate;
use App\ViewModels\TransactionViewModel;
use function Tests\fakeCryptoCompare;

function safeUtf8(string $value): string
{
    if (preg_match('//u', $value) === 1) {
        return $value;
    }

    $previousSubstitute = mb_substitute_character();
    mb_substitute_character(0xFFFD);
    $converted = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
    mb_substitute_character($previousSubstitute);

    return $converted;
}

function payloadDetails(TransactionViewModel $transaction): ?array
{
    if (! $transaction->hasPayload()) {
        return null;
    }

    return [
        'formatted' => safeUtf8($transaction->formattedPayload() ?? ''),
        'utf8'      => safeUtf8($transaction->utf8Payload() ?? ''),
        'raw'       => safeUtf8($transaction->rawPayload() ?? ''),
    ];
}

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

    (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 2.0);
    (new CryptoDataCache())->setPrices('USD.week', collect([
        $transaction->timestamp->format('Y-m-d') => 2.0,
    ]));

    $viewModel = new TransactionViewModel($transaction);
    $subject   = TransactionDTO::fromModel($transaction);

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
        'multiPaymentRecipients'    => [],
        'multiPaymentTotal'         => null,
        'exchangeRates'             => ExchangeRate::allCurrencyRates($transaction->timestamp),
        'url'                       => route('transaction', $transaction),
        'validatorRegistration'     => null,
        'votedFor'                  => null,
        'sender'                    => [
            'address'  => $walletFrom->address,
            'username' => 'joe.blogs',
        ],
        'recipient' => [
            'address'  => $walletTo->address,
            'username' => 'bill.ding',
        ],
        'votedForUsername'                => null,
        'tokenApprovalDetails'            => null,
        'methodData'                      => [
            'functionName' => null,
            'methodId'     => null,
            'arguments'    => null,
        ],
        'payload' => null,
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

    (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 2.0);
    (new CryptoDataCache())->setPrices('USD.week', collect([
        $transaction->timestamp->format('Y-m-d') => 2.0,
    ]));

    $viewModel = new TransactionViewModel($transaction);
    $subject   = TransactionDTO::fromModel($transaction);

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
        'multiPaymentRecipients'    => [],
        'multiPaymentTotal'         => null,
        'exchangeRates'             => ExchangeRate::allCurrencyRates($transaction->timestamp),
        'url'                       => route('transaction', $transaction),
        'validatorRegistration'     => null,
        'votedFor'                  => $walletTo->address,
        'sender'                    => [
            'address'  => $walletFrom->address,
            'username' => 'joe.blogs',
        ],
        'recipient'                       => WalletReference::stub(Network::knownContract('consensus'))->toArray(),
        'votedForUsername'                => 'bill.ding',
        'tokenApprovalDetails'            => null,
        'methodData'                      => [
            'functionName' => 'vote(address)',
            'methodId'     => ContractMethod::vote(),
            'arguments'    => [
                $walletTo->address,
            ],
        ],
        'payload' => payloadDetails($viewModel),
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

    $blsPublicKey = '8a8b2c9d1e5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3f4a5b6c7d8e9f0';

    $registrationTransaction = Transaction::factory()
        ->validatorRegistration($blsPublicKey)
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

    (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 2.0);
    (new CryptoDataCache())->setPrices('USD.week', collect([
        $transaction->timestamp->format('Y-m-d') => 2.0,
    ]));

    $viewModel    = new TransactionViewModel($transaction);
    $regViewModel = new TransactionViewModel($registrationTransaction);
    $subject      = TransactionDTO::fromModel($transaction);

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
        'multiPaymentRecipients'    => [],
        'multiPaymentTotal'         => null,
        'exchangeRates'             => ExchangeRate::allCurrencyRates($transaction->tiamestamp),
        'url'                       => route('transaction', $transaction),
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
            'multiPaymentRecipients'    => [],
            'multiPaymentTotal'         => null,
            'exchangeRates'             => ExchangeRate::allCurrencyRates($registrationTransaction->timestamp),
            'url'                       => route('transaction', $registrationTransaction),
            'validatorRegistration'     => null,
            'votedFor'                  => null,
            'sender'                    => [
                'address'  => $walletFrom->address,
                'username' => 'joe.blogs',
            ],
            'recipient'                       => WalletReference::stub(Network::knownContract('consensus'))->toArray(),
            'votedForUsername'                => null,
            'tokenApprovalDetails'            => null,
            'methodData'                      => [
                'functionName' => 'registerValidator(bytes,bytes)',
                'methodId'     => ContractMethod::validatorRegistration(),
                'arguments'    => [
                    str_pad($blsPublicKey, 64, '0', STR_PAD_LEFT),
                ],
            ],
            'payload' => payloadDetails($regViewModel),
        ],
        'votedFor' => null,
        'sender'   => [
            'address'  => $walletFrom->address,
            'username' => 'joe.blogs',
        ],
        'recipient'                       => WalletReference::stub(Network::knownContract('consensus'))->toArray(),
        'votedForUsername'                => null,
        'tokenApprovalDetails'            => null,
        'methodData'                      => [
            'functionName' => 'resignValidator()',
            'methodId'     => ContractMethod::validatorResignation(),
            'arguments'    => [],
        ],
        'payload' => payloadDetails($viewModel),
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

    (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 2.0);
    (new CryptoDataCache())->setPrices('USD.week', collect([
        $transaction->timestamp->format('Y-m-d') => 2.0,
    ]));

    // Should not throw an exception
    $subject = TransactionDTO::fromModel($transaction);

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

    (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 2.0);
    (new CryptoDataCache())->setPrices('USD.week', collect([
        $transaction->timestamp->format('Y-m-d') => 2.0,
    ]));

    // Should not throw an exception
    $subject = TransactionDTO::fromModel($transaction);

    expect($subject->recipient)->not->toBeNull();
    expect($subject->recipient->address)->toBe($nonExistentRecipientAddress);
    expect($subject->sender)->not->toBeNull();
});

it('should stub sender wallet when address is not in db', function () {
    $senderWallet = Wallet::factory()->create();

    $transaction = Transaction::factory()
        ->create([
            'from'              => $senderWallet->address,
            'sender_public_key' => $senderWallet->public_key,
        ]);

    // Delete the wallet so findByAddress will throw ModelNotFoundException
    $senderWallet->delete();

    // Ensure sender relation is not loaded so it falls through to findByAddress
    $transaction->unsetRelation('sender');

    $subject = TransactionDTO::fromModel($transaction);

    expect($subject->sender)->not->toBeNull();
    expect(strtolower($subject->sender->address))->toBe(strtolower($senderWallet->address));
    expect($subject->sender->username)->toBeNull();
});

it('normalizes invalid utf8 payloads for the dto', function () {
    $transaction = Transaction::factory()
        ->withPayload('c328')
        ->create([
            'block_number' => 900,
            'status'       => true,
        ]);

    $dto     = TransactionDTO::fromModel($transaction);
    $payload = $dto->payload;

    expect($payload)->not->toBeNull();
    expect($payload['raw'])->toBe('c328');
    expect($payload['formatted'])->toBeString();
    expect($payload['utf8'])->toBeString();
    expect(preg_match('//u', $payload['utf8']))->toBe(1);
});

it('should include token approval details for approve transaction', function () {
    fakeCryptoCompare();

    $spender = Wallet::factory()->create([
        'attributes' => ['username' => 'spender.user'],
    ]);

    $transaction = Transaction::factory()
        ->approve($spender->address, BigNumber::new(5000))
        ->create(['block_number' => 900, 'status' => true]);

    $dto = TransactionDTO::fromModel($transaction);

    expect($dto->tokenApprovalDetails)->not->toBeNull();
    expect($dto->tokenApprovalDetails['spender']->address)->toBe($spender->address);
    expect($dto->tokenApprovalDetails['spender']->username)->toBe('spender.user');
    expect($dto->tokenApprovalDetails['isUnlimited'])->toBeFalse();
    expect($dto->tokenApprovalDetails['isRevoke'])->toBeFalse();
});

it('should use preloaded wallets collection for token approval details', function () {
    fakeCryptoCompare();

    $spender = Wallet::factory()->make([
        'attributes' => ['username' => 'preloaded.user'],
    ]);

    $transaction = Transaction::factory()
        ->approve($spender->address, BigNumber::new(1000))
        ->create(['block_number' => 900, 'status' => true]);

    $preloadedWallets = collect([$spender->address => $spender]);

    $dto = TransactionDTO::fromModel($transaction, null, $preloadedWallets);

    expect($dto->tokenApprovalDetails)->not->toBeNull();
    expect($dto->tokenApprovalDetails['spender']->address)->toBe($spender->address);
    expect($dto->tokenApprovalDetails['spender']->username)->toBe('preloaded.user');
});

it('should use stub when spender not found in preloaded wallets', function () {
    fakeCryptoCompare();

    $spenderAddress = '0x'.str_repeat('ab', 20);

    $transaction = Transaction::factory()
        ->approve($spenderAddress, BigNumber::new(1000))
        ->create(['block_number' => 900, 'status' => true]);

    $dto = TransactionDTO::fromModel($transaction, null, collect());

    expect($dto->tokenApprovalDetails)->not->toBeNull();
    expect(strtolower($dto->tokenApprovalDetails['spender']->address))->toBe(strtolower($spenderAddress));
    expect($dto->tokenApprovalDetails['spender']->username)->toBeNull();
});
