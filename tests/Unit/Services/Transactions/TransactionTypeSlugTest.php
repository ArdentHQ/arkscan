<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Services\Transactions\TransactionTypeSlug;

it('should determine the type', function (string $type, string $expectedExact, ?string $expectedGeneric = null) {
    $transaction     = Transaction::factory()->{$type}()->create();
    $transactionType = new TransactionTypeSlug($transaction);

    expect($transactionType->exact())->toBe($expectedExact);

    if ($expectedGeneric) {
        expect($transactionType->generic())->toBe($expectedGeneric);
    }
})->with([
    [
        'transfer',
        'transfer',
        'transfer',
    ],
    [
        'secondSignature',
        'second-signature',
        'second-signature',
    ],
    [
        'delegateRegistration',
        'delegate-registration',
        'delegate-registration',
    ],
    [
        'vote',
        'vote',
        'vote',
    ],
    [
        'unvote',
        'unvote',
        'unvote',
    ],
    [
        'voteCombination',
        'vote-combination',
        'vote-combination',
    ],
    [
        'multiSignature',
        'multi-signature',
        'multi-signature',
    ],
    [
        'ipfs',
        'ipfs',
        'ipfs',
    ],
    [
        'delegateResignation',
        'delegate-resignation',
        'delegate-resignation',
    ],
    [
        'multiPayment',
        'multi-payment',
        'multi-payment',
    ],
    [
        'timelock',
        'timelock',
        'timelock',
    ],
    [
        'timelockClaim',
        'timelock-claim',
        'timelock-claim',
    ],
    [
        'timelockRefund',
        'timelock-refund',
        'timelock-refund',
    ],
    [
        'businessEntityRegistration',
        'business-entity-registration',
        'entity-registration',
    ],
    [
        'businessEntityResignation',
        'business-entity-resignation',
        'entity-resignation',
    ],
    [
        'businessEntityUpdate',
        'business-entity-update',
        'entity-update',
    ],
    [
        'productEntityRegistration',
        'product-entity-registration',
        'entity-registration',
    ],
    [
        'productEntityResignation',
        'product-entity-resignation',
        'entity-resignation',
    ],
    [
        'productEntityUpdate',
        'product-entity-update',
        'entity-update',
    ],
    [
        'pluginEntityRegistration',
        'plugin-entity-registration',
        'entity-registration',
    ],
    [
        'pluginEntityResignation',
        'plugin-entity-resignation',
        'entity-resignation',
    ],
    [
        'pluginEntityUpdate',
        'plugin-entity-update',
        'entity-update',
    ],
    [
        'moduleEntityRegistration',
        'module-entity-registration',
        'entity-registration',
    ],
    [
        'moduleEntityResignation',
        'module-entity-resignation',
        'entity-resignation',
    ],
    [
        'moduleEntityUpdate',
        'module-entity-update',
        'entity-update',
    ],
    [
        'delegateEntityRegistration',
        'delegate-entity-registration',
        'entity-registration',
    ],
    [
        'delegateEntityResignation',
        'delegate-entity-resignation',
        'entity-resignation',
    ],
    [
        'delegateEntityUpdate',
        'delegate-entity-update',
        'entity-update',
    ],
    [
        'legacyBusinessRegistration',
        'legacy-business-registration',
    ],
    [
        'legacyBusinessResignation',
        'legacy-business-resignation',
    ],
    [
        'legacyBusinessUpdate',
        'legacy-business-update',
    ],
    [
        'legacyBridgechainRegistration',
        'legacy-bridgechain-registration',
    ],
    [
        'legacyBridgechainResignation',
        'legacy-bridgechain-resignation',
    ],
    [
        'legacyBridgechainUpdate',
        'legacy-bridgechain-update',
    ],
]);

it('should determine is unknown type', function () {
    $transaction = Transaction::factory()->create([
        'type'       => 0,
        'type_group' => 0,
        'asset'      => [],
    ]);
    $transactionType = new TransactionTypeSlug($transaction);

    expect($transactionType->generic())->toBe('unknown');
    expect($transactionType->exact())->toBe('unknown');
});
