<?php

declare(strict_types=1);

use App\DTO\Inertia\TransactionDetails;
use ReflectionMethod;

it('returns null when payload input is null', function () {
    $method = new ReflectionMethod(TransactionDetails::class, 'safeUtf8');
    $method->setAccessible(true);

    expect($method->invoke(null, null))->toBeNull();
});

it('normalizes invalid utf8 payload input', function () {
    $method = new ReflectionMethod(TransactionDetails::class, 'safeUtf8');
    $method->setAccessible(true);

    $result = $method->invoke(null, "\xC3\x28");

    expect($result)->toBeString();
    expect(preg_match('//u', $result))->toBe(1);
});
