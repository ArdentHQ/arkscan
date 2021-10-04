<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Http\Livewire\Tables\Transactions;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Transaction;
use App\Services\NumberFormatter;
use App\ViewModels\ViewModelFactory;
use Livewire\Livewire;

it('should list the first page of records', function () {
    Transaction::factory(30)->transfer()->create();

    $component = Livewire::test(Transactions::class, [
        'transactions' => Transaction::withScope(OrderByTimestampScope::class),
    ]);

    foreach (ViewModelFactory::paginate(Transaction::withScope(OrderByTimestampScope::class)->paginate())->items() as $transaction) {
        $component->assertSee($transaction->id());
        $component->assertSee($transaction->timestamp());
        $component->assertSee($transaction->sender()->address());
        $component->assertSee($transaction->recipient()->address());
        $component->assertSee(NumberFormatter::currency($transaction->amount(), Network::currency()));
        $component->assertSee(NumberFormatter::currency($transaction->fee(), Network::currency()));
    }
});
