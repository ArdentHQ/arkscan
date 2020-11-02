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
        'balance'    => '100000000000',
        'attributes' => [
            'delegate' => [
                'voteBalance' => '200000000000',
            ],
        ],
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

it('should have many voters', function () {
    expect($this->subject->voters())->toBeInstanceOf(HasMany::class);
    expect($this->subject->voters)->toBeInstanceOf(Collection::class);
});

it('should order wallets by their balance from high to low', function () {
    expect($this->subject->wealthy())->toBeInstanceOf(Builder::class);
});

it('should only query wallets that vote for the given public key', function () {
    expect($this->subject->vote('some-public-key'))->toBeInstanceOf(Builder::class);
});
