<?php

declare(strict_types=1);

use App\DTO\Payment;

use function Tests\configureExplorerDatabase;

it('should make an instance that has all properties', function () {
    configureExplorerDatabase();

    $subject = new Payment('amount', 'recipient');

    expect($subject->amount())->toBe('amount');
    expect($subject->recipient())->toBe('recipient');
});
