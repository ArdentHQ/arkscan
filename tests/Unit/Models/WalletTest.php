<?php

declare(strict_types=1);

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
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

it('should determine migration wallet', function () {
    Config::set('explorer.migration.address', 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj');

    $wallet = Wallet::factory()->create([
        'address' => 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj',
    ]);

    expect($wallet->isMigration())->toBeTrue();
});

it('should determine not a migration wallet', function () {
    Config::set('explorer.migration.address', 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj');

    $wallet = Wallet::factory()->create();

    expect($wallet->isMigration())->toBeFalse();
});
