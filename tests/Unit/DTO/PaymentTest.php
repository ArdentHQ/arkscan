<?php

declare(strict_types=1);

use App\DTO\Payment;
use App\Models\Wallet;
use App\Services\NumberFormatter;

it('should make an instance that has all properties', function () {
    $wallet = Wallet::factory()->create();

    $subject = new Payment(123, [
        'amount'      => 1e8,
        'recipientId' => $wallet->address,
    ]);

    expect($subject->amount())->toBe(1.0);
    expect($subject->amountFiat())->toBe(NumberFormatter::currency(0, 'USD'));
    expect($subject->address())->toBe($wallet->address);
    expect($subject->username())->toBe($wallet->attributes['delegate']['username']);
    expect($subject->recipient())->toBeInstanceOf(Payment::class);
});
