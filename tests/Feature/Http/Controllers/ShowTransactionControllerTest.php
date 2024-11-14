<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Models\Wallet;

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
