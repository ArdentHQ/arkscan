<?php

declare(strict_types=1);

use App\Enums\CoreTransactionTypeEnum;
use App\Enums\TransactionTypeGroupEnum;

use App\Http\Livewire\TransactionTable;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use App\ViewModels\ViewModelFactory;
use Livewire\Livewire;
use Ramsey\Uuid\Uuid;

use function Tests\configureExplorerDatabase;

beforeEach(fn () => configureExplorerDatabase());

it('should list the first page of records', function () {
    Transaction::factory(30)->create();

    $component = Livewire::test(TransactionTable::class);

    foreach (ViewModelFactory::paginate(Transaction::latestByTimestamp()->paginate())->items() as $transaction) {
        $component->assertSee($transaction->id());
        $component->assertSee($transaction->timestamp());
        $component->assertSee($transaction->sender());
        $component->assertSee($transaction->recipient());
        $component->assertSee($transaction->fee());
        $component->assertSee($transaction->amount());
    }
});

it('should apply filters', function () {
    $block = Block::factory()->create();
    $wallet = Wallet::factory()->create([
        'public_key' => 'public_key',
        'address'    => 'address',
    ]);

    $component = Livewire::test(TransactionTable::class);

    $notExpected = Transaction::factory(10)->create([
        'id'                => (string) Uuid::uuid4(),
        'block_id'          => $block->id,
        'type'              => TransactionTypeGroupEnum::CORE,
        'type_group'        => CoreTransactionTypeEnum::TRANSFER,
        'sender_public_key' => $wallet->public_key,
        'recipient_id'      => $wallet->address,
        'timestamp'         => 112982056,
        'fee'               => 1e8,
        'amount'            => 1e8,
    ]);

    foreach (ViewModelFactory::collection($notExpected) as $transaction) {
        $component->assertDontSee($transaction->id());
        $component->assertDontSee($transaction->timestamp());
        $component->assertDontSee($transaction->sender());
        $component->assertDontSee($transaction->recipient());
        $component->assertDontSee($transaction->fee());
        $component->assertDontSee($transaction->amount());
    }

    $expected = Transaction::factory(10)->create([
        'type_group' => TransactionTypeGroupEnum::CORE,
        'type'       => CoreTransactionTypeEnum::VOTE,
    ]);

    $component->set('state.type', 'vote');

    foreach (ViewModelFactory::collection($expected) as $transaction) {
        $component->assertSee($transaction->id());
        $component->assertSee($transaction->timestamp());
        $component->assertSee($transaction->sender());
        $component->assertSee($transaction->recipient());
        $component->assertSee($transaction->fee());
        $component->assertSee($transaction->amount());
    }
});

it('should apply filters through an event', function () {
    $block = Block::factory()->create();
    $wallet = Wallet::factory()->create([
        'public_key' => 'public_key',
        'address'    => 'address',
    ]);

    $component = Livewire::test(TransactionTable::class);

    $notExpected = Transaction::factory(10)->create([
        'id'                => (string) Uuid::uuid4(),
        'block_id'          => $block->id,
        'type'              => TransactionTypeGroupEnum::CORE,
        'type_group'        => CoreTransactionTypeEnum::TRANSFER,
        'sender_public_key' => $wallet->public_key,
        'recipient_id'      => $wallet->address,
        'timestamp'         => 112982056,
        'fee'               => 1e8,
        'amount'            => 1e8,
    ]);

    foreach (ViewModelFactory::collection($notExpected) as $transaction) {
        $component->assertDontSee($transaction->id());
        $component->assertDontSee($transaction->timestamp());
        $component->assertDontSee($transaction->sender());
        $component->assertDontSee($transaction->recipient());
        $component->assertDontSee($transaction->fee());
        $component->assertDontSee($transaction->amount());
    }

    $expected = Transaction::factory(10)->create([
        'type_group' => TransactionTypeGroupEnum::CORE,
        'type'       => CoreTransactionTypeEnum::VOTE,
    ]);

    $component->emit('filterTransactionsByType', 'vote');

    foreach (ViewModelFactory::collection($expected) as $transaction) {
        $component->assertSee($transaction->id());
        $component->assertSee($transaction->timestamp());
        $component->assertSee($transaction->sender());
        $component->assertSee($transaction->recipient());
        $component->assertSee($transaction->fee());
        $component->assertSee($transaction->amount());
    }
});
