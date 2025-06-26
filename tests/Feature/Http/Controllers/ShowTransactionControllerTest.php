<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Models\Wallet;
use Carbon\Carbon;

it('should render the page without any errors', function ($type, $args) {
    $this->withoutExceptionHandling();

    $transaction = Transaction::factory()->{$type}(...$args)->create();

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

    $validator    = Wallet::factory()->activeValidator()->create();

    $transaction  = Transaction::factory()->vote($validator->address)->create();

    $this
        ->get(route('transaction', $transaction->hash))
        ->assertOk()
        ->assertSee($transaction->hash);
});

it('should render the page for a unvote transaction without any errors', function () {
    $this->withoutExceptionHandling();

    $transaction  = Transaction::factory()->unvote()->create();

    $this
        ->get(route('transaction', $transaction->hash))
        ->assertOk()
        ->assertSee($transaction->hash);
});

it('should handle failed token transfers with missing data', function () {
    $this->travelTo('2021-04-14 16:02:04');

    $address = Wallet::factory()->create()->address;
    $amount  = (int) (34 * 1e9);

    $transaction = Transaction::factory()->tokenTransfer($address, $amount)->create([
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
        ->validatorRegistration('0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B')
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
        ->validatorRegistration('0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B')
        ->create();

    $resignationTransaction = Transaction::factory()
        ->validatorResignation()
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
