<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Casts\UnixSeconds;
use App\Models\Transaction;
use Carbon\Carbon;

it('should convert milliseconds to Carbon', function () {
    $transaction = Transaction::factory()->create();

    $value = (new UnixSeconds())->get($transaction, 'timestamp', 1700000000000, []);

    expect($value)->toBeInstanceOf(Carbon::class);
    expect($value->timestamp)->toBe(1700000000);
});

it('should floor the result', function () {
    $transaction = Transaction::factory()->create();

    $value = (new UnixSeconds())->get($transaction, 'timestamp', 1700000000999, []);

    expect($value->timestamp)->toBe(1700000000);
});

it('should return a Carbon instance', function () {
    $block = Block::factory()->create();

    $value = (new UnixSeconds())->get($block, 'timestamp', 1700000000500, []);

    expect($value)->toBeInstanceOf(Carbon::class);
});

it('should convert Carbon to milliseconds on set', function () {
    $transaction = Transaction::factory()->create();
    $carbon      = Carbon::createFromTimestamp(1700000000);

    $value = (new UnixSeconds())->set($transaction, 'timestamp', $carbon, []);

    expect($value)->toBe(1700000000000);
});

it('should return raw value as-is on set when not Carbon', function () {
    $transaction = Transaction::factory()->create();

    $value = (new UnixSeconds())->set($transaction, 'timestamp', 1700000000000, []);

    expect($value)->toBe(1700000000000);
});

it('should handle zero', function () {
    $transaction = Transaction::factory()->create();

    $value = (new UnixSeconds())->get($transaction, 'timestamp', 0, []);

    expect($value)->toBeInstanceOf(Carbon::class);
    expect($value->timestamp)->toBe(0);
});
