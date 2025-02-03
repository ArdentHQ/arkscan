<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Http\Livewire\WalletTransactionTable;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\NumberFormatter;
use App\ViewModels\TransactionViewModel;
use App\ViewModels\WalletViewModel;
use Carbon\Carbon;
use Livewire\Livewire;
use function Tests\fakeCryptoCompare;
use function Tests\faker;

beforeEach(function () {
    fakeCryptoCompare();

    $this->subject = Wallet::factory()->create();
});

it('should list all transactions', function () {
    $sent = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ])->fresh();

    $received = Transaction::factory()->transfer()->create([
        'recipient_address' => $this->subject->address,
    ])->fresh();

    $component = Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady');

    $sentTransaction = new TransactionViewModel($sent);

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
        NumberFormatter::number($sentTransaction->fee()),
    ]);

    $receivedTransaction = new TransactionViewModel($received);

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
        NumberFormatter::number($receivedTransaction->fee()),
    ]);
});

it('should list all transactions for cold wallet', function () {
    $received = Transaction::factory()->transfer()->create([
        'recipient_address' => $this->subject->address,
    ])->fresh();

    $component = Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady');

    $transaction = new TransactionViewModel($received);

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
        NumberFormatter::number($transaction->fee()),
    ]);
});

it('should show transfer without amount sent to self', function () {
    $sent = Transaction::factory()->transfer()->create([
        'sender_public_key'      => $this->subject->public_key,
        'recipient_address'      => $this->subject->address,
    ])->fresh();

    $component = Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady');

    $transaction = new TransactionViewModel($sent);

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
        NumberFormatter::number($transaction->fee()),
    ]);
});

it('should toggle all filters when "select all" is selected', function () {
    Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady')
        ->assertSet('filter', [
            'outgoing'            => true,
            'incoming'            => true,
            'transfers'           => true,
            'multipayments'       => true,
            'votes'               => true,
            'validator'           => true,
            'username'            => true,
            'contract_deployment' => true,
            'others'              => true,
        ])
        ->assertSet('selectAllFilters', true)
        ->set('filter.outgoing', true)
        ->assertSet('selectAllFilters', true)
        ->set('selectAllFilters', false)
        ->assertSet('filter', [
            'outgoing'            => false,
            'incoming'            => false,
            'transfers'           => false,
            'multipayments'       => false,
            'votes'               => false,
            'validator'           => false,
            'username'            => false,
            'contract_deployment' => false,
            'others'              => false,
        ])
        ->set('selectAllFilters', true)
        ->assertSet('filter', [
            'outgoing'            => true,
            'incoming'            => true,
            'transfers'           => true,
            'multipayments'       => true,
            'votes'               => true,
            'validator'           => true,
            'username'            => true,
            'contract_deployment' => true,
            'others'              => true,
        ]);
});

it('should toggle "select all" when all filters are selected', function () {
    Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady')
        ->assertSet('filter', [
            'outgoing'            => true,
            'incoming'            => true,
            'transfers'           => true,
            'multipayments'       => true,
            'votes'               => true,
            'validator'           => true,
            'username'            => true,
            'contract_deployment' => true,
            'others'              => true,
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
        'recipient_address' => $this->subject->address,
    ]);

    Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady')
        ->set('filter', [
            'outgoing'            => true,
            'incoming'            => false,
            'transfers'           => true,
            'multipayments'       => false,
            'votes'               => false,
            'validator'           => false,
            'username'            => false,
            'contract_deployment' => false,
            'others'              => false,
        ])
        ->assertSee($sent->id)
        ->assertDontSee($received->id);
});

it('should filter by incoming transactions', function () {
    $sent = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $received = Transaction::factory()->transfer()->create([
        'recipient_address' => $this->subject->address,
    ]);

    Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady')
        ->set('filter', [
            'outgoing'            => false,
            'incoming'            => true,
            'transfers'           => true,
            'multipayments'       => false,
            'votes'               => false,
            'validator'           => false,
            'username'            => false,
            'contract_deployment' => false,
            'others'              => false,
        ])
        ->assertSee($received->id)
        ->assertDontSee($sent->id);
});

it('should filter by incoming and outgoing transactions', function () {
    $sent = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
        'recipient_address' => $this->subject->address,
    ]);

    $received = Transaction::factory()->transfer()->create([
        'recipient_address' => $this->subject->address,
    ]);

    Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady')
        ->set('filter', [
            'outgoing'            => true,
            'incoming'            => true,
            'transfers'           => true,
            'multipayments'       => false,
            'votes'               => false,
            'validator'           => false,
            'username'            => false,
            'contract_deployment' => false,
            'others'              => false,
        ])
        ->assertSee($sent->id)
        ->assertSee($received->id);
});

it('should filter by transfer transactions', function () {
    $transfer = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $vote = Transaction::factory()->vote($this->subject->address)->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady')
        ->set('filter', [
            'outgoing'            => true,
            'incoming'            => false,
            'transfers'           => true,
            'multipayments'       => false,
            'votes'               => false,
            'validator'           => false,
            'username'            => false,
            'contract_deployment' => false,
            'others'              => false,
        ])
        ->assertSee($transfer->id)
        ->assertDontSee($vote->id);
});

it('should filter by multipayment transactions', function () {
    $transfer = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $multiPayment = Transaction::factory()
        ->multiPayment([faker()->wallet['address']], [BigNumber::new(1 * 1e18)])
        ->create([
            'sender_public_key' => $this->subject->public_key,
        ]);

    Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady')
        ->set('filter', [
            'outgoing'            => true,
            'incoming'            => false,
            'transfers'           => false,
            'multipayments'       => true,
            'votes'               => false,
            'validator'           => false,
            'username'            => false,
            'contract_deployment' => false,
            'others'              => false,
        ])
        ->assertSee($multiPayment->id)
        ->assertDontSee($transfer->id);
});

it('should filter by vote transactions', function () {
    $vote = Transaction::factory()->vote($this->subject->address)->create([
        'sender_public_key' => $this->subject->public_key,
        'timestamp'         => Carbon::parse('2024-11-03 11:33:44')->getTimestampMs(), // oldest transaction
    ]);

    $transfer = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
        'timestamp'         => Carbon::parse('2024-11-04 11:33:44')->getTimestampMs(),
    ]);

    $unvote = Transaction::factory()->unvote()->create([
        'sender_public_key' => $this->subject->public_key,
        'timestamp'         => Carbon::parse('2024-11-03 11:33:44')->getTimestampMs(), // oldest transaction
    ]);

    Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady')
        ->assertSee($transfer->id)
        ->assertSee($unvote->id)
        ->set('filter', [
            'outgoing'            => true,
            'incoming'            => false,
            'transfers'           => false,
            'multipayments'       => false,
            'votes'               => true,
            'validator'           => false,
            'username'            => false,
            'contract_deployment' => false,
            'others'              => false,
        ])
        ->assertDontSee($transfer->id)
        ->assertSee($vote->id)
        ->assertSee($unvote->id);
});

it('should filter by validator transactions', function () {
    $registration = Transaction::factory()->validatorRegistration()->create([
        'sender_public_key' => $this->subject->public_key,
        'timestamp'         => Carbon::parse('2024-11-03 11:33:44')->getTimestampMs(), // oldest transaction
    ]);

    $transfer = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
        'timestamp'         => Carbon::parse('2024-11-04 11:33:44')->getTimestampMs(),
    ]);

    $resignation = Transaction::factory()->validatorResignation()->create([
        'sender_public_key' => $this->subject->public_key,
        'timestamp'         => Carbon::parse('2024-11-03 11:33:44')->getTimestampMs(), // oldest transaction
    ]);

    Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady')
        ->assertSee($transfer->id)
        ->assertSee($resignation->id)
        ->set('filter', [
            'outgoing'            => true,
            'incoming'            => false,
            'transfers'           => false,
            'multipayments'       => false,
            'votes'               => false,
            'validator'           => true,
            'username'            => false,
            'contract_deployment' => false,
            'others'              => false,
        ])
        ->assertDontSee($transfer->id)
        ->assertSee($registration->id)
        ->assertSee($resignation->id);
});

it('should filter by username transactions', function () {
    $registration = Transaction::factory()->usernameRegistration()->create([
        'sender_public_key' => $this->subject->public_key,
        'timestamp'         => Carbon::parse('2024-11-03 11:33:44')->getTimestampMs(), // oldest transaction
    ]);

    $transfer = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
        'timestamp'         => Carbon::parse('2024-11-04 11:33:44')->getTimestampMs(),
    ]);

    $resignation = Transaction::factory()->usernameResignation()->create([
        'sender_public_key' => $this->subject->public_key,
        'timestamp'         => Carbon::parse('2024-11-03 11:33:44')->getTimestampMs(), // oldest transaction
    ]);

    Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady')
        ->assertSee($transfer->id)
        ->assertSee($resignation->id)
        ->set('filter', [
            'outgoing'            => true,
            'incoming'            => false,
            'transfers'           => false,
            'multipayments'       => false,
            'votes'               => false,
            'validator'           => false,
            'username'            => true,
            'contract_deployment' => false,
            'others'              => false,
        ])
        ->assertDontSee($transfer->id)
        ->assertSee($registration->id)
        ->assertSee($resignation->id);
});

it('should filter by contract deployment transactions', function () {
    $contractDeployment = Transaction::factory()->contractDeployment()->create([
        'sender_public_key' => $this->subject->public_key,
        'timestamp'         => Carbon::parse('2024-11-03 11:33:44')->getTimestampMs(), // oldest transaction
    ]);

    $transfer = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
        'timestamp'         => Carbon::parse('2024-11-04 11:33:44')->getTimestampMs(),
    ]);

    Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady')
        ->assertSee($transfer->id)
        ->assertSee($contractDeployment->id)
        ->set('filter', [
            'outgoing'            => true,
            'incoming'            => false,
            'transfers'           => false,
            'multipayments'       => false,
            'votes'               => false,
            'validator'           => false,
            'username'            => false,
            'contract_deployment' => true,
            'others'              => false,
        ])
        ->assertDontSee($transfer->id)
        ->assertSee($contractDeployment->id);
});

it('should filter by other transactions to consensus address', function () {
    $transfer = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $other = Transaction::factory()->withPayload('12345678')->create([
        'sender_public_key' => $this->subject->public_key,
        'recipient_address' => Network::knownContract('consensus'),
    ]);

    Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady')
        ->set('filter', [
            'outgoing'            => true,
            'incoming'            => false,
            'transfers'           => false,
            'multipayments'       => false,
            'votes'               => false,
            'validator'           => false,
            'username'            => false,
            'contract_deployment' => false,
            'others'              => true,
        ])
        ->assertSee($other->id)
        ->assertDontSee($transfer->id);
});

it('should filter by other transactions to non-consensus address', function () {
    $transfer = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $other = Transaction::factory()->withPayload('12345678')->create([
        'sender_public_key' => $this->subject->public_key,
        'recipient_address' => 'not consensus address',
    ]);

    Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady')
        ->set('filter', [
            'outgoing'            => true,
            'incoming'            => false,
            'transfers'           => false,
            'multipayments'       => false,
            'votes'               => false,
            'validator'           => false,
            'username'            => false,
            'contract_deployment' => false,
            'others'              => true,
        ])
        ->assertSee($other->id)
        ->assertDontSee($transfer->id);
});

it('should not filter transfers to consensus as "other"', function () {
    $transfer = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $other = Transaction::factory()->create([
        'sender_public_key' => $this->subject->public_key,
        'recipient_address' => Network::knownContract('consensus'),
    ]);

    Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady')
        ->set('filter', [
            'outgoing'            => true,
            'incoming'            => false,
            'transfers'           => false,
            'multipayments'       => false,
            'votes'               => false,
            'validator'           => false,
            'username'            => false,
            'contract_deployment' => false,
            'others'              => true,
        ])
        ->assertDontSee($other->id)
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
            'outgoing'            => false,
            'incoming'            => false,
            'transfers'           => false,
            'multipayments'       => false,
            'votes'               => false,
            'validator'           => false,
            'username'            => false,
            'contract_deployment' => false,
            'others'              => false,
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
            'outgoing'            => false,
            'incoming'            => false,
            'transfers'           => true,
            'multipayments'       => true,
            'votes'               => true,
            'validator'           => true,
            'username'            => true,
            'contract_deployment' => true,
            'others'              => true,
        ])
        ->assertDontSee($transfer->id)
        ->assertDontSee($validatorRegistration->id)
        ->assertSee(trans('tables.transactions.no_results.no_addressing_filters'));
});

it('should show no transactions if no type filter', function () {
    $transfer = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $validatorRegistration = Transaction::factory()
        ->validatorRegistration()
        ->create([
            'sender_public_key' => $this->subject->public_key,
        ]);

    Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady')
        ->set('filter', [
            'outgoing'            => true,
            'incoming'            => true,
            'transfers'           => false,
            'multipayments'       => false,
            'votes'               => false,
            'validator'           => false,
            'username'            => false,
            'contract_deployment' => false,
            'others'              => false,
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
    $vote = Transaction::factory()->vote($this->subject->address)->create([
        'sender_public_key' => $this->subject->public_key,
        'timestamp'         => Carbon::parse('2024-11-03 11:33:44')->getTimestampMs(), // oldest transaction
    ]);

    Transaction::factory(30)->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
        'timestamp'         => Carbon::parse('2024-11-04 11:33:44')->getTimestampMs(),
    ]);

    Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->call('setIsReady')
        ->assertDontSee($vote->id)
        ->call('setPage', 2)
        ->assertSee($vote->id)
        ->assertSee('Showing 31 results')
        ->set('filter.transfers', false)
        ->assertSet('filter', [
            'outgoing'            => true,
            'incoming'            => true,
            'transfers'           => false,
            'multipayments'       => true,
            'votes'               => true,
            'validator'           => true,
            'username'            => true,
            'contract_deployment' => true,
            'others'              => true,
        ])
        ->assertSet('paginators.page', 1)
        ->assertSee('Showing 1 result')
        ->assertSee($vote->id);
});

it('should show no data if not ready', function () {
    $transaction = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    Livewire::test(WalletTransactionTable::class, [new WalletViewModel($this->subject)])
        ->assertDontSee($transaction->id)
        ->call('setIsReady')
        ->assertSee($transaction->id);
});
