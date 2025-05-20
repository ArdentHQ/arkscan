<?php

declare(strict_types=1);

use App\Http\Livewire\Home\Transactions;
use App\Models\Receipt;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Transaction;
use App\ViewModels\ViewModelFactory;
use Livewire\Livewire;

it('should list the first page of transactions', function () {
    $transactions = Transaction::factory(30)->transfer()->create([
        'value'     => 143.2232 * 1e18,
        'gas_price' => 0.128373 * 1e9,
    ]);

    foreach ($transactions as $transaction) {
        Receipt::factory()->create([
            'transaction_hash'       => $transaction->hash,
            'gas_used'               => 1e9,
        ]);
    }

    $component = Livewire::test(Transactions::class)
        ->call('setIsReady');

    foreach (ViewModelFactory::collection(Transaction::withScope(OrderByTimestampScope::class)->take(15)->get()) as $transaction) {
        $component->assertSee($transaction->hash());
        $component->assertSee($transaction->timestamp());
        $component->assertSee($transaction->sender()->address());
        $component->assertSee($transaction->recipient()->address());
        $component->assertSee('143.22');
        $component->assertSee('0.128373');
    }
});

it('should also sort transactions by index in block', function () {
    $transaction1 = Transaction::factory()
        ->transfer()
        ->create([
            'transaction_index' => 0,
        ]);

    $transaction2 = Transaction::factory()
        ->transfer()
        ->create([
            'transaction_index' => 1,
        ]);

    Livewire::test(Transactions::class)
        ->call('setIsReady')
        ->assertSeeInOrder([
            $transaction2->hash,
            $transaction1->hash,
        ]);
});
