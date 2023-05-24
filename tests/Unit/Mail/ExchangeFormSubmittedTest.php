<?php

declare(strict_types=1);

use App\Mail\ExchangeFormSubmitted;

it('should build a mail object', function () {
    $mail = new ExchangeFormSubmitted([
        'name'    => 'Test User',
        'website' => 'https://google.com',
        'pairs'   => 'BTC, USD, ETH',
        'message' => 'This is a test mail message',
    ]);

    $build = $mail->build();

    expect($build->to)->toBe([
        [
            'name'    => null,
            'address' => config('mail.exchange_submitted.address'),
        ],
    ]);

    expect($build->markdown)->toBe('mails.exchange-submitted');
    expect($build->subject)->toBe('An Exchange has been submitted');
});
