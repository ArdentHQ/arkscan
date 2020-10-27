<?php

declare(strict_types=1);

use App\Http\Livewire\Tables\Transactions;
use App\Models\Transaction;
use App\ViewModels\ViewModelFactory;
use Livewire\Livewire;

use function Tests\configureExplorerDatabase;

beforeEach(fn () => configureExplorerDatabase());

it('should list the first page of records', function () {
    Transaction::factory(30)->create();

    $component = Livewire::test(Transactions::class, [
        'transactions' => Transaction::latestByTimestamp(),
    ]);

    foreach (ViewModelFactory::paginate(Transaction::latestByTimestamp()->paginate())->items() as $transaction) {
        $component->assertSee($transaction->id());
        $component->assertSee($transaction->timestamp());
        $component->assertSee($transaction->sender());
        $component->assertSee($transaction->recipient());
        $component->assertSee($transaction->fee());
        $component->assertSee($transaction->amount());
    }
});
