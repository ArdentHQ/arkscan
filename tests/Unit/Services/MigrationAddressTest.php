<?php

declare(strict_types=1);

use App\Services\MigrationAddress;

it('determines whether its a delegate', function () {
    $address = new MigrationAddress();

    expect($address->isDelegate())->toBeTrue(); // hardcoded
});

it('can get the wallet address', function () {
    config([
        'explorer.migration_address' => '0xTestAddress',
    ]);

    $address = new MigrationAddress();

    expect($address->address())->toBe('0xTestAddress');
});

it('can get the wallet name', function () {
    config([
        'explorer.migration_wallet' => 'known-name',
    ]);

    $address = new MigrationAddress();

    expect($address->username())->toBe('known-name');
});
