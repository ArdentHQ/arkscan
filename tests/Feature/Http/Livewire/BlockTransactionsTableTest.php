<?php

declare(strict_types=1);

use App\Http\Livewire\BlockTransactionsTable;
use App\Models\Block;
use App\Models\Transaction;
use App\Services\NumberFormatter;
use App\Services\Timestamp;
use App\ViewModels\BlockViewModel;
use App\ViewModels\ViewModelFactory;
use Livewire\Livewire;

it('should list the first transactions for the giving block id', function () {
    $block = Block::factory()->create();
    Transaction::factory(25)->transfer()->create(['block_id' => $block->id]);

    $component = Livewire::test(BlockTransactionsTable::class, ['block' => new BlockViewModel($block)]);

    foreach (ViewModelFactory::paginate($block->transactions()->paginate(25))->items() as $transaction) {
        $component->assertSee($transaction->id());
        $component->assertSee($transaction->sender()->address());
        $component->assertSee($transaction->recipient()->address());
        $component->assertSee(NumberFormatter::networkCurrency($transaction->amount()));
        $component->assertSee(NumberFormatter::networkCurrency($transaction->fee()));
    }
});

it('should load the next batch of transactions', function () {
    $block = Block::factory()->create([
        'transactions_count' => 54,
    ]);
    $thirdPageTransactions = Transaction::factory(4)->transfer()->create([
        'block_id'  => $block->id,
        'timestamp' => Timestamp::now()->sub(2, 'days')->timestamp,
    ]);
    $secondPageTransactions = Transaction::factory(25)->transfer()->create([
        'block_id'  => $block->id,
        'timestamp' => Timestamp::now()->sub(1, 'day')->timestamp,
    ]);
    $visibleTransactions = Transaction::factory(25)->transfer()->create([
        'block_id'  => $block->id,
        'timestamp' => Timestamp::now()->timestamp,
    ]);

    $component = Livewire::test(BlockTransactionsTable::class, ['block' => new BlockViewModel($block)])
        ->assertCount('lazyLoadedData', 25);

    foreach ($visibleTransactions as $transaction) {
        $component->assertSee($transaction->id);
    }

    foreach ($secondPageTransactions as $transaction) {
        $component->assertDontSee($transaction->id);
    }

    foreach ($thirdPageTransactions as $transaction) {
        $component->assertDontSee($transaction->id);
    }

    $component->call('nextPage')
        ->assertCount('lazyLoadedData', 50);

    foreach ($visibleTransactions as $transaction) {
        $component->assertSee($transaction->id);
    }

    foreach ($secondPageTransactions as $transaction) {
        $component->assertSee($transaction->id);
    }

    foreach ($thirdPageTransactions as $transaction) {
        $component->assertDontSee($transaction->id);
    }
});

it('should not go past the last page', function () {
    $block = Block::factory()->create([
        'transactions_count' => 27,
    ]);
    Transaction::factory(2)->transfer()->create([
        'block_id'  => $block->id,
        'timestamp' => Timestamp::now()->sub(1, 'day')->timestamp,
    ]);
    Transaction::factory(25)->transfer()->create([
        'block_id'  => $block->id,
        'timestamp' => Timestamp::now()->timestamp,
    ]);

    Livewire::test(BlockTransactionsTable::class, ['block' => new BlockViewModel($block)])
        ->assertSet('paginators.page', 1)
        ->assertCount('lazyLoadedData', 25)
        ->call('nextPage')
        ->assertSet('paginators.page', 2)
        ->assertCount('lazyLoadedData', 27)
        ->call('nextPage')
        ->assertSet('paginators.page', 2)
        ->assertCount('lazyLoadedData', 27)
        ->call('nextPage')
        ->assertSet('paginators.page', 2)
        ->assertCount('lazyLoadedData', 27);
});
