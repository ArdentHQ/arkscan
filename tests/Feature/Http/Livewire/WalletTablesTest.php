<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Http\Livewire\WalletTables;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\NumberFormatter;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use Livewire\Livewire;
use Ramsey\Uuid\Uuid;
use function Tests\fakeCryptoCompare;

beforeEach(function () {
    fakeCryptoCompare();

    $this->subject = Wallet::factory()->create();
});

it('should list all transactions', function () {
    $sent = Transaction::factory()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $received = Transaction::factory()->create([
        'recipient_id' => $this->subject->address,
    ]);

    $component = Livewire::test(WalletTables::class, [new WalletViewModel($this->subject)]);
    $component->set('state.view', 'transactions');

    foreach (ViewModelFactory::collection(collect([$sent, $received])) as $transaction) {
        $component->assertSee($transaction->id());
        $component->assertSee($transaction->timestamp());
        $component->assertSee($transaction->sender()->address());
        $component->assertSee($transaction->recipient()->address());
        $component->assertSee(NumberFormatter::currency($transaction->fee(), Network::currency()));
        $component->assertSee(NumberFormatter::currency($transaction->amount(), Network::currency()));
    }
});

it('should list all transactions for cold wallet', function () {
    $received = Transaction::factory()->create([
        'recipient_id' => $this->subject->address,
    ]);

    $component = Livewire::test(WalletTables::class, [new WalletViewModel($this->subject)]);
    $component->set('state.view', 'transactions');

    $transaction = ViewModelFactory::make($received);
    $component->assertSee($transaction->id());
    $component->assertSee($transaction->timestamp());
    $component->assertSee($transaction->sender()->address());
    $component->assertSee($transaction->recipient()->address());
    $component->assertSee(NumberFormatter::currency($transaction->fee(), Network::currency()));
    $component->assertSee(NumberFormatter::currency($transaction->amount(), Network::currency()));
});

it('should list blocks', function () {
    $sent = Transaction::factory()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $received = Transaction::factory()->create([
        'recipient_id' => $this->subject->address,
    ]);

    $component = Livewire::test(WalletTables::class, [new WalletViewModel($this->subject)]);
    $component->set('state.view', 'blocks');

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
})->skip('not implemented');

it('should list voters', function () {
    $sent = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $received = Transaction::factory()->create([
        'recipient_id' => $this->subject->address,
    ]);

    $component = Livewire::test(WalletTables::class, [new WalletViewModel($this->subject)]);
    $component->set('state.view', 'sent');

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
})->skip('not implemented');

it('should reset the pagination when state changes', function () {
    $component = Livewire::test(WalletTables::class, [new WalletViewModel($this->subject)]);

    $component->set('page', 3);
    $component->assertSet('page', 3);

    $component->set('state.view', 'blocks');
    $component->assertSet('page', 1);
});
