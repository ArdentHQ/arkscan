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

    expect($result->first()->is($wallet))->toBeTrue();
});

it('should search for a wallet by public key', function () {
    Wallet::factory(10)->create(['public_key' => '123']);

    $wallet = Wallet::factory()->create();

    $result = (new WalletSearch())->search([
        'term' => $wallet->public_key,
    ]);

    expect($result->get())->toHaveCount(1);
});

it('should search for a wallet by delegate username in terms', function () {
    $wallet = Wallet::factory(10)->create()[0];

    $result = (new WalletSearch())->search([
        'term' => $wallet->attributes['delegate']['username'],
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
    $wallet = Wallet::factory(10)->create(['balance' => 100 * 1e8])[0];
    $wallet->update(['balance' => 1000 * 1e8]);

    $result = (new WalletSearch())->search([
        'balanceFrom' => 101,
    ]);

    expect($result->get())->toHaveCount(1);
});

it('should search for a wallet by balance maximum', function () {
    $wallet = Wallet::factory(10)->create(['balance' => 100 * 1e8])[0];
    $wallet->update(['balance' => 1000 * 1e8]);

    $result = (new WalletSearch())->search([
        'balanceTo' => 999,
    ]);

    expect($result->get())->toHaveCount(9);
});

it('should search for a wallet by balance range', function () {
    Wallet::factory(10)->create(['balance' => 10 * 1e8]);
    $wallet = Wallet::factory(10)->create(['balance' => 100 * 1e8])[0];
    $wallet->update(['balance' => 1000 * 1e8]);

    $result = (new WalletSearch())->search([
        'balanceFrom' => 50,
        'balanceTo'   => 100,
    ]);

    expect($result->get())->toHaveCount(9);
});
