<?php

declare(strict_types=1);

use App\Models\MultiPayment;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;

it('should link to a transaction', function () {
    $recipientAddress = Wallet::factory()->create()->address;

    $transaction = Transaction::factory()
        ->multiPayment([$recipientAddress], [BigNumber::new(1)])
        ->create();

    $recipient = MultiPayment::factory()
        ->create([
            'to'     => $recipientAddress,
            'from'   => $transaction->from,
            'hash'   => $transaction->hash,
            'amount' => BigNumber::new(1),
        ]);

    expect($recipient->hash)->toBe($transaction->hash);
    expect($recipient->transaction)->toBeInstanceOf(Transaction::class);
    expect($recipient->transaction->hash)->toBe($transaction->hash);
});
