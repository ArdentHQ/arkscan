<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Http\Livewire\LatestRecords;
use App\Models\Block;
use App\Models\Scopes\OrderByHeightScope;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\NumberFormatter;
use App\ViewModels\ViewModelFactory;
use Livewire\Livewire;
use Ramsey\Uuid\Uuid;

it('should list the first page of blocks', function () {
    Block::factory(30)->create();

    $component = Livewire::test(LatestRecords::class)
        ->set('state.selected', 'blocks')
        ->call('pollBlocks');

    foreach (ViewModelFactory::collection(Block::withScope(OrderByHeightScope::class)->take(15)->get()) as $block) {
        $component->assertSee($block->id());
        $component->assertSee($block->timestamp());
        $component->assertSee($block->username());
        $component->assertSee(NumberFormatter::number($block->height()));
        $component->assertSee(NumberFormatter::number($block->transactionCount()));
        $component->assertSee(NumberFormatter::currency($block->amount(), Network::currency()));
        $component->assertSee(NumberFormatter::currency($block->fee(), Network::currency()));
    }
});

it('should list the first page of transactions', function () {
    Transaction::factory(30)->transfer()->create();

    $component = Livewire::test(LatestRecords::class)
        ->call('pollTransactions');

    foreach (ViewModelFactory::collection(Transaction::withScope(OrderByTimestampScope::class)->take(15)->get()) as $transaction) {
        $component->assertSee($transaction->id());
        $component->assertSee($transaction->timestamp());
        $component->assertSee($transaction->sender()->address());
        $component->assertSee($transaction->recipient()->address());
        $component->assertSee(NumberFormatter::currency($transaction->amount(), Network::currency()));
        $component->assertSee(NumberFormatter::currency($transaction->fee(), Network::currency()));
    }
});

it('should apply filters for transactions', function () {
    $block  = Block::factory()->create();
    $wallet = Wallet::factory()->create();

    $component = Livewire::test(LatestRecords::class)
        ->call('pollBlocks');

    $notExpected = Transaction::factory(10)->transfer()->create([
        'id'                => (string) Uuid::uuid4(),
        'block_id'          => $block->id,
        'sender_public_key' => $wallet->public_key,
        'recipient_id'      => $wallet->address,
        'timestamp'         => 112982056,
        'fee'               => 192837,
        'amount'            => 237461,
    ]);

    foreach (ViewModelFactory::collection($notExpected) as $transaction) {
        $component->assertDontSee($transaction->id());
        $component->assertDontSee($transaction->timestamp());
        $component->assertDontSee($transaction->sender()->address());
        $component->assertDontSee($transaction->recipient()->address());
        $component->assertDontSee($transaction->fee());
        $component->assertDontSee($transaction->amount());
    }

    $expected = Transaction::factory(10)->vote()->create(['asset' => null]);

    $component->set('state.type', 'vote');

    foreach (ViewModelFactory::collection($expected) as $transaction) {
        $component->assertSee($transaction->id());
        $component->assertSee($transaction->timestamp());
        $component->assertSee($transaction->sender()->address());
        $component->assertSee($transaction->recipient()->address());
        $component->assertSee($transaction->fee());
        $component->assertSee($transaction->amount());
    }
});

it('should apply filters through an event for transactions', function () {
    $block  = Block::factory()->create();
    $wallet = Wallet::factory()->create();

    $component = Livewire::test(LatestRecords::class)
        ->call('pollTransactions');

    $notExpected = Transaction::factory(10)->transfer()->create([
        'id'                => (string) Uuid::uuid4(),
        'block_id'          => $block->id,
        'sender_public_key' => $wallet->public_key,
        'recipient_id'      => $wallet->address,
        'timestamp'         => 112982056,
        'fee'               => 192837,
        'amount'            => 237461,
    ]);

    foreach (ViewModelFactory::collection($notExpected) as $transaction) {
        $component->assertDontSee($transaction->id());
        $component->assertDontSee($transaction->timestamp());
        $component->assertDontSee($transaction->sender()->address());
        $component->assertDontSee($transaction->recipient()->address());
        $component->assertDontSee($transaction->fee());
        $component->assertDontSee($transaction->amount());
    }

    $expected = Transaction::factory(10)->vote()->create(['asset' => null]);

    $component->set('state.type', 'vote');

    foreach (ViewModelFactory::collection($expected) as $transaction) {
        $component->assertSee($transaction->id());
        $component->assertSee($transaction->timestamp());
        $component->assertSee($transaction->sender()->address());
        $component->assertSee($transaction->recipient()->address());
        $component->assertSee($transaction->fee());
        $component->assertSee($transaction->amount());
    }
});

it('should poll transactions when currency changed', function () {
    $transaction = Transaction::factory()->create();

    Livewire::test(LatestRecords::class)
        ->assertDontSee($transaction->id)
        ->emit('currencyChanged')
        ->assertSee($transaction->id);
});

it('should poll blocks when currency changed', function () {
    $block = Block::factory()->create();

    Livewire::test(LatestRecords::class)
        ->set('state.selected', 'blocks')
        ->assertDontSee($block->id)
        ->emit('currencyChanged')
        ->assertSee($block->id);
});
