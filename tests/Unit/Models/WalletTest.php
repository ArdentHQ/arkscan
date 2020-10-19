<?php

declare(strict_types=1);

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use function Tests\configureExplorerDatabase;

beforeEach(function () {
    configureExplorerDatabase();

    $this->subject = Wallet::factory()->create([
        'balance'      => 1000 * 1e8,
        'vote_balance' => 2000 * 1e8,
    ]);
});

it('should have many sent transactions', function () {
    expect($this->subject->sentTransactions())->toBeInstanceOf(HasMany::class);
    expect($this->subject->sentTransactions)->toBeInstanceOf(Collection::class);
});

it('should have many received transactions', function () {
    expect($this->subject->receivedTransactions())->toBeInstanceOf(HasMany::class);
    expect($this->subject->receivedTransactions)->toBeInstanceOf(Collection::class);
});

it('should have many blocks', function () {
    expect($this->subject->blocks())->toBeInstanceOf(HasMany::class);
    expect($this->subject->blocks)->toBeInstanceOf(Collection::class);
});

it('should order wallets by their balance from high to low', function () {
    expect($this->subject->wealthy())->toBeInstanceOf(Builder::class);
});

it('should only query wallets that vote for the given public key', function () {
    expect($this->subject->vote('some-public-key'))->toBeInstanceOf(Builder::class);
});

it('should get the formatted balance', function () {
    expect($this->subject->formatted_balance)->toBeFloat();
    expect($this->subject->formatted_balance)->toBe(1000.0);
});

it('should get the formatted vote_balance', function () {
    expect($this->subject->formatted_vote_balance)->toBeFloat();
    expect($this->subject->formatted_vote_balance)->toBe(2000.0);
});

it('should find a wallet by its address', function () {
    expect(Wallet::findByAddress($this->subject->address))->toBeInstanceOf(Wallet::class);
});

it('should find a wallet by its public_key', function () {
    expect(Wallet::findByPublicKey($this->subject->public_key))->toBeInstanceOf(Wallet::class);
});

it('should find a wallet by its username', function () {
    expect(Wallet::findByUsername($this->subject->username))->toBeInstanceOf(Wallet::class);
});
