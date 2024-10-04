<?php

declare(strict_types=1);

use App\Http\Livewire\Home\Transactions;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Transaction;
use App\ViewModels\ViewModelFactory;
use Livewire\Livewire;

it('should list the first page of transactions', function () {
    Transaction::factory(30)->transfer()->create([
        'amount' => 143.2232 * 1e18,
        'fee'    => 0.128373 * 1e18,
    ]);

    $component = Livewire::test(Transactions::class)
        ->call('setIsReady');

    foreach (ViewModelFactory::collection(Transaction::withScope(OrderByTimestampScope::class)->take(15)->get()) as $transaction) {
        $component->assertSee($transaction->id());
        $component->assertSee($transaction->timestamp());
        $component->assertSee($transaction->sender()->address());
        $component->assertSee($transaction->recipient()->address());
        $component->assertSee('143.2232');
        $component->assertSee('0.128373');
    }
});
