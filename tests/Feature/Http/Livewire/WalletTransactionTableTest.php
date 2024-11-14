<?php

declare(strict_types=1);

use App\Http\Livewire\WalletTransactionTable;
use App\Models\Transaction;
use App\Models\Wallet;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use Livewire\Livewire;
use function Tests\fakeCryptoCompare;

beforeEach(function () {
    fakeCryptoCompare();

    $this->subject = Wallet::factory()->create();
});

it('should list all transactions', function () {
    $sent = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $received = Transaction::factory()->transfer()->create([
        'recipient_id' => $this->subject->address,
    ]);

    $component = Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady');

    $sentTransaction = ViewModelFactory::make($sent);

    $component->assertSee($sentTransaction->id());
    $component->assertSee($sentTransaction->timestamp());
    $component->assertSee(sprintf(
        '%s…%s',
        substr($sentTransaction->recipient()->address(), 0, 5),
        substr($sentTransaction->recipient()->address(), -5)
    ));
    $component->assertDontSee(sprintf(
        '%s…%s',
        substr($sentTransaction->sender()->address(), 0, 5),
        substr($sentTransaction->sender()->address(), -5)
    ));
    $component->assertSeeInOrder([
        '-',
        $sentTransaction->amount(),
        $sentTransaction->fee(),
    ]);

    $receivedTransaction = ViewModelFactory::make($received);

    $component->assertSee($receivedTransaction->id());
    $component->assertSee($receivedTransaction->timestamp());
    $component->assertSee(sprintf(
        '%s…%s',
        substr($receivedTransaction->sender()->address(), 0, 5),
        substr($receivedTransaction->sender()->address(), -5)
    ));
    $component->assertDontSee(sprintf(
        '%s…%s',
        substr($receivedTransaction->recipient()->address(), 0, 5),
        substr($receivedTransaction->recipient()->address(), -5)
    ));
    $component->assertSeeInOrder([
        '+',
        $receivedTransaction->amount(),
        $receivedTransaction->fee(),
    ]);
});

it('should list all transactions for cold wallet', function () {
    $received = Transaction::factory()->transfer()->create([
        'recipient_id' => $this->subject->address,
    ]);

    $component = Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady');

    $transaction = ViewModelFactory::make($received);

    $component->assertSee($transaction->id());
    $component->assertSee($transaction->timestamp());
    $component->assertSee(sprintf(
        '%s…%s',
        substr($transaction->sender()->address(), 0, 5),
        substr($transaction->sender()->address(), -5)
    ));
    $component->assertDontSee(sprintf(
        '%s…%s',
        substr($transaction->recipient()->address(), 0, 5),
        substr($transaction->recipient()->address(), -5)
    ));
    $component->assertSeeInOrder([
        '+',
        $transaction->amount(),
        $transaction->fee(),
    ]);
});

it('should show transfer without amount sent to self', function () {
    $sent = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
        'recipient_id'      => $this->subject->address,
    ]);

    $component = Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady');

    $transaction = ViewModelFactory::make($sent);

    $component->assertSee($transaction->id());
    $component->assertSee($transaction->timestamp());
    $component->assertSee(sprintf(
        '%s…%s',
        substr($this->subject->address, 0, 5),
        substr($this->subject->address, -5)
    ));
    $component->assertSeeInOrder([
        '-',
        '0.00',
        $transaction->fee(),
    ]);
});

it('should toggle all filters when "select all" is selected', function () {
    Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady')
        ->assertSet('filter', [
            'outgoing'  => true,
            'incoming'  => true,
            'transfers' => true,
            'votes'     => true,
            'others'    => true,
        ])
        ->assertSet('selectAllFilters', true)
        ->set('filter.outgoing', true)
        ->assertSet('selectAllFilters', true)
        ->set('selectAllFilters', false)
        ->assertSet('filter', [
            'outgoing'  => false,
            'incoming'  => false,
            'transfers' => false,
            'votes'     => false,
            'others'    => false,
        ])
        ->set('selectAllFilters', true)
        ->assertSet('filter', [
            'outgoing'  => true,
            'incoming'  => true,
            'transfers' => true,
            'votes'     => true,
            'others'    => true,
        ]);
});

it('should toggle "select all" when all filters are selected', function () {
    Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady')
        ->assertSet('filter', [
            'outgoing'  => true,
            'incoming'  => true,
            'transfers' => true,
            'votes'     => true,
            'others'    => true,
        ])
        ->assertSet('selectAllFilters', true)
        ->set('filter.outgoing', false)
        ->assertSet('selectAllFilters', false)
        ->set('filter.outgoing', true)
        ->assertSet('selectAllFilters', true);
});

it('should filter by outgoing transactions', function () {
    $sent = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $received = Transaction::factory()->transfer()->create([
        'recipient_id' => $this->subject->address,
    ]);

    Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady')
        ->set('filter', [
            'outgoing'  => true,
            'incoming'  => false,
            'transfers' => true,
            'votes'     => false,
            'others'    => false,
        ])
        ->assertSee($sent->id)
        ->assertDontSee($received->id);
});

it('should filter by incoming transactions', function () {
    $sent = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $received = Transaction::factory()->transfer()->create([
        'recipient_id' => $this->subject->address,
    ]);

    Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady')
        ->set('filter', [
            'outgoing'  => false,
            'incoming'  => true,
            'transfers' => true,
            'votes'     => false,
            'others'    => false,
        ])
        ->assertSee($received->id)
        ->assertDontSee($sent->id);
});

it('should filter by incoming and outgoing transactions', function () {
    $sent = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $received = Transaction::factory()->transfer()->create([
        'recipient_id' => $this->subject->address,
    ]);

    Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady')
        ->set('filter', [
            'outgoing'  => true,
            'incoming'  => true,
            'transfers' => true,
            'votes'     => false,
            'others'    => false,
        ])
        ->assertSee($sent->id)
        ->assertSee($received->id);
});

it('should filter by transfer transactions', function () {
    $transfer = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $vote = Transaction::factory()->vote()->create([
        'sender_public_key' => $this->subject->public_key,
        'asset'             => [
            'votes' => [$this->subject->address],
        ],
    ]);

    Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady')
        ->set('filter', [
            'outgoing'  => true,
            'incoming'  => false,
            'transfers' => true,
            'votes'     => false,
            'others'    => false,
        ])
        ->assertSee($transfer->id)
        ->assertDontSee($vote->id);
});

it('should filter by vote transactions', function () {
    $transfer = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $vote = Transaction::factory()->vote()->create([
        'sender_public_key' => $this->subject->public_key,
        'asset'             => [
            'votes' => [$this->subject->address],
        ],
    ]);

    Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady')
        ->set('filter', [
            'outgoing'  => true,
            'incoming'  => false,
            'transfers' => false,
            'votes'     => true,
            'others'    => false,
        ])
        ->assertSee($vote->id)
        ->assertDontSee($transfer->id);
});

it('should filter by other transactions', function () {
    $transfer = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $validatorRegistration = Transaction::factory()->validatorRegistration()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady')
        ->set('filter', [
            'outgoing'  => true,
            'incoming'  => false,
            'transfers' => false,
            'votes'     => false,
            'others'    => true,
        ])
        ->assertSee($validatorRegistration->id)
        ->assertDontSee($transfer->id);
});

it('should show no transactions if no filters', function () {
    $transfer = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $validatorRegistration = Transaction::factory()->validatorRegistration()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady')
        ->set('filter', [
            'outgoing'  => false,
            'incoming'  => false,
            'transfers' => false,
            'votes'     => false,
            'others'    => false,
        ])
        ->assertDontSee($transfer->id)
        ->assertDontSee($validatorRegistration->id)
        ->assertSee(trans('tables.transactions.no_results.no_filters'));
});

it('should show no transactions if no addressing filter', function () {
    $transfer = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $validatorRegistration = Transaction::factory()->validatorRegistration()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady')
        ->set('filter', [
            'outgoing'  => false,
            'incoming'  => false,
            'transfers' => true,
            'votes'     => true,
            'others'    => true,
        ])
        ->assertDontSee($transfer->id)
        ->assertDontSee($validatorRegistration->id)
        ->assertSee(trans('tables.transactions.no_results.no_addressing_filters'));
});

it('should show no transactions if no type filter', function () {
    $transfer = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $validatorRegistration = Transaction::factory()->validatorRegistration()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady')
        ->set('filter', [
            'outgoing'  => true,
            'incoming'  => true,
            'transfers' => false,
            'votes'     => false,
            'others'    => false,
        ])
        ->assertDontSee($transfer->id)
        ->assertDontSee($validatorRegistration->id)
        ->assertSee(trans('tables.transactions.no_results.no_results'));
});

it('should show no results message if no transactions matching filter', function () {
    Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady')
        ->assertSee(trans('tables.transactions.no_results.no_results'));
});

it('should reset pagination when filtering', function () {
    $vote = Transaction::factory()->vote()->create([
        'sender_public_key' => $this->subject->public_key,
        'timestamp'         => 102982050, // oldest transaction
        'asset'             => [
            'votes' => [$this->subject->address],
        ],
    ]);

    Transaction::factory(30)->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady')
        ->assertDontSee($vote->id)
        ->call('setPage', 2)
        ->assertSee($vote->id)
        ->set('filter.transfers', false)
        ->assertSet('paginators.page', 1)
        ->assertSee($vote->id);
});

it('should show no data if not ready', function () {
    $transaction = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    Livewire::test(WalletTransactionTable::class, [ViewModelFactory::make($this->subject)])
        ->assertDontSee($transaction->id)
        ->call('setIsReady')
        ->assertSee($transaction->id);
});
