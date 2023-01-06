<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    $this->subject = Transaction::factory()->create([
        'fee'    => '100000000',
        'amount' => '200000000',
    ]);
});

it('should belong to a block', function () {
    expect($this->subject->block())->toBeInstanceOf(BelongsTo::class);
    expect($this->subject->block)->toBeInstanceOf(Block::class);
});

it('should belong to a sender', function () {
    Wallet::factory()->create(['public_key' => $this->subject->sender_public_key]);

    expect($this->subject->sender())->toBeInstanceOf(BelongsTo::class);
    expect($this->subject->sender)->toBeInstanceOf(Wallet::class);
});

it('should belong to a recipient', function () {
    Wallet::factory()->create(['address' => $this->subject->recipient_id]);

    expect($this->subject->recipient())->toBeInstanceOf(BelongsTo::class);
    expect($this->subject->recipient)->toBeInstanceOf(Wallet::class);
});

it('should get migrated transactions', function () {
    Config::set('explorer.migration_address', 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj');

    $migratedTransactions = Transaction::factory(5)->transfer()->create([
        'recipient_id' => 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj',
    ]);

    expect(Transaction::migrated()->count())->toBe(5);
});

it('should exclude migrated transactions which are not transfers', function () {
    Config::set('explorer.migration_address', 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj');

    $migratedTransactions = Transaction::factory(5)->vote()->create([
        'recipient_id' => 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj',
    ]);

    expect(Transaction::migrated()->count())->toBe(0);
});

it('should exclude migrated transactions which are not core group type', function () {
    Config::set('explorer.migration_address', 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj');

    $migratedTransactions = Transaction::factory(5)->legacyBusinessRegistration()->create([
        'recipient_id' => 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj',
    ]);

    expect(Transaction::migrated()->count())->toBe(0);
});
