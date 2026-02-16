<?php

declare(strict_types=1);

use App\Console\Commands\CacheTokens;
use App\DTO\Inertia\TransactionDetails;
use App\Models\Token;
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
    expect($details->tokenApproval['spender'])->toBe($spender->address);
    expect($details->tokenApproval['amount'])->toBeString();
    expect($details->tokenApproval['isUnlimited'])->toBeFalse();
    expect($details->tokenApproval['isRevoke'])->toBeFalse();
    expect($details->tokenApproval['spenderUsername'])->toBe('spender.user');
    expect($details->tokenApproval['spenderHasUsername'])->toBeTrue();
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
