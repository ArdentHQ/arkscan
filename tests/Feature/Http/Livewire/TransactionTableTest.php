<?php

declare(strict_types=1);

use App\Http\Livewire\TransactionTable;
use App\Models\Transaction;

use App\ViewModels\ViewModelFactory;
use Livewire\Livewire;
use function Tests\configureExplorerDatabase;

beforeEach(fn () => configureExplorerDatabase());

it('should list the first page of records', function () {
    Transaction::factory(100)->create();

    $component = Livewire::test(TransactionTable::class);

    foreach (ViewModelFactory::paginate(Transaction::latestByTimestamp()->paginate())->items() as $transaction) {
        $component->assertSee($transaction->id());
        $component->assertSee($transaction->timestamp());
        $component->assertSee($transaction->type());
        $component->assertSee($transaction->sender());
        $component->assertSee($transaction->recipient());
        $component->assertSee($transaction->fee());
        $component->assertSee($transaction->amount());
    }
});
