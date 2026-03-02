<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Casts\UnixSeconds;
use App\Models\Transaction;

it('should convert milliseconds to seconds', function () {
    $transaction = Transaction::factory()->create();

    $value = (new UnixSeconds())->get($transaction, 'timestamp', 1700000000000, []);

    expect($value)->toBe(1700000000);
});

it('should floor the result', function () {
    $transaction = Transaction::factory()->create();

    $value = (new UnixSeconds())->get($transaction, 'timestamp', 1700000000999, []);

    expect($value)->toBe(1700000000);
});

it('should return an integer', function () {
    $block = Block::factory()->create();

    $value = (new UnixSeconds())->get($block, 'timestamp', 1700000000500, []);

    expect($value)->toBeInt();
});

it('should return value as-is on set', function () {
    $transaction = Transaction::factory()->create();

    $value = (new UnixSeconds())->set($transaction, 'timestamp', 1700000000, []);

    expect($value)->toBe(1700000000);
});

it('should handle zero', function () {
    $transaction = Transaction::factory()->create();

    $value = (new UnixSeconds())->get($transaction, 'timestamp', 0, []);

    expect($value)->toBe(0);
});
