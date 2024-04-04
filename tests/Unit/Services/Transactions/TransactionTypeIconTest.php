<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Services\Transactions\TransactionTypeIcon;

it('should determine the icon that matches the type', function (string $type, string $icon) {
    $transaction = Transaction::factory()->{$type}()->create();

    expect((new TransactionTypeIcon($transaction))->name())->toBe($icon);
})->with([
    [
        'transfer',
        'transfer',
    ],
    [
        'secondSignature',
        'second-signature',
    ],
    [
        'validatorRegistration',
        'validator-registration',
    ],
    [
        'vote',
        'vote',
    ],
    [
        'unvote',
        'unvote',
    ],
    [
        'voteCombination',
        'vote-combination',
    ],
    [
        'multiSignature',
        'multi-signature',
    ],
    [
        'ipfs',
        'ipfs',
    ],
    [
        'validatorResignation',
        'validator-resignation',
    ],
    [
        'multiPayment',
        'multi-payment',
    ],
    [
        'businessEntityRegistration',
        'business',
    ],
    [
        'businessEntityResignation',
        'business',
    ],
    [
        'businessEntityUpdate',
        'business',
    ],
    [
        'productEntityRegistration',
        'product',
    ],
    [
        'productEntityResignation',
        'product',
    ],
    [
        'productEntityUpdate',
        'product',
    ],
    [
        'pluginEntityRegistration',
        'plugin',
    ],
    [
        'pluginEntityResignation',
        'plugin',
    ],
    [
        'pluginEntityUpdate',
        'plugin',
    ],
    [
        'moduleEntityRegistration',
        'module',
    ],
    [
        'moduleEntityResignation',
        'module',
    ],
    [
        'moduleEntityUpdate',
        'module',
    ],
    [
        'validatorEntityRegistration',
        'validator-registration',
    ],
    [
        'validatorEntityResignation',
        'validator-registration',
    ],
    [
        'validatorEntityUpdate',
        'validator-registration',
    ],
    [
        'legacyBusinessRegistration',
        'business',
    ],
    [
        'legacyBusinessResignation',
        'business',
    ],
    [
        'legacyBusinessUpdate',
        'business',
    ],
    [
        'legacyBridgechainRegistration',
        'bridgechain',
    ],
    [
        'legacyBridgechainResignation',
        'bridgechain',
    ],
    [
        'legacyBridgechainUpdate',
        'bridgechain',
    ],
]);

it('should determine the icon of unknown type', function () {
    $transaction = Transaction::factory()->create([
        'type'       => 0,
        'type_group' => 0,
    ]);

    expect((new TransactionTypeIcon($transaction))->name())->toBe('unknown');
});
