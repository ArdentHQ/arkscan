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
        $component->assertSee($transaction->timestamp);
        $component->assertSee($transaction->type);
        $component->assertSee($transaction->type_group);
        $component->assertSee($transaction->sender_public_key);
        $component->assertSee($transaction->recipient_id);
        $component->assertSee($transaction->amount);
        $component->assertSee($transaction->fee);
    }
});
