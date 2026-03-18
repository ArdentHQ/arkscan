<?php

declare(strict_types=1);

use App\Console\Commands\CacheTokens;
use App\DTO\Inertia\TransactionDetails;
use App\Models\Token;
use App\Models\TokenAction;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\Cache\NetworkCache;
use function Tests\fakeCryptoCompare;

it('normalizes invalid utf8 payloads for the dto', function () {
    fakeCryptoCompare();

    (new NetworkCache())->setHeight(fn () => 1000);

    $transaction = Transaction::factory()
        ->withPayload('c328')
        ->create([
            'block_number' => 900,
            'status'       => true,
        ]);

    $details = TransactionDetails::fromModel($transaction);
    $payload = $details->payload;

    expect($payload)->not->toBeNull();
    expect($payload['raw'])->toBe('c328');
    expect($payload['formatted'])->toBeString();
    expect($payload['utf8'])->toBeString();
    expect(preg_match('//u', $payload['utf8']))->toBe(1);
});

it('should include token data', function () {
    fakeCryptoCompare();

    (new NetworkCache())->setHeight(fn () => 1000);

    $wallet = Wallet::factory()->create([
        'address' => '0x1234567890AbcdEF1234567890aBcdef12345678',
    ]);

    $transaction = Transaction::factory()
        ->tokenTransfer($wallet->address, BigNumber::new(1000))
        ->create([
            'from'              => $wallet->address,
            'sender_public_key' => $wallet->public_key,
            'block_number'      => 900,
            'status'            => true,
        ]);

    $token = Token::factory()->create([
        'address' => $transaction->to,
    ]);

    $details = TransactionDetails::fromModel($transaction);

    expect($details->token)->toBeNull();

    (new CacheTokens())->handle();

    $details = TransactionDetails::fromModel($transaction);

    expect($details->token->symbol)->toBe($token->symbol);
});

it('should include token approval details for approve transaction', function () {
    fakeCryptoCompare();

    (new NetworkCache())->setHeight(fn () => 1000);

    $spender = Wallet::factory()->create([
        'attributes' => ['username' => 'spender.user'],
    ]);

    $transaction = Transaction::factory()
        ->approve($spender->address, BigNumber::new(5000))
        ->create([
            'block_number' => 900,
            'status'       => true,
        ]);

    $details = TransactionDetails::fromModel($transaction);

    expect($details->tokenApproval)->not->toBeNull();
    expect($details->tokenApproval['spender']->address)->toBe($spender->address);
    expect($details->tokenApproval['amount'])->toBeString();
    expect($details->tokenApproval['isUnlimited'])->toBeFalse();
    expect($details->tokenApproval['isRevoke'])->toBeFalse();
    expect($details->tokenApproval['spender']->username)->toBe('spender.user');
});

it('should detect unlimited approve', function () {
    fakeCryptoCompare();

    (new NetworkCache())->setHeight(fn () => 1000);

    $spender = Wallet::factory()->create();

    // Max uint256 (2^256 - 1)
    $maxUint256 = BigNumber::new('115792089237316195423570985008687907853269984665640564039457584007913129639935');

    $transaction = Transaction::factory()
        ->approve($spender->address, $maxUint256)
        ->create([
            'block_number' => 900,
            'status'       => true,
        ]);

    $details = TransactionDetails::fromModel($transaction);

    expect($details->tokenApproval)->not->toBeNull();
    expect($details->tokenApproval['isUnlimited'])->toBeTrue();
});

it('should detect approval revoke', function () {
    fakeCryptoCompare();

    (new NetworkCache())->setHeight(fn () => 1000);

    $spender = Wallet::factory()->create();

    $transaction = Transaction::factory()
        ->approve($spender->address, BigNumber::zero())
        ->create([
            'block_number' => 900,
            'status'       => true,
        ]);

    $details = TransactionDetails::fromModel($transaction);

    expect($details->tokenApproval)->not->toBeNull();
    expect($details->tokenApproval['isRevoke'])->toBeTrue();
});

it('should handle approve with unknown spender wallet', function () {
    fakeCryptoCompare();

    (new NetworkCache())->setHeight(fn () => 1000);

    $unknownSpender = '0x'.str_repeat('ab', 20);

    $transaction = Transaction::factory()
        ->approve($unknownSpender, BigNumber::new(5000))
        ->create([
            'block_number' => 900,
            'status'       => true,
        ]);

    $details = TransactionDetails::fromModel($transaction);

    expect($details->tokenApproval)->not->toBeNull();
    expect(strtolower($details->tokenApproval['spender']->address))->toBe(strtolower($unknownSpender));
    expect($details->tokenApproval['spender']->username)->toBeNull();
});

it('should return null token approval for approve without valid arguments', function () {
    fakeCryptoCompare();

    (new NetworkCache())->setHeight(fn () => 1000);

    // Approve method hash but no arguments
    $transaction = Transaction::factory()
        ->withPayload('095ea7b3')
        ->create([
            'block_number' => 900,
            'status'       => true,
        ]);

    $details = TransactionDetails::fromModel($transaction);

    expect($details->tokenApproval)->toBeNull();
});

it('should return null token approval for non-approve transaction', function () {
    fakeCryptoCompare();

    (new NetworkCache())->setHeight(fn () => 1000);

    $transaction = Transaction::factory()
        ->create([
            'block_number' => 900,
            'status'       => true,
        ]);

    $details = TransactionDetails::fromModel($transaction);

    expect($details->tokenApproval)->toBeNull();
});

it('should include batch token transfers', function () {
    fakeCryptoCompare();

    (new NetworkCache())->setHeight(fn () => 1000);

    $token = Token::factory()->create();

    $recipient1 = Wallet::factory()->create([
        'attributes' => ['username' => 'bob'],
    ]);
    $recipient2 = Wallet::factory()->create([
        'attributes' => [],
    ]);

    $transaction = Transaction::factory()
        ->batchTransfer(
            $token->address,
            [$recipient1->address, $recipient2->address],
            [BigNumber::new(1000), BigNumber::new(2000)],
        )
        ->create([
            'block_number' => 900,
            'status'       => true,
        ]);

    TokenAction::factory()->count(2)->sequence(
        [
            'transaction_hash' => $transaction->hash,
            'block_number'     => $transaction->block_number,
            'address'          => $token->address,
            'from'             => $transaction->from,
            'to'               => $recipient1->address,
            'value'            => '1000',
            'index'            => 0,
        ],
        [
            'transaction_hash' => $transaction->hash,
            'block_number'     => $transaction->block_number,
            'address'          => $token->address,
            'from'             => $transaction->from,
            'to'               => $recipient2->address,
            'value'            => '2000',
            'index'            => 1,
        ],
    )->create();

    $details = TransactionDetails::fromModel($transaction);

    expect($details->batchTokenTransfers)->toHaveCount(2);
    expect($details->batchTokenTransfers[0]['recipient']->address)->toBe($recipient1->address);
    expect($details->batchTokenTransfers[0]['amount'])->toBe('1000');
    expect($details->batchTokenTransfers[0]['recipient']->username)->toBe('bob');
    expect($details->batchTokenTransfers[1]['recipient']->address)->toBe($recipient2->address);
    expect($details->batchTokenTransfers[1]['amount'])->toBe('2000');
    expect($details->batchTokenTransfers[1]['recipient']->username)->toBeNull();
    expect($details->token)->not->toBeNull();
    expect($details->token->symbol)->toBe($token->symbol);
});

it('should handle batch transfer with unknown recipient wallet', function () {
    fakeCryptoCompare();

    (new NetworkCache())->setHeight(fn () => 1000);

    $token = Token::factory()->create();

    $unknownAddress = '0x'.str_repeat('ab', 20);

    $transaction = Transaction::factory()
        ->batchTransfer(
            $token->address,
            [$unknownAddress],
            [BigNumber::new(500)],
        )
        ->create([
            'block_number' => 900,
            'status'       => true,
        ]);

    TokenAction::factory()->create([
        'transaction_hash' => $transaction->hash,
        'block_number'     => $transaction->block_number,
        'address'          => $token->address,
        'from'             => $transaction->from,
        'to'               => $unknownAddress,
        'value'            => '500',
        'index'            => 0,
    ]);

    $details = TransactionDetails::fromModel($transaction);

    expect($details->batchTokenTransfers)->toHaveCount(1);
    expect($details->batchTokenTransfers[0]['recipient']->address)->toBe($unknownAddress);
    expect($details->batchTokenTransfers[0]['recipient']->username)->toBeNull();
});

it('should resolve token from token_transfer record for token transfers', function () {
    fakeCryptoCompare();

    (new NetworkCache())->setHeight(fn () => 1000);

    $token     = Token::factory()->create();
    $recipient = Wallet::factory()->create();

    $transaction = Transaction::factory()
        ->tokenTransfer($recipient->address, BigNumber::new(1000))
        ->create([
            'block_number' => 900,
            'status'       => true,
        ]);

    TokenAction::factory()->create([
        'transaction_hash' => $transaction->hash,
        'block_number'     => $transaction->block_number,
        'address'          => $token->address,
        'from'             => $transaction->from,
        'to'               => $recipient->address,
        'value'            => '1000',
        'index'            => 0,
    ]);

    $details = TransactionDetails::fromModel($transaction);

    expect($details->token)->not->toBeNull();
    expect($details->token->symbol)->toBe($token->symbol);
});

it('should handle token transfer with unknown recipient wallet', function () {
    fakeCryptoCompare();

    (new NetworkCache())->setHeight(fn () => 1000);

    $unknownAddress = '0x'.str_repeat('cd', 20);

    $transaction = Transaction::factory()
        ->tokenTransfer($unknownAddress, BigNumber::new(1000))
        ->create([
            'block_number' => 900,
            'status'       => true,
        ]);

    $details = TransactionDetails::fromModel($transaction);

    expect($details->tokenAction)->not->toBeNull();
    expect(strtolower($details->tokenAction['recipient']->address))->toBe(strtolower($unknownAddress));
    expect($details->tokenAction['recipient']->username)->toBeNull();
});
