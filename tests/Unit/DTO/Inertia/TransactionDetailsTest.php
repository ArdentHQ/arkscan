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
