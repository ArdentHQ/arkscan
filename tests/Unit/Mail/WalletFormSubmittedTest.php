<?php

use App\Mail\WalletFormSubmitted;

it('should build a mail object', function () {
    $mail = new WalletFormSubmitted([
        'name' => 'Test User',
        'website' => 'https://google.com',
        'message' => 'This is a test mail message',
    ]);

    $build = $mail->build();

    expect($build->to)->toBe([
        [
            'name' => null,
            'address' => config('mail.wallet_submitted.address'),
        ]
    ]);

    expect($build->markdown)->toBe('mails.wallet-submitted');
    expect($build->subject)->toBe('A Wallet has been submitted');
});
