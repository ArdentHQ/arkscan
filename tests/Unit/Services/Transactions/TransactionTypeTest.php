<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Transactions\TransactionType;

it('should determine the type', function (string $type, string $expected) {
    $transaction     = Transaction::factory()->{$type}()->create();
    $transactionType = new TransactionType($transaction);

    expect($transactionType->{'is'.ucfirst($type)}())->toBeTrue();
    expect($transactionType->name())->toBe($expected);
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
        'delegateRegistration',
        'delegate-registration',
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
        'delegateResignation',
        'delegate-resignation',
    ],
    [
        'multiPayment',
        'multi-payment',
    ],
    [
        'timelock',
        'timelock',
    ],
    [
        'timelockClaim',
        'timelock-claim',
    ],
    [
        'timelockRefund',
        'timelock-refund',
    ],
    [
        'businessEntityRegistration',
        'business-entity-registration',
    ],
    [
        'businessEntityResignation',
        'business-entity-resignation',
    ],
    [
        'businessEntityUpdate',
        'business-entity-update',
    ],
    [
        'productEntityRegistration',
        'product-entity-registration',
    ],
    [
        'productEntityResignation',
        'product-entity-resignation',
    ],
    [
        'productEntityUpdate',
        'product-entity-update',
    ],
    [
        'pluginEntityRegistration',
        'plugin-entity-registration',
    ],
    [
        'pluginEntityResignation',
        'plugin-entity-resignation',
    ],
    [
        'pluginEntityUpdate',
        'plugin-entity-update',
    ],
    [
        'moduleEntityRegistration',
        'module-entity-registration',
    ],
    [
        'moduleEntityResignation',
        'module-entity-resignation',
    ],
    [
        'moduleEntityUpdate',
        'module-entity-update',
    ],
    [
        'delegateEntityRegistration',
        'delegate-entity-registration',
    ],
    [
        'delegateEntityResignation',
        'delegate-entity-resignation',
    ],
    [
        'delegateEntityUpdate',
        'delegate-entity-update',
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
        'bridgechain-entity-registration',
    ],
    [
        'legacyBridgechainResignation',
        'bridgechain-entity-resignation',
    ],
    [
        'legacyBridgechainUpdate',
        'bridgechain-entity-update',
    ],
]);

it('should determine the migration type', function () {
    config(['explorer.migration.address' => 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj']);

    Wallet::factory()->create([
        'address' => 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj',
    ]);

    $transaction = Transaction::factory()->transfer()->create([
        'recipient_id' => 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj',
        'fee'          => '5000000', // 0.5
        'amount'       => '100000000', // 1
        'vendor_field' => '0xRKeoIZ9Kh2g4HslgeHr5B9yblHbnwWYgfeFgO36n',
    ]);

    $transaction = Transaction::find($transaction->id);

    $transactionType = new TransactionType($transaction);

    expect($transactionType->isMigration())->toBeTrue();
});

it('should not determine the migration type', function ($transaction) {
    config(['explorer.migration.address' => 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj']);

    Wallet::factory()->create([
        'address' => 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj',
    ]);

    $transaction = Transaction::factory()->transfer()->create([
        'recipient_id' => 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj',
        'fee'          => '5000000', // 0.5
        'amount'       => '100000000', // 1
        'vendor_field' => '0xRKeoIZ9Kh2g4HslgeHr5B9yblHbnwWYgfeFgO36n',
        ...$transaction,
    ]);

    $transaction = Transaction::find($transaction->id);

    $transactionType = new TransactionType($transaction);

    expect($transactionType->isMigration())->toBeFalse();
})->with([
    'different recipient' => [[
        'recipient_id' => 'DFAKEFAKEFAKEFAKEFAKEFAKEFAKEFAKEE',
    ]],
    'low amount'          => [[
        'amount' => '10000000', // 0.1
    ]],
    'low fee'             => [[
        'fee' => '500000', // 0.05
    ]],
    'short vendor field'  => [[
        'vendor_field' => '0x123',
    ]],
    'empty vendor field'  => [[
        'vendor_field' => '',
    ]],
    'null vendor field'   => [[
        'vendor_field' => null,
    ]],
]);

it('should determine is unknown type', function () {
    $transaction = Transaction::factory()->create([
        'type'       => 0,
        'type_group' => 0,
        'asset'      => [],
    ]);
    $transactionType = new TransactionType($transaction);

    expect($transactionType->isUnknown())->toBeTrue();
    expect($transactionType->name())->toBe('unknown');
});

it('should play through every scenario of an unknown type', function (string $type) {
    $transaction = Transaction::factory()->{$type}()->create();

    expect((new TransactionType($transaction))->isUnknown())->toBeFalse();
})->with([
    ['transfer'],
    ['secondSignature'],
    ['delegateRegistration'],
    ['vote'],
    ['unvote'],
    ['voteCombination'],
    ['multiSignature'],
    ['ipfs'],
    ['delegateResignation'],
    ['multiPayment'],
    ['timelock'],
    ['timelockClaim'],
    ['timelockRefund'],
    ['businessEntityRegistration'],
    ['businessEntityResignation'],
    ['businessEntityUpdate'],
    ['productEntityRegistration'],
    ['productEntityResignation'],
    ['productEntityUpdate'],
    ['pluginEntityRegistration'],
    ['pluginEntityResignation'],
    ['pluginEntityUpdate'],
    ['moduleEntityRegistration'],
    ['moduleEntityResignation'],
    ['moduleEntityUpdate'],
    ['delegateEntityRegistration'],
    ['delegateEntityResignation'],
    ['delegateEntityUpdate'],
    ['legacyBusinessRegistration'],
    ['legacyBusinessResignation'],
    ['legacyBusinessUpdate'],
    ['legacyBridgechainRegistration'],
    ['legacyBridgechainResignation'],
    ['legacyBridgechainUpdate'],
]);
