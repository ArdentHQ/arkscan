<?php

declare(strict_types=1);

use App\Http\Livewire\Migration\Transactions;
use App\Models\Transaction;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;

it('should get migrated transactions', function () {
    Config::set('explorer.migration_address', 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj');

    $transactions         = Transaction::factory(5)->transfer()->create();
    $migratedTransactions = Transaction::factory(5)->transfer()->create([
        'recipient_id' => 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj',
    ]);

    $component = Livewire::test(Transactions::class);

    foreach ($transactions as $transaction) {
        $component->assertDontSee($transaction->sender->address);
    }

    foreach ($migratedTransactions as $transaction) {
        $component->assertSee($transaction->sender->address);
    }
});

it('should paginate transactions', function () {
    Config::set('explorer.migration_address', 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj');

    $migratedTransactions = Transaction::factory(10)->transfer()->create([
        'recipient_id' => 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj',
    ]);
    $secondPageTransactions = Transaction::factory(10)->transfer()->create([
        'recipient_id' => 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj',
    ]);

    $component = Livewire::test(Transactions::class);

    foreach ($migratedTransactions as $transaction) {
        $component->assertSee($transaction->sender->address);
    }

    foreach ($secondPageTransactions as $transaction) {
        $component->assertDontSee($transaction->sender->address);
    }
});
