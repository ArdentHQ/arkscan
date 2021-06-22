<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Http\Livewire\TransactionTable;
use App\Models\Block;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\NumberFormatter;

use App\ViewModels\ViewModelFactory;
use Livewire\Livewire;
use Ramsey\Uuid\Uuid;
use function Tests\configureExplorerDatabase;

beforeEach(fn () => configureExplorerDatabase());

it('should list the first page of records', function () {
    Transaction::factory(30)->transfer()->create();

    $component = Livewire::test(TransactionTable::class);

    foreach (ViewModelFactory::paginate(Transaction::withScope(OrderByTimestampScope::class)->paginate())->items() as $transaction) {
        $component->assertSee($transaction->id());
        $component->assertSee($transaction->timestamp());
        $component->assertSee($transaction->sender()->address());
        $component->assertSee($transaction->recipient()->address());
        $component->assertSee(NumberFormatter::currency($transaction->amount(), Network::currency()));
        $component->assertSee(NumberFormatter::currency($transaction->fee(), Network::currency()));
    }
});

it('should apply filters', function () {
    $block = Block::factory()->create();
    $wallet = Wallet::factory()->create();

    $component = Livewire::test(TransactionTable::class);

    $notExpected = Transaction::factory(10)->transfer()->create([
        'id'                => (string) Uuid::uuid4(),
        'block_id'          => $block->id,
        'sender_public_key' => $wallet->public_key,
        'recipient_id'      => $wallet->address,
        'timestamp'         => 112982056,
        'fee'               => 1e8,
        'amount'            => 1e8,
    ]);

    foreach (ViewModelFactory::collection($notExpected) as $transaction) {
        $component->assertDontSee($transaction->id());
        $component->assertDontSee($transaction->timestamp());
        $component->assertDontSee($transaction->sender()->address());
        $component->assertDontSee($transaction->recipient()->address());
        $component->assertDontSee(NumberFormatter::currency($transaction->fee(), Network::currency()));
        $component->assertDontSee(NumberFormatter::currency($transaction->amount(), Network::currency()));
    }

    $expected = Transaction::factory(10)->vote()->create(['asset' => null]);

    $component->set('state.type', 'vote');

    foreach (ViewModelFactory::collection($expected) as $transaction) {
        $component->assertSee($transaction->id());
        $component->assertSee($transaction->timestamp());
        $component->assertSee($transaction->sender()->address());
        $component->assertSee($transaction->recipient()->address());
        $component->assertSee(NumberFormatter::currency($transaction->fee(), Network::currency()));
        $component->assertSee(NumberFormatter::currency($transaction->amount(), Network::currency()));
    }
});

it('should apply filters through an event', function () {
    $block = Block::factory()->create();
    $wallet = Wallet::factory()->create();

    $component = Livewire::test(TransactionTable::class);

    $notExpected = Transaction::factory(10)->transfer()->create([
        'id'                => (string) Uuid::uuid4(),
        'block_id'          => $block->id,
        'sender_public_key' => $wallet->public_key,
        'recipient_id'      => $wallet->address,
        'timestamp'         => 112982056,
        'fee'               => 1e8,
        'amount'            => 1e8,
    ]);

    foreach (ViewModelFactory::collection($notExpected) as $transaction) {
        $component->assertDontSee($transaction->id());
        $component->assertDontSee($transaction->timestamp());
        $component->assertDontSee($transaction->sender()->address());
        $component->assertDontSee($transaction->recipient()->address());
        $component->assertDontSee(NumberFormatter::currency($transaction->fee(), Network::currency()));
        $component->assertDontSee(NumberFormatter::currency($transaction->amount(), Network::currency()));
    }

    $expected = Transaction::factory(10)->vote()->create(['asset' => null]);

    $component->set('state.type', 'vote');

    foreach (ViewModelFactory::collection($expected) as $transaction) {
        $component->assertSee($transaction->id());
        $component->assertSee($transaction->timestamp());
        $component->assertSee($transaction->sender()->address());
        $component->assertSee($transaction->recipient()->address());
        $component->assertSee(NumberFormatter::currency($transaction->fee(), Network::currency()));
        $component->assertSee(NumberFormatter::currency($transaction->amount(), Network::currency()));
    }
});
