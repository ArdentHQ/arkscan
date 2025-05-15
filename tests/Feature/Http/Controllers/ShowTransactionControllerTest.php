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
    'secondSignature',
    'delegateRegistration',
    'multiSignature',
    'ipfs',
    'delegateResignation',
    'blsRegistration',
    'timelock',
    'timelockClaim',
    'timelockRefund',
    'entityRegistration',
    'entityResignation',
    'entityUpdate',
    'businessEntityRegistration',
    'businessEntityResignation',
    'businessEntityUpdate',
    'productEntityRegistration',
    'productEntityResignation',
    'productEntityUpdate',
    'pluginEntityRegistration',
    'pluginEntityResignation',
    'pluginEntityUpdate',
    'moduleEntityRegistration',
    'moduleEntityResignation',
    'moduleEntityUpdate',
    'delegateEntityRegistration',
    'delegateEntityResignation',
    'delegateEntityUpdate',
    'legacyBusinessRegistration',
    'legacyBusinessResignation',
    'legacyBusinessUpdate',
    'legacyBridgechainRegistration',
    'legacyBridgechainResignation',
    'legacyBridgechainUpdate',
]);

it('should render the page for a vote/unvote transaction without any errors', function ($type) {
    $this->withoutExceptionHandling();

    $delegate    = Wallet::factory()->activeDelegate()->create();
    $transaction = Transaction::factory()->{$type}()->create([
        'asset' => [
            'votes' => ['+'.$delegate->public_key],
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

it('should render the page for a vote combination transaction without any errors', function () {
    $this->withoutExceptionHandling();

    $oldDelegate = Wallet::factory()->activeDelegate()->create();
    $newDelegate = Wallet::factory()->activeDelegate()->create();
    $transaction = Transaction::factory()->voteCombination()->create([
        'asset' => [
            'votes' => ['-'.$oldDelegate->public_key, '+'.$newDelegate->public_key],
        ],
    ]);

    $this
        ->get(route('transaction', $transaction))
        ->assertOk()
        ->assertSee($transaction->id);
});
