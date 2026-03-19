<?php

declare(strict_types=1);

use App\DTO\Inertia\WalletReference;
use App\Models\Wallet;

it('should create from model', function () {
    $wallet = Wallet::factory()
        ->create([
            'attributes' => [
                'username' => 'joe.blogs',
            ],
        ]);

    $subject = WalletReference::fromModel($wallet);

    expect($subject->address)->toBe($wallet->address);
    expect($subject->username)->toBe('joe.blogs');
});

it('should create a stub', function () {
    $address = '0x1234567890abcdef1234567890abcdef12345678';

    $subject = WalletReference::stub($address);

    expect($subject->address)->toBe($address);
    expect($subject->username)->toBeNull();
});

it('should create from model without username', function () {
    $wallet = Wallet::factory()
        ->create([
            'attributes' => [],
        ]);

    $subject = WalletReference::fromModel($wallet);

    expect($subject->address)->toBe($wallet->address);
    expect($subject->username)->toBeNull();
});
