<?php

declare(strict_types=1);

use App\Enums\CoreTransactionTypeEnum;
use App\Enums\MagistrateTransactionEntityActionEnum;
use App\Enums\MagistrateTransactionEntitySubTypeEnum;
use App\Enums\MagistrateTransactionEntityTypeEnum;
use App\Enums\MagistrateTransactionTypeEnum;
use App\Enums\TransactionTypeGroupEnum;
use App\Models\Transaction;
use App\Services\Transactions\TransactionTypeIcon;
use function Tests\configureExplorerDatabase;

beforeEach(fn () => configureExplorerDatabase());

it('should determine the icon that matches the type', function (string $method, int $type, int $typeGroup, array $asset, string $icon) {
    $transaction = Transaction::factory()->create([
        'type'       => $type,
        'type_group' => $typeGroup,
        'asset'      => $asset,
    ]);

    expect((new TransactionTypeIcon($transaction))->name())->toBe($icon);
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
            '+publicKey',
        ],
        'vote',
    ], [
        'isUnvote',
        CoreTransactionTypeEnum::VOTE,
        TransactionTypeGroupEnum::CORE,
        [
            '-publicKey',
        ],
        'unvote',
    ], [
        'isVoteCombination',
        CoreTransactionTypeEnum::VOTE,
        TransactionTypeGroupEnum::CORE,
        [
            '-publicKey',
            '+publicKey',
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
        'timelock',
    ], [
        'isTimelockRefund',
        CoreTransactionTypeEnum::TIMELOCK_REFUND,
        TransactionTypeGroupEnum::CORE,
        [],
        'timelock',
    ], [
        'isBusinessEntityRegistration',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::BUSINESS,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::REGISTER,
        ],
        'business',
    ], [
        'isBusinessEntityResignation',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::BUSINESS,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::RESIGN,
        ],
        'business',
    ], [
        'isBusinessEntityUpdate',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::BUSINESS,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::UPDATE,
        ],
        'business',
    ], [
        'isProductEntityRegistration',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::PRODUCT,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::REGISTER,
        ],
        'product',
    ], [
        'isProductEntityResignation',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::PRODUCT,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::RESIGN,
        ],
        'product',
    ], [
        'isProductEntityUpdate',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::PRODUCT,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::UPDATE,
        ],
        'product',
    ], [
        'isPluginEntityRegistration',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::PLUGIN,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::REGISTER,
        ],
        'plugin',
    ], [
        'isPluginEntityResignation',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::PLUGIN,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::RESIGN,
        ],
        'plugin',
    ], [
        'isPluginEntityUpdate',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::PLUGIN,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::UPDATE,
        ],
        'plugin',
    ], [
        'isModuleEntityRegistration',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::MODULE,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::REGISTER,
        ],
        'module',
    ], [
        'isModuleEntityResignation',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::MODULE,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::RESIGN,
        ],
        'module',
    ], [
        'isModuleEntityUpdate',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::MODULE,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::UPDATE,
        ],
        'module',
    ], [
        'isDelegateEntityRegistration',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::DELEGATE,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::REGISTER,
        ],
        'delegate-registration',
    ], [
        'isDelegateEntityResignation',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::DELEGATE,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::RESIGN,
        ],
        'delegate-registration',
    ], [
        'isDelegateEntityUpdate',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::DELEGATE,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::UPDATE,
        ],
        'delegate-registration',
    ], [
        'isLegacyBusinessRegistration',
        MagistrateTransactionTypeEnum::BUSINESS_REGISTRATION,
        TransactionTypeGroupEnum::MAGISTRATE,
        [],
        'business',
    ], [
        'isLegacyBusinessResignation',
        MagistrateTransactionTypeEnum::BUSINESS_RESIGNATION,
        TransactionTypeGroupEnum::MAGISTRATE,
        [],
        'business',
    ], [
        'isLegacyBusinessUpdate',
        MagistrateTransactionTypeEnum::BUSINESS_UPDATE,
        TransactionTypeGroupEnum::MAGISTRATE,
        [],
        'business',
    ], [
        'isLegacyBridgechainRegistration',
        MagistrateTransactionTypeEnum::BRIDGECHAIN_REGISTRATION,
        TransactionTypeGroupEnum::MAGISTRATE,
        [],
        'bridgechain',
    ], [
        'isLegacyBridgechainResignation',
        MagistrateTransactionTypeEnum::BRIDGECHAIN_RESIGNATION,
        TransactionTypeGroupEnum::MAGISTRATE,
        [],
        'bridgechain',
    ], [
        'isLegacyBridgechainUpdate',
        MagistrateTransactionTypeEnum::BRIDGECHAIN_UPDATE,
        TransactionTypeGroupEnum::MAGISTRATE,
        [],
        'bridgechain',
    ],
]);
