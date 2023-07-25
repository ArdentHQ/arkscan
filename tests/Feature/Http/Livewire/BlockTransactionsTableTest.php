<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Http\Livewire\BlockTransactionsTable;
use App\Models\Block;
use App\Models\Transaction;
use App\Services\NumberFormatter;
use App\ViewModels\BlockViewModel;
use App\ViewModels\ViewModelFactory;
use Livewire\Livewire;

it('should list the first transactions for the giving block id', function () {
    $block = Block::factory()->create();
    Transaction::factory(25)->transfer()->create(['block_id' => $block->id]);

    $component = Livewire::test(BlockTransactionsTable::class, ['block' => new BlockViewModel($block)])
        ->call('setIsReady');

    foreach (ViewModelFactory::paginate($block->transactions()->paginate(25))->items() as $transaction) {
        $component->assertSee($transaction->id());
        $component->assertSee($transaction->sender()->address());
        $component->assertSee($transaction->recipient()->address());
        $component->assertSee(NumberFormatter::networkCurrency($transaction->amount()));
        $component->assertSee(NumberFormatter::networkCurrency($transaction->fee()));
    }
});
