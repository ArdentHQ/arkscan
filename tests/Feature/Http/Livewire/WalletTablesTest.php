<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Http\Livewire\WalletTables;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\NumberFormatter;
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

    $component = Livewire::test(WalletTables::class, [new WalletViewModel($this->subject)]);
    $component->set('state.view', 'transactions');

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

    $component = Livewire::test(WalletTables::class, [new WalletViewModel($this->subject)]);
    $component->set('state.view', 'transactions');

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

it('should show sent multipayment', function () {
    $recipient1 = Wallet::factory()->create();
    $recipient2 = Wallet::factory()->create();
    $recipient3 = Wallet::factory()->create();

    $sent = Transaction::factory()->multiPayment()->create([
        'sender_public_key' => $this->subject->public_key,
        'recipient_id'      => $this->subject->address,

        'asset' => [
            'payments' => [
                [
                    'recipientId' => $recipient1->address,
                    'amount'      => 1 * 1e8,
                ],
                [
                    'recipientId' => $recipient2->address,
                    'amount'      => 1 * 1e8,
                ],
                [
                    'recipientId' => $recipient3->address,
                    'amount'      => 1 * 1e8,
                ],
            ],
        ],
    ]);

    $component = Livewire::test(WalletTables::class, [new WalletViewModel($this->subject)]);
    $component->set('state.view', 'transactions');

    $transaction = ViewModelFactory::make($sent);

    $component->assertSee($transaction->id());
    $component->assertSee($transaction->timestamp());
    $component->assertDontSee(sprintf(
        '%s…%s',
        substr($this->subject->address, 0, 5),
        substr($this->subject->address, -5)
    ));
    $component->assertSeeInOrder(['Multiple', '(3)']);
    $component->assertSeeInOrder([
        '-',
        $transaction->amount(),
        $transaction->fee(),
    ]);
});

it('should show received multipayment', function () {
    $sender     = Wallet::factory()->create();
    $recipient2 = Wallet::factory()->create();
    $recipient3 = Wallet::factory()->create();

    $received = Transaction::factory()->multiPayment()->create([
        'sender_public_key' => $sender->public_key,
        'recipient_id'      => $sender->address,

        'asset' => [
            'payments' => [
                [
                    'recipientId' => $this->subject->address,
                    'amount'      => 1 * 1e8,
                ],
                [
                    'recipientId' => $recipient2->address,
                    'amount'      => 1 * 1e8,
                ],
                [
                    'recipientId' => $recipient3->address,
                    'amount'      => 1 * 1e8,
                ],
            ],
        ],
    ]);

    $component = Livewire::test(WalletTables::class, [new WalletViewModel($this->subject)]);
    $component->set('state.view', 'transactions');

    $transaction = ViewModelFactory::make($received);

    $component->assertSee($transaction->id());
    $component->assertSee($transaction->timestamp());
    $component->assertSee(sprintf(
        '%s…%s',
        substr($sender->address, 0, 5),
        substr($sender->address, -5)
    ));
    $component->assertDontSee('Multiple');
    $component->assertSeeInOrder([
        '-',
        $transaction->amount(),
        $transaction->fee(),
    ]);
});

it('should show multipayment without amount sent to self', function () {
    $recipient1 = Wallet::factory()->create();
    $recipient2 = Wallet::factory()->create();

    $sent = Transaction::factory()->multiPayment()->create([
        'sender_public_key' => $this->subject->public_key,
        'recipient_id'      => $this->subject->address,

        'asset' => [
            'payments' => [
                [
                    'recipientId' => $recipient1->address,
                    'amount'      => 1 * 1e8,
                ],
                [
                    'recipientId' => $recipient2->address,
                    'amount'      => 1 * 1e8,
                ],
                [
                    'recipientId' => $this->subject->address,
                    'amount'      => 1 * 1e8,
                ],
            ],
        ],
    ]);

    $component = Livewire::test(WalletTables::class, [new WalletViewModel($this->subject)]);
    $component->set('state.view', 'transactions');

    $transaction = ViewModelFactory::make($sent);

    $component->assertSee($transaction->id());
    $component->assertSee($transaction->timestamp());
    $component->assertDontSee(sprintf(
        '%s…%s',
        substr($this->subject->address, 0, 5),
        substr($this->subject->address, -5)
    ));
    $component->assertSeeInOrder(['Multiple', '(3)']);
    $component->assertSeeInOrder([
        '-',
        $transaction->amountExcludingItself(),
        $transaction->fee(),
    ]);
});

it('should show transfer without amount sent to self', function () {
    $sent = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
        'recipient_id'      => $this->subject->address,
    ]);

    $component = Livewire::test(WalletTables::class, [new WalletViewModel($this->subject)]);
    $component->set('state.view', 'transactions');

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

it('should toggle all filters when "select all" is selected', function () {
    Livewire::test(WalletTables::class, [new WalletViewModel($this->subject)])
        ->assertSet('filter', [
            'outgoing'      => true,
            'incoming'      => true,
            'transfers'     => true,
            'votes'         => true,
            'multipayments' => true,
            'others'        => true,
        ])
        ->assertSet('selectAllFilters', true)
        ->set('filter.outgoing', true)
        ->assertSet('selectAllFilters', true)
        ->set('selectAllFilters', false)
        ->assertSet('filter', [
            'outgoing'      => false,
            'incoming'      => false,
            'transfers'     => false,
            'votes'         => false,
            'multipayments' => false,
            'others'        => false,
        ])
        ->set('selectAllFilters', true)
        ->assertSet('filter', [
            'outgoing'      => true,
            'incoming'      => true,
            'transfers'     => true,
            'votes'         => true,
            'multipayments' => true,
            'others'        => true,
        ]);
});

it('should toggle "select all" when all filters are selected', function () {
    Livewire::test(WalletTables::class, [new WalletViewModel($this->subject)])
        ->assertSet('filter', [
            'outgoing'      => true,
            'incoming'      => true,
            'transfers'     => true,
            'votes'         => true,
            'multipayments' => true,
            'others'        => true,
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

    Livewire::test(WalletTables::class, [new WalletViewModel($this->subject)])
        ->set('state.view', 'transactions')
        ->set('filter', [
            'outgoing'      => true,
            'incoming'      => false,
            'transfers'     => true,
            'votes'         => false,
            'multipayments' => false,
            'others'        => false,
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

    Livewire::test(WalletTables::class, [new WalletViewModel($this->subject)])
        ->set('state.view', 'transactions')
        ->set('filter', [
            'outgoing'      => false,
            'incoming'      => true,
            'transfers'     => true,
            'votes'         => false,
            'multipayments' => false,
            'others'        => false,
        ])
        ->assertSee($received->id)
        ->assertDontSee($sent->id);
});

it('should show multipayments when filtered by incoming transactions', function () {
    $sent = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $multiPaymentSender = Wallet::factory()->create();
    $recipient2         = Wallet::factory()->create();
    $recipient3         = Wallet::factory()->create();
    $received           = Transaction::factory()->multiPayment()->create([
        'sender_public_key' => $multiPaymentSender->public_key,
        'recipient_id'      => $multiPaymentSender->address,

        'asset' => [
            'payments' => [
                [
                    'recipientId' => $this->subject->address,
                    'amount'      => 1 * 1e8,
                ],
                [
                    'recipientId' => $recipient2->address,
                    'amount'      => 1 * 1e8,
                ],
                [
                    'recipientId' => $recipient3->address,
                    'amount'      => 1 * 1e8,
                ],
            ],
        ],
    ]);

    Livewire::test(WalletTables::class, [new WalletViewModel($this->subject)])
        ->set('state.view', 'transactions')
        ->set('filter', [
            'outgoing'      => false,
            'incoming'      => true,
            'transfers'     => true,
            'votes'         => false,
            'multipayments' => true,
            'others'        => false,
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

    Livewire::test(WalletTables::class, [new WalletViewModel($this->subject)])
        ->set('state.view', 'transactions')
        ->set('filter', [
            'outgoing'      => true,
            'incoming'      => true,
            'transfers'     => true,
            'votes'         => false,
            'multipayments' => false,
            'others'        => false,
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
    ]);

    Livewire::test(WalletTables::class, [new WalletViewModel($this->subject)])
        ->set('state.view', 'transactions')
        ->set('filter', [
            'outgoing'      => true,
            'incoming'      => false,
            'transfers'     => true,
            'votes'         => false,
            'multipayments' => false,
            'others'        => false,
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
    ]);

    Livewire::test(WalletTables::class, [new WalletViewModel($this->subject)])
        ->set('state.view', 'transactions')
        ->set('filter', [
            'outgoing'      => true,
            'incoming'      => false,
            'transfers'     => false,
            'votes'         => true,
            'multipayments' => false,
            'others'        => false,
        ])
        ->assertSee($vote->id)
        ->assertDontSee($transfer->id);
});

it('should filter by multipayment transactions', function () {
    $transfer = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $multipayment = Transaction::factory()->multiPayment()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    Livewire::test(WalletTables::class, [new WalletViewModel($this->subject)])
        ->set('state.view', 'transactions')
        ->set('filter', [
            'outgoing'      => true,
            'incoming'      => false,
            'transfers'     => false,
            'votes'         => false,
            'multipayments' => true,
            'others'        => false,
        ])
        ->assertSee($multipayment->id)
        ->assertDontSee($transfer->id);
});

it('should filter by other transactions', function () {
    $transfer = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $delegateRegistration = Transaction::factory()->delegateRegistration()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $entityRegistration = Transaction::factory()->entityRegistration()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    Livewire::test(WalletTables::class, [new WalletViewModel($this->subject)])
        ->set('state.view', 'transactions')
        ->set('filter', [
            'outgoing'      => true,
            'incoming'      => false,
            'transfers'     => false,
            'votes'         => false,
            'multipayments' => false,
            'others'        => true,
        ])
        ->assertSee($delegateRegistration->id)
        ->assertSee($entityRegistration->id)
        ->assertDontSee($transfer->id);
});

it('should show no transactions if no filters', function () {
    $transfer = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $delegateRegistration = Transaction::factory()->delegateRegistration()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $entityRegistration = Transaction::factory()->entityRegistration()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    Livewire::test(WalletTables::class, [new WalletViewModel($this->subject)])
        ->set('state.view', 'transactions')
        ->set('filter', [
            'outgoing'      => false,
            'incoming'      => false,
            'transfers'     => false,
            'votes'         => false,
            'multipayments' => false,
            'others'        => false,
        ])
        ->assertDontSee($transfer->id)
        ->assertDontSee($delegateRegistration->id)
        ->assertDontSee($entityRegistration->id)
        ->assertSee(trans('tables.transactions.no_results.no_addressing_filters'));
});

it('should show no transactions if no addressing filter', function () {
    $transfer = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $delegateRegistration = Transaction::factory()->delegateRegistration()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $entityRegistration = Transaction::factory()->entityRegistration()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    Livewire::test(WalletTables::class, [new WalletViewModel($this->subject)])
        ->set('state.view', 'transactions')
        ->set('filter', [
            'outgoing'      => false,
            'incoming'      => false,
            'transfers'     => true,
            'votes'         => true,
            'multipayments' => true,
            'others'        => true,
        ])
        ->assertDontSee($transfer->id)
        ->assertDontSee($delegateRegistration->id)
        ->assertDontSee($entityRegistration->id)
        ->assertSee(trans('tables.transactions.no_results.no_addressing_filters'));
});

it('should show no transactions if no type filter', function () {
    $transfer = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $delegateRegistration = Transaction::factory()->delegateRegistration()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $entityRegistration = Transaction::factory()->entityRegistration()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    Livewire::test(WalletTables::class, [new WalletViewModel($this->subject)])
        ->set('state.view', 'transactions')
        ->set('filter', [
            'outgoing'      => true,
            'incoming'      => true,
            'transfers'     => false,
            'votes'         => false,
            'multipayments' => false,
            'others'        => false,
        ])
        ->assertDontSee($transfer->id)
        ->assertDontSee($delegateRegistration->id)
        ->assertDontSee($entityRegistration->id)
        ->assertSee(trans('tables.transactions.no_results.no_type_filters'));
});

it('should show no results message if no transactions matching filter', function () {
    Livewire::test(WalletTables::class, [new WalletViewModel($this->subject)])
        ->set('state.view', 'transactions')
        ->assertSee(trans('tables.transactions.no_results.no_results'));
});

it('should reset pagination when filtering', function () {
    $vote = Transaction::factory()->vote()->create([
        'sender_public_key' => $this->subject->public_key,
        'timestamp'         => 102982050, // oldest transaction
    ]);

    Transaction::factory(15)->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    Livewire::test(WalletTables::class, [new WalletViewModel($this->subject)])
        ->set('state.view', 'transactions')
        ->assertDontSee($vote->id)
        ->call('setPage', 2)
        ->assertSee($vote->id)
        ->set('filter.transfers', false)
        ->assertSet('page', 1)
        ->assertSee($vote->id);
});
