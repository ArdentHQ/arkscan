<?php

declare(strict_types=1);

use App\Enums\CoreTransactionTypeEnum;
use App\Enums\TransactionTypeGroupEnum;
use App\Facades\Network;
use App\Http\Livewire\WalletTransactionTable;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\NumberFormatter;
use App\ViewModels\ViewModelFactory;
use Livewire\Livewire;
use Ramsey\Uuid\Uuid;
use function Tests\bip39;
use function Tests\configureExplorerDatabase;
use function Tests\fakeCryptoCompare;

beforeEach(function () {
    fakeCryptoCompare();

    configureExplorerDatabase();

    $this->subject = Wallet::factory()->create(['public_key' => bip39()]);
});

it('should list all transactions', function () {
    $sent = Transaction::factory()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $received = Transaction::factory()->create([
        'recipient_id' => $this->subject->address,
    ]);

    $component = Livewire::test(WalletTransactionTable::class, [$this->subject->address, $this->subject->public_key]);
    $component->set('state.direction', 'all');

    foreach (ViewModelFactory::collection(collect([$sent, $received])) as $transaction) {
        $component->assertSee($transaction->id());
        $component->assertSee($transaction->timestamp());
        $component->assertSee($transaction->sender()->address());
        $component->assertSee($transaction->recipient()->address());
        $component->assertSee(NumberFormatter::currency($transaction->fee(), Network::currency()));
        $component->assertSee(NumberFormatter::currency($transaction->amount(), Network::currency()));
    }
});

it('should list received transactions (non-multi)', function () {
    $sent = Transaction::factory()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $received = Transaction::factory()->create([
        'recipient_id' => $this->subject->address,
    ]);

    $component = Livewire::test(WalletTransactionTable::class, [$this->subject->address, $this->subject->public_key]);
    $component->set('state.direction', 'received');

    foreach (ViewModelFactory::collection(collect([$received])) as $transaction) {
        $component->assertSee($transaction->id());
        $component->assertSee($transaction->timestamp());
        $component->assertSee($transaction->sender()->address());
        $component->assertSee($transaction->recipient()->address());
        $component->assertSee(NumberFormatter::currency($transaction->fee(), Network::currency()));
        $component->assertSee(NumberFormatter::currency($transaction->amount(), Network::currency()));
    }

    foreach (ViewModelFactory::collection(collect([$sent])) as $transaction) {
        $component->assertDontSee($transaction->id());
    }
});

it('should list received transactions (multi)', function () {
    $sent = Transaction::factory()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $received = Transaction::factory()->create([
        'asset' => [
            'payments' => [
                [
                    'amount'      => '100000000',
                    'recipientId' => $this->subject->address,
                ],
            ],
        ],
    ]);

    $component = Livewire::test(WalletTransactionTable::class, [$this->subject->address, $this->subject->public_key]);
    $component->set('state.direction', 'received');

    foreach (ViewModelFactory::collection(collect([$received])) as $transaction) {
        $component->assertSee($transaction->id());
        $component->assertSee($transaction->timestamp());
        $component->assertSee($transaction->sender()->address());
        $component->assertSee($transaction->recipient()->address());
        $component->assertSee(NumberFormatter::currency($transaction->fee(), Network::currency()));
        $component->assertSee(NumberFormatter::currency($transaction->amount(), Network::currency()));
    }

    foreach (ViewModelFactory::collection(collect([$sent])) as $transaction) {
        $component->assertDontSee($transaction->id());
    }
});

it('should list sent transactions', function () {
    $sent = Transaction::factory()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $received = Transaction::factory()->create([
        'recipient_id' => $this->subject->address,
    ]);

    $component = Livewire::test(WalletTransactionTable::class, [$this->subject->address, $this->subject->public_key]);
    $component->set('state.direction', 'sent');

    foreach (ViewModelFactory::collection(collect([$sent])) as $transaction) {
        $component->assertSee($transaction->id());
        $component->assertSee($transaction->timestamp());
        $component->assertSee($transaction->sender()->address());
        $component->assertSee($transaction->recipient()->address());
        $component->assertSee(NumberFormatter::currency($transaction->fee(), Network::currency()));
        $component->assertSee(NumberFormatter::currency($transaction->amount(), Network::currency()));
    }

    foreach (ViewModelFactory::collection(collect([$received])) as $transaction) {
        $component->assertDontSee($transaction->id());
    }
});

it('should apply filters', function () {
    $block = Block::factory()->create();

    $component = Livewire::test(WalletTransactionTable::class, [$this->subject->address, $this->subject->public_key]);

    $notExpected = Transaction::factory(10)->create([
        'id'                => (string) Uuid::uuid4(),
        'block_id'          => $block->id,
        'type'              => TransactionTypeGroupEnum::CORE,
        'type_group'        => CoreTransactionTypeEnum::TRANSFER,
        'sender_public_key' => $this->subject->public_key,
        'recipient_id'      => $this->subject->address,
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

    $expected = Transaction::factory(10)->create([
        'sender_public_key' => $this->subject->public_key,
        'type_group'        => TransactionTypeGroupEnum::CORE,
        'type'              => CoreTransactionTypeEnum::VOTE,
    ]);

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

    $component = Livewire::test(WalletTransactionTable::class, [$this->subject->address, $this->subject->public_key]);

    $notExpected = Transaction::factory(10)->create([
        'id'                => (string) Uuid::uuid4(),
        'block_id'          => $block->id,
        'type'              => TransactionTypeGroupEnum::CORE,
        'type_group'        => CoreTransactionTypeEnum::TRANSFER,
        'sender_public_key' => $this->subject->public_key,
        'recipient_id'      => $this->subject->address,
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

    $expected = Transaction::factory(10)->create([
        'sender_public_key' => $this->subject->public_key,
        'type_group'        => TransactionTypeGroupEnum::CORE,
        'type'              => CoreTransactionTypeEnum::VOTE,
    ]);

    $component->emit('filterTransactionsByType', 'vote');

    foreach (ViewModelFactory::collection($expected) as $transaction) {
        $component->assertSee($transaction->id());
        $component->assertSee($transaction->timestamp());
        $component->assertSee($transaction->sender()->address());
        $component->assertSee($transaction->recipient()->address());
        $component->assertSee(NumberFormatter::currency($transaction->fee(), Network::currency()));
        $component->assertSee(NumberFormatter::currency($transaction->amount(), Network::currency()));
    }
});

it('should apply directions through an event', function () {
    $sent = Transaction::factory()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $received = Transaction::factory()->create([
        'recipient_id' => $this->subject->address,
    ]);

    $component = Livewire::test(WalletTransactionTable::class, [$this->subject->address, $this->subject->public_key]);

    $component->emit('filterTransactionsByDirection', 'all');

    foreach (ViewModelFactory::collection(collect([$sent, $received])) as $transaction) {
        $component->assertSee($transaction->id());
        $component->assertSee($transaction->timestamp());
        $component->assertSee($transaction->sender()->address());
        $component->assertSee($transaction->recipient()->address());
        $component->assertSee(NumberFormatter::currency($transaction->fee(), Network::currency()));
        $component->assertSee(NumberFormatter::currency($transaction->amount(), Network::currency()));
    }

    $component->emit('filterTransactionsByDirection', 'received');

    $component->assertSee($received->id);
    $component->assertDontSee($sent->id);

    $component->emit('filterTransactionsByDirection', 'sent');

    $component->assertDontSee($received->id);
    $component->assertSee($sent->id);

    $component->emit('filterTransactionsByDirection', 'all');

    $component->assertSee($received->id);
    $component->assertSee($sent->id);
});
