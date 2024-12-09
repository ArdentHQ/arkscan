<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Models\Wallet;
use Carbon\Carbon;

use function Tests\faker;

it('should render the page without any errors', function ($type) {
    $this->withoutExceptionHandling();

    $transaction = Transaction::factory()->{$type}()->create();

    $this
        ->get(route('transaction', $transaction))
        ->assertOk()
        ->assertSee($transaction->id);
})->with([
    'transfer',
    'validatorRegistration',
    'validatorResignation',
]);

it('should render the page for a vote/unvote transaction without any errors', function ($type) {
    $this->withoutExceptionHandling();

    $validator    = Wallet::factory()->activeValidator()->create();
    $transaction  = Transaction::factory()->{$type}()->create([
        'asset' => [
            'votes' => [$validator->address],
        ],
    ]);

    $this
        ->get(route('transaction', $transaction))
        ->assertOk()
        ->assertSee($transaction->id);
})->with([
    'vote',
    'unvote',
]);

it('should handle failed token transfers with missing data', function () {
    $this->travelTo('2021-04-14 16:02:04');

    $address = Wallet::factory()->create()->address;
    $amount  = (int) (34 * 1e9);

    $transaction = Transaction::factory()->tokenTransfer($address, $amount)->create([
        'timestamp' => Carbon::parse('2021-04-14 13:02:04')->getTimestampMs(),
        'amount'    => 123 * 1e18,
    ]);

    $this
        ->get(route('transaction', $transaction))
        ->assertOk()
        ->assertSeeInOrder([
            trans('pages.transaction.tokens_transferred'),
            $address,
            '34 DARK',
        ]);
});
