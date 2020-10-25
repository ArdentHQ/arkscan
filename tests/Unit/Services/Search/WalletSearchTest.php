<?php

declare(strict_types=1);

use App\Models\Wallet;
use App\Services\Search\WalletSearch;

use function Tests\configureExplorerDatabase;

beforeEach(fn () => configureExplorerDatabase());

it('should search for a wallet by address', function () {
    $wallet = Wallet::factory(10)->create()[0];

    $result = (new WalletSearch())->search([
        'term' => $wallet->address,
    ]);

    expect($result->get())->toHaveCount(1);
});

it('should search for a wallet by public_key', function () {
    $wallet = Wallet::factory(10)->create()[0];

    $result = (new WalletSearch())->search([
        'term' => $wallet->public_key,
    ]);

    expect($result->get())->toHaveCount(1);
});

it('should search for a wallet by username', function () {
    $wallet = Wallet::factory(10)->create()[0];

    $result = (new WalletSearch())->search([
        'username' => $wallet->attributes['delegate']['username'],
    ]);

    expect($result->get())->toHaveCount(1);
});

it('should search for a wallet by vote', function () {
    Wallet::factory(10)->create();

    $wallet = Wallet::factory()->create([
        'attributes' => [
            'vote' => 'public_key',
        ],
    ]);

    $result = (new WalletSearch())->search([
        'vote' => $wallet->attributes['vote'],
    ]);

    expect($result->get())->toHaveCount(1);
});

it('should search for a wallet by balance minimum', function () {
    $wallet = Wallet::factory(10)->create(['balance' => 100])[0];
    $wallet->update(['balance' => 1000]);

    $result = (new WalletSearch())->search([
        'balanceFrom' => 101,
    ]);

    expect($result->get())->toHaveCount(1);
});

it('should search for a wallet by balance maximum', function () {
    $wallet = Wallet::factory(10)->create(['balance' => 100])[0];
    $wallet->update(['balance' => 1000]);

    $result = (new WalletSearch())->search([
        'balanceTo' => 999,
    ]);

    expect($result->get())->toHaveCount(9);
});

it('should search for a wallet by balance range', function () {
    Wallet::factory(10)->create(['balance' => 10]);
    $wallet = Wallet::factory(10)->create(['balance' => 100])[0];
    $wallet->update(['balance' => 1000]);

    $result = (new WalletSearch())->search([
        'balanceFrom' => 50,
        'balanceTo'   => 100,
    ]);

    expect($result->get())->toHaveCount(9);
});
