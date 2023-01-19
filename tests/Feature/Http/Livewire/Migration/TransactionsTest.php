<?php

declare(strict_types=1);

use App\Http\Livewire\Migration\Transactions;
use App\Models\Transaction;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;

it('should get migrated transactions', function () {
    Config::set('explorer.migration.address', 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj');

    $transactions         = Transaction::factory(5)->transfer()->create();
    $migratedTransactions = Transaction::factory(5)->transfer()->create([
        'recipient_id' => 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj',
        'fee'          => '10000000', // 0.1
        'amount'       => '200000000', // 2
        'vendor_field' => '0xRKeoIZ9Kh2g4HslgeHr5B9yblHbnwWYgfeFgO36n',
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
    Config::set('explorer.migration.address', 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj');

    $migratedTransactions = Transaction::factory(10)->transfer()->create([
        'recipient_id' => 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj',
        'fee'          => '10000000', // 0.1
        'amount'       => '200000000', // 2
        'vendor_field' => '0xRKeoIZ9Kh2g4HslgeHr5B9yblHbnwWYgfeFgO36n',
    ]);
    $secondPageTransactions = Transaction::factory(10)->transfer()->create([
        'recipient_id' => 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj',
        'fee'          => '10000000', // 0.1
        'amount'       => '200000000', // 2
        'vendor_field' => '0xRKeoIZ9Kh2g4HslgeHr5B9yblHbnwWYgfeFgO36n',
    ]);

    $component = Livewire::test(Transactions::class);

    foreach ($migratedTransactions as $transaction) {
        $component->assertSee($transaction->sender->address);
    }

    foreach ($secondPageTransactions as $transaction) {
        $component->assertDontSee($transaction->sender->address);
    }
});

it('should not show migrated transactions which do not match criteria', function () {
    Config::set('explorer.migration.address', 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj');

    $migratedTransactions = Transaction::factory(3)->transfer()->create([
        'recipient_id' => 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj',
        'fee'          => '10000000', // 0.1
        'amount'       => '200000000', // 2
        'vendor_field' => '0xRKeoIZ9Kh2g4HslgeHr5B9yblHbnwWYgfeFgO36n',
    ]);
    $lowAmountTransactions = Transaction::factory(2)->transfer()->create([
        'recipient_id' => 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj',
        'fee'          => '10000000', // 0.1
        'amount'       => '20000000', // 0.2
        'vendor_field' => '0xRKeoIZ9Kh2g4HslgeHr5B9yblHbnwWYgfeFgO36n',
    ]);
    $lowFeeTransactions = Transaction::factory(2)->transfer()->create([
        'recipient_id' => 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj',
        'fee'          => '1000000', // 0.01
        'amount'       => '200000000', // 2
        'vendor_field' => '0xRKeoIZ9Kh2g4HslgeHr5B9yblHbnwWYgfeFgO36n',
    ]);
    $invalidVendorFieldTransactions = Transaction::factory(2)->transfer()->create([
        'recipient_id' => 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj',
        'fee'          => '10000000', // 0.1
        'amount'       => '200000000', // 2
        'vendor_field' => '0xInvalid',
    ]);

    $component = Livewire::test(Transactions::class);

    foreach ($migratedTransactions as $transaction) {
        $component->assertSee($transaction->sender->address);
    }

    foreach ($lowAmountTransactions as $transaction) {
        $component->assertDontSee($transaction->sender->address);
    }
    foreach ($lowFeeTransactions as $transaction) {
        $component->assertDontSee($transaction->sender->address);
    }
    foreach ($invalidVendorFieldTransactions as $transaction) {
        $component->assertDontSee($transaction->sender->address);
    }
});
