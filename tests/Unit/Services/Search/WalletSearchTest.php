<?php

declare(strict_types=1);

use App\Models\Wallet;
use App\Services\Search\WalletSearch;

it('should search for a wallet by address', function (?string $modifier) {
    $wallet = Wallet::factory(10)->create()[0];

    $result = (new WalletSearch())->search([
        'term' => $modifier ? $modifier($wallet->address) : $wallet->address,
    ]);

    expect($result->first()->is($wallet))->toBeTrue();
})->with([null, 'strtolower', 'strtoupper']);

it('should search for a wallet by public key', function (?string $modifier) {
    Wallet::factory(10)->create(['public_key' => '123']);

    $wallet = Wallet::factory()->create();

    $result = (new WalletSearch())->search([
        'term' => $modifier ? $modifier($wallet->public_key) : $wallet->public_key,
    ]);

    expect($result->get())->toHaveCount(1);
})->with([null, 'strtolower', 'strtoupper']);

it('should search for a wallet by delegate username in terms', function (?string $modifier) {
    $wallet = Wallet::factory(10)->create()[0];

    $result = (new WalletSearch())->search([
        'term' => $modifier ? $modifier($wallet->attributes['delegate']['username']) : $wallet->attributes['delegate']['username'],
    ]);

    expect($result->get())->toHaveCount(1);
})->with([null, 'strtolower', 'strtoupper']);

it('can search for a wallet by term matching the username containing special characters', function (?string $modifier) {
    $wallet               = Wallet::factory(10)->create()[0];
    $delegate             = $wallet->attributes['delegate'];
    $delegate['username'] = 'john.doe (old) [new] 2';

    $wallet->update([
        'attributes' => array_merge($wallet->attributes, ['delegate' => $delegate]),
    ]);

    $result = (new WalletSearch())->search([
        'term' => $modifier ? $modifier('john.doe (old) [new] 2') : 'john.doe (old) [new] 2',
    ]);

    expect($result->get())->toHaveCount(1);
})->with([null, 'strtolower', 'strtoupper']);

it('should search for a wallet by username', function (?string $modifier) {
    $wallet = Wallet::factory(10)->create()[0];

    $result = (new WalletSearch())->search([
        'term'     => '',
        'username' => $modifier ? $modifier($wallet->attributes['delegate']['username']) : $wallet->attributes['delegate']['username'],
    ]);

    expect($result->get())->toHaveCount(1);
})->with([null, 'strtolower', 'strtoupper']);

it('can search for a wallet by username containing special characters', function (?string $modifier) {
    $wallet               = Wallet::factory(10)->create()[0];
    $delegate             = $wallet->attributes['delegate'];
    $delegate['username'] = 'john.doe (old) [new] 2';

    $wallet->update([
        'attributes' => array_merge($wallet->attributes, ['delegate' => $delegate]),
    ]);

    $result = (new WalletSearch())->search([
        'term'     => '',
        'username' => $modifier ? $modifier('john.doe (old) [new] 2') : 'john.doe (old) [new] 2',
    ]);

    expect($result->get())->toHaveCount(1);
})->with([null, 'strtolower', 'strtoupper']);

it('should search for a wallet by vote', function (?string $modifier) {
    Wallet::factory(10)->create();

    $wallet = Wallet::factory()->create([
        'attributes' => [
            'vote' => 'public_key',
        ],
    ]);

    $result = (new WalletSearch())->search([
        'vote' => $modifier ? $modifier($wallet->attributes['vote']) : $wallet->attributes['vote'],
    ]);

    expect($result->get())->toHaveCount(1);
})->with([null, 'strtolower', 'strtoupper']);

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
