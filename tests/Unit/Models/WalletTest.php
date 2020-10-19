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
    expect($this->subject->formatted_balance)->toBeString();
    expect($this->subject->formatted_balance)->toBe('ARK 1,000.00');
});

it('should get the formatted vote_balance', function () {
    expect($this->subject->formatted_vote_balance)->toBeString();
    expect($this->subject->formatted_vote_balance)->toBe('ARK 2,000.00');
});
