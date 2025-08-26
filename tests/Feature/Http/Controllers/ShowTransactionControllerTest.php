<?php

declare(strict_types=1);

use App\Models\MultiPayment;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;

it('should render the page without any errors', function ($type, $args) {
    $this->withoutExceptionHandling();

    $transaction = Transaction::factory()
        ->{$type}(...$args)
        ->withReceipt()
        ->create();

    $this
        ->get(route('transaction', $transaction->hash))
        ->assertOk()
        ->assertSee($transaction->hash);
})->with([
    'transfer'              => ['transfer', []],
    'validatorRegistration' => ['validatorRegistration', ['30492624ED2db94EEfCD8E91d7218488658e972d']],
    'validatorResignation'  => ['validatorResignation', []],
]);

it('should render the page for a vote transaction without any errors', function () {
    $this->withoutExceptionHandling();

    $validator = Wallet::factory()->activeValidator()->create();

    $transaction = Transaction::factory()
        ->vote($validator->address)
        ->withReceipt()
        ->create();

    $this
        ->get(route('transaction', $transaction->hash))
        ->assertOk()
        ->assertSee($transaction->hash);
});

it('should render the page for a unvote transaction without any errors', function () {
    $this->withoutExceptionHandling();

    $transaction  = Transaction::factory()
        ->unvote()
        ->withReceipt()
        ->create();

    $this
        ->get(route('transaction', $transaction->hash))
        ->assertOk()
        ->assertSee($transaction->hash);
});

it('should handle failed token transfers with missing data', function () {
    $this->travelTo('2021-04-14 16:02:04');

    $address = Wallet::factory()->create()->address;
    $amount  = (int) (34 * 1e9);

    $transaction = Transaction::factory()
        ->tokenTransfer($address, $amount)
        ->withReceipt()
        ->create([
            'timestamp' => Carbon::parse('2021-04-14 13:02:04')->getTimestampMs(),
            'value'     => 123 * 1e18,
        ]);

    $this
        ->get(route('transaction', $transaction->hash))
        ->assertOk()
        ->assertSeeInOrder([
            trans('pages.transaction.tokens_transferred'),
            $address,
            '34 DARK',
        ]);
});

it('should show a locked amount for a validator registration', function () {
    $transaction = Transaction::factory()
        ->validatorRegistration('C5a19e23E99bdFb7aae4301A009763AdC01c1b5B')
        ->withReceipt()
        ->create();

    $this
        ->get(route('transaction', $transaction->hash))
        ->assertOk()
        ->assertSeeInOrder([
            trans('pages.transaction.header.locked_amount'),
            '250.00 DARK',
        ]);
});

it('should show a corresponding validator registration', function () {
    $registrationTransaction = Transaction::factory()
        ->validatorRegistration('C5a19e23E99bdFb7aae4301A009763AdC01c1b5B')
        ->withReceipt()
        ->create();

    $resignationTransaction = Transaction::factory()
        ->validatorResignation()
        ->withReceipt()
        ->create([
            'sender_public_key' => $registrationTransaction->sender_public_key,
        ]);

    $this
        ->get(route('transaction', $resignationTransaction->hash))
        ->assertOk()
        ->assertSeeInOrder([
            trans('pages.transaction.header.unlocked_amount'),
            '250.00 DARK',
            trans('pages.transaction.unlocked_amount_tooltip'),
        ]);
});

it('should show 0 if no corresponding validator registration', function () {
    $transaction = Transaction::factory()
        ->validatorResignation()
        ->withReceipt()
        ->create();

    $this
        ->get(route('transaction', $transaction->hash))
        ->assertOk()
        ->assertSeeHtmlInOrder([
            trans('pages.transaction.header.unlocked_amount'),
            ' 0 DARK',
            trans('pages.transaction.legacy_registration_tooltip'),
        ]);
});

it('should render the page for basic transaction types without any errors', function ($transactionType) {
    $transaction = Transaction::factory()
        ->{$transactionType}()
        ->withReceipt()
        ->create();

    $this
        ->get(route('transaction', $transaction->hash))
        ->assertOk()
        ->assertSee($transaction->hash);
})->with([
    'transfer',
    'unvote',
    'validatorRegistration',
    'validatorUpdate',
    'validatorResignation',
    'usernameRegistration',
    'usernameResignation',
    'contractDeployment',
]);

it('should render the page for votes without any errors', function () {
    $validator = Wallet::factory()->create();

    $transaction = Transaction::factory()
        ->vote($validator->address)
        ->withReceipt()
        ->create();

    $this
        ->get(route('transaction', $transaction->hash))
        ->assertOk()
        ->assertSee($transaction->hash)
        ->assertSee($validator->address);
});

it('should render the page for multipayments without any errors', function () {
    $recipient1 = Wallet::factory()->create();
    $recipient2 = Wallet::factory()->create();

    $transaction = Transaction::factory()
        ->multiPayment([
            $recipient1->address,
            $recipient2->address,
        ], [
            BigNumber::new(123 * 1e18),
            BigNumber::new(456 * 1e18),
        ])
        ->withReceipt()
        ->create();

    MultiPayment::factory()
        ->count(2)
        ->state(new Sequence(
            [
                'to'     => $recipient1->address,
                'amount' => BigNumber::new(123 * 1e18),
            ],
            [
                'to'     => $recipient2->address,
                'amount' => BigNumber::new(456 * 1e18),
            ],
        ))
        ->create([
            'from' => $transaction->from,
            'hash' => $transaction->hash,
        ]);

    $this
        ->get(route('transaction', $transaction->hash))
        ->assertOk()
        ->assertSee($transaction->hash)
        ->assertSeeInOrder([
            '123.00 DARK',
            '456.00 DARK',
        ]);
});
