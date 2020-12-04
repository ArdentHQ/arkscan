<?php

declare(strict_types=1);

use App\Enums\CoreTransactionTypeEnum;
use App\Enums\MagistrateTransactionEntityActionEnum;
use App\Enums\MagistrateTransactionEntitySubTypeEnum;
use App\Enums\MagistrateTransactionEntityTypeEnum;
use App\Enums\MagistrateTransactionTypeEnum;
use App\Enums\TransactionTypeGroupEnum;
use App\Models\Transaction;
use App\Services\Transactions\TransactionType;
use function Tests\configureExplorerDatabase;

beforeEach(fn () => configureExplorerDatabase());

it('should determine the the type', function (string $method, int $type, int $typeGroup, array $asset, string $name) {
    $transaction = Transaction::factory()->create([
        'type'       => $type,
        'type_group' => $typeGroup,
        'asset'      => $asset,
    ]);

    expect((new TransactionType($transaction))->$method())->toBeTrue();
    expect((new TransactionType($transaction))->name())->toBe($name);
})->with([
    [
        'isTransfer',
        CoreTransactionTypeEnum::TRANSFER,
        TransactionTypeGroupEnum::CORE,
        [],
        'transfer',
    ], [
        'isSecondSignature',
        CoreTransactionTypeEnum::SECOND_SIGNATURE,
        TransactionTypeGroupEnum::CORE,
        [],
        'second-signature',
    ], [
        'isDelegateRegistration',
        CoreTransactionTypeEnum::DELEGATE_REGISTRATION,
        TransactionTypeGroupEnum::CORE,
        [],
        'delegate-registration',
    ], [
        'isVote',
        CoreTransactionTypeEnum::VOTE,
        TransactionTypeGroupEnum::CORE,
        [
            'votes' => ['+publicKey'],
        ],
        'vote',
    ], [
        'isUnvote',
        CoreTransactionTypeEnum::VOTE,
        TransactionTypeGroupEnum::CORE,
        [
            'votes' => ['-publicKey'],
        ],
        'unvote',
    ], [
        'isVoteCombination',
        CoreTransactionTypeEnum::VOTE,
        TransactionTypeGroupEnum::CORE,
        [
            'votes' => ['+publicKey', '-publicKey'],
        ],
        'vote-combination',
    ], [
        'isMultiSignature',
        CoreTransactionTypeEnum::MULTI_SIGNATURE,
        TransactionTypeGroupEnum::CORE,
        [],
        'multi-signature',
    ], [
        'isIpfs',
        CoreTransactionTypeEnum::IPFS,
        TransactionTypeGroupEnum::CORE,
        [],
        'ipfs',
    ], [
        'isDelegateResignation',
        CoreTransactionTypeEnum::DELEGATE_RESIGNATION,
        TransactionTypeGroupEnum::CORE,
        [],
        'delegate-resignation',
    ], [
        'isMultiPayment',
        CoreTransactionTypeEnum::MULTI_PAYMENT,
        TransactionTypeGroupEnum::CORE,
        [],
        'multi-payment',
    ], [
        'isTimelock',
        CoreTransactionTypeEnum::TIMELOCK,
        TransactionTypeGroupEnum::CORE,
        [],
        'timelock',
    ], [
        'isTimelockClaim',
        CoreTransactionTypeEnum::TIMELOCK_CLAIM,
        TransactionTypeGroupEnum::CORE,
        [],
        'timelock-claim',
    ], [
        'isTimelockRefund',
        CoreTransactionTypeEnum::TIMELOCK_REFUND,
        TransactionTypeGroupEnum::CORE,
        [],
        'timelock-refund',
    ], [
        'isBusinessEntityRegistration',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::BUSINESS,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::REGISTER,
        ],
        'business-registration',
    ], [
        'isBusinessEntityResignation',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::BUSINESS,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::RESIGN,
        ],
        'business-resignation',
    ], [
        'isBusinessEntityUpdate',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::BUSINESS,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::UPDATE,
        ],
        'business-update',
    ], [
        'isProductEntityRegistration',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::PRODUCT,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::REGISTER,
        ],
        'product-registration',
    ], [
        'isProductEntityResignation',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::PRODUCT,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::RESIGN,
        ],
        'product-resignation',
    ], [
        'isProductEntityUpdate',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::PRODUCT,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::UPDATE,
        ],
        'product-update',
    ], [
        'isPluginEntityRegistration',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::PLUGIN,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::REGISTER,
        ],
        'plugin-registration',
    ], [
        'isPluginEntityResignation',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::PLUGIN,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::RESIGN,
        ],
        'plugin-resignation',
    ], [
        'isPluginEntityUpdate',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::PLUGIN,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::UPDATE,
        ],
        'plugin-update',
    ], [
        'isModuleEntityRegistration',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::MODULE,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::REGISTER,
        ],
        'module-registration',
    ], [
        'isModuleEntityResignation',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::MODULE,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::RESIGN,
        ],
        'module-resignation',
    ], [
        'isModuleEntityUpdate',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::MODULE,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::UPDATE,
        ],
        'module-update',
    ], [
        'isDelegateEntityRegistration',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::DELEGATE,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::REGISTER,
        ],
        'delegate-entity-registration',
    ], [
        'isDelegateEntityResignation',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::DELEGATE,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::RESIGN,
        ],
        'delegate-entity-resignation',
    ], [
        'isDelegateEntityUpdate',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::DELEGATE,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::UPDATE,
        ],
        'delegate-entity-update',
    ], [
        'isLegacyBusinessRegistration',
        MagistrateTransactionTypeEnum::BUSINESS_REGISTRATION,
        TransactionTypeGroupEnum::MAGISTRATE,
        [],
        'legacy-business-registration',
    ], [
        'isLegacyBusinessResignation',
        MagistrateTransactionTypeEnum::BUSINESS_RESIGNATION,
        TransactionTypeGroupEnum::MAGISTRATE,
        [],
        'legacy-business-resignation',
    ], [
        'isLegacyBusinessUpdate',
        MagistrateTransactionTypeEnum::BUSINESS_UPDATE,
        TransactionTypeGroupEnum::MAGISTRATE,
        [],
        'legacy-business-update',
    ], [
        'isLegacyBridgechainRegistration',
        MagistrateTransactionTypeEnum::BRIDGECHAIN_REGISTRATION,
        TransactionTypeGroupEnum::MAGISTRATE,
        [],
        'bridgechain-registration',
    ], [
        'isLegacyBridgechainResignation',
        MagistrateTransactionTypeEnum::BRIDGECHAIN_RESIGNATION,
        TransactionTypeGroupEnum::MAGISTRATE,
        [],
        'bridgechain-resignation',
    ], [
        'isLegacyBridgechainUpdate',
        MagistrateTransactionTypeEnum::BRIDGECHAIN_UPDATE,
        TransactionTypeGroupEnum::MAGISTRATE,
        [],
        'bridgechain-update',
    ], [
        'isUnknown',
        0,
        0,
        [],
        'unknown',
    ],
]);

it('should play through every scenario of an unknown type', function (string $method, int $type, int $typeGroup, array $asset) {
    $transaction = Transaction::factory()->create([
        'type'       => $type,
        'type_group' => $typeGroup,
        'asset'      => $asset,
    ]);

    expect((new TransactionType($transaction))->isUnknown())->toBeFalse();
})->with([
    [
        'isTransfer',
        CoreTransactionTypeEnum::TRANSFER,
        TransactionTypeGroupEnum::CORE,
        [],
    ], [
        'isSecondSignature',
        CoreTransactionTypeEnum::SECOND_SIGNATURE,
        TransactionTypeGroupEnum::CORE,
        [],
    ], [
        'isDelegateRegistration',
        CoreTransactionTypeEnum::DELEGATE_REGISTRATION,
        TransactionTypeGroupEnum::CORE,
        [],
    ], [
        'isVote',
        CoreTransactionTypeEnum::VOTE,
        TransactionTypeGroupEnum::CORE,
        [
            'votes' => ['+publicKey'],
        ],
    ], [
        'isUnvote',
        CoreTransactionTypeEnum::VOTE,
        TransactionTypeGroupEnum::CORE,
        [
            'votes' => ['-publicKey'],
        ],
    ], [
        'isVoteCombination',
        CoreTransactionTypeEnum::VOTE,
        TransactionTypeGroupEnum::CORE,
        [
            'votes' => ['+publicKey', '-publicKey'],
        ],
    ], [
        'isMultiSignature',
        CoreTransactionTypeEnum::MULTI_SIGNATURE,
        TransactionTypeGroupEnum::CORE,
        [],
    ], [
        'isIpfs',
        CoreTransactionTypeEnum::IPFS,
        TransactionTypeGroupEnum::CORE,
        [],
    ], [
        'isDelegateResignation',
        CoreTransactionTypeEnum::DELEGATE_RESIGNATION,
        TransactionTypeGroupEnum::CORE,
        [],
    ], [
        'isMultiPayment',
        CoreTransactionTypeEnum::MULTI_PAYMENT,
        TransactionTypeGroupEnum::CORE,
        [],
    ], [
        'isTimelock',
        CoreTransactionTypeEnum::TIMELOCK,
        TransactionTypeGroupEnum::CORE,
        [],
    ], [
        'isTimelockClaim',
        CoreTransactionTypeEnum::TIMELOCK_CLAIM,
        TransactionTypeGroupEnum::CORE,
        [],
    ], [
        'isTimelockRefund',
        CoreTransactionTypeEnum::TIMELOCK_REFUND,
        TransactionTypeGroupEnum::CORE,
        [],
    ], [
        'isBusinessEntityRegistration',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::BUSINESS,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::REGISTER,
        ],
    ], [
        'isBusinessEntityResignation',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::BUSINESS,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::RESIGN,
        ],
    ], [
        'isBusinessEntityUpdate',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::BUSINESS,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::UPDATE,
        ],
    ], [
        'isProductEntityRegistration',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::PRODUCT,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::REGISTER,
        ],
    ], [
        'isProductEntityResignation',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::PRODUCT,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::RESIGN,
        ],
    ], [
        'isProductEntityUpdate',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::PRODUCT,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::UPDATE,
        ],
    ], [
        'isPluginEntityRegistration',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::PLUGIN,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::REGISTER,
        ],
    ], [
        'isPluginEntityResignation',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::PLUGIN,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::RESIGN,
        ],
    ], [
        'isPluginEntityUpdate',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::PLUGIN,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::UPDATE,
        ],
    ], [
        'isModuleEntityRegistration',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::MODULE,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::REGISTER,
        ],
    ], [
        'isModuleEntityResignation',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::MODULE,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::RESIGN,
        ],
    ], [
        'isModuleEntityUpdate',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::MODULE,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::UPDATE,
        ],
    ], [
        'isDelegateEntityRegistration',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::DELEGATE,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::REGISTER,
        ],
    ], [
        'isDelegateEntityResignation',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::DELEGATE,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::RESIGN,
        ],
    ], [
        'isDelegateEntityUpdate',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::DELEGATE,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::UPDATE,
        ],
    ], [
        'isLegacyBusinessRegistration',
        MagistrateTransactionTypeEnum::BUSINESS_REGISTRATION,
        TransactionTypeGroupEnum::MAGISTRATE,
        [],
    ], [
        'isLegacyBusinessResignation',
        MagistrateTransactionTypeEnum::BUSINESS_RESIGNATION,
        TransactionTypeGroupEnum::MAGISTRATE,
        [],
    ], [
        'isLegacyBusinessUpdate',
        MagistrateTransactionTypeEnum::BUSINESS_UPDATE,
        TransactionTypeGroupEnum::MAGISTRATE,
        [],
    ], [
        'isLegacyBridgechainRegistration',
        MagistrateTransactionTypeEnum::BRIDGECHAIN_REGISTRATION,
        TransactionTypeGroupEnum::MAGISTRATE,
        [],
    ], [
        'isLegacyBridgechainResignation',
        MagistrateTransactionTypeEnum::BRIDGECHAIN_RESIGNATION,
        TransactionTypeGroupEnum::MAGISTRATE,
        [],
    ], [
        'isLegacyBridgechainUpdate',
        MagistrateTransactionTypeEnum::BRIDGECHAIN_UPDATE,
        TransactionTypeGroupEnum::MAGISTRATE,
        [],
    ],
]);
