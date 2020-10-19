<?php

declare(strict_types=1);

use App\Http\Livewire\TransactionTable;
use App\Models\Transaction;

use Livewire\Livewire;
use function Tests\configureExplorerDatabase;

beforeEach(fn () => configureExplorerDatabase());

it('should list the first page of records', function () {
    Transaction::factory(100)->create();

    $component = Livewire::test(TransactionTable::class);

    foreach (Transaction::latestByTimestamp()->paginate()->items() as $transaction) {
        $component->assertSee($transaction->id);
        $component->assertSee($transaction->formatted_timestamp);
        $component->assertSee($transaction->type);
        $component->assertSee($transaction->type_group);
        $component->assertSee($transaction->sender->address);
        $component->assertSee($transaction->recipient->address);
        $component->assertSee($transaction->formatted_amount);
        $component->assertSee($transaction->formatted_fee);
    }
});
