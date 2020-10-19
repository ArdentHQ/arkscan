<?php

declare(strict_types=1);

use App\Enums\CoreTransactionTypeEnum;
use App\Enums\MagistrateTransactionEntityActionEnum;
use App\Enums\MagistrateTransactionEntitySubTypeEnum;

use App\Enums\MagistrateTransactionEntityTypeEnum;
use App\Enums\MagistrateTransactionTypeEnum;
use App\Enums\TransactionTypeGroupEnum;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use App\ViewModels\TransactionViewModel;
use function Tests\configureExplorerDatabase;

beforeEach(function () {
    configureExplorerDatabase();

    $block = Block::factory()->create(['height' => 1]);
    Block::factory()->create(['height' => 5000000]);

    $this->subject = new TransactionViewModel(Transaction::factory()->create([
        'block_id'          => $block->id,
        'fee'               => 1 * 1e8,
        'amount'            => 2 * 1e8,
        'sender_public_key' => Wallet::factory()->create(['address' => 'sender'])->public_key,
        'recipient_id'      => Wallet::factory()->create(['address' => 'recipient'])->address,
    ]));
});

it('should determine if the transaction is incoming', function () {
    expect($this->subject->isSent('sender'))->toBeTrue();
    expect($this->subject->isSent('recipient'))->toBeFalse();
});

it('should determine if the transaction is outgoing', function () {
    expect($this->subject->isReceived('recipient'))->toBeTrue();
    expect($this->subject->isReceived('sender'))->toBeFalse();
});

it('should get the timestamp', function () {
    expect($this->subject->timestamp())->toBeString();
    expect($this->subject->timestamp())->toBe('19 Oct 2020 (04:54:16)');
});

it('should get the fee', function () {
    expect($this->subject->fee())->toBeString();
    expect($this->subject->fee())->toBe('ARK 1.00');
});

it('should get the amount', function () {
    expect($this->subject->amount())->toBeString();
    expect($this->subject->amount())->toBe('ARK 2.00');
});

it('should get the confirmations', function () {
    expect($this->subject->confirmations())->toBeString();
    expect($this->subject->confirmations())->toBe('4,999,999');
});

it('should determine if the transaction is confirmed', function () {
    expect($this->subject->isConfirmed())->toBeTrue();
});

it('should determine the transaction type', function (string $method, int $type, int $typeGroup, array $asset) {
    $subject = new TransactionViewModel(Transaction::factory()->create([
        'type'       => $type,
        'type_group' => $typeGroup,
        'asset'      => $asset,
    ]));

    expect($subject->$method())->toBeTrue();

    $subject = new TransactionViewModel(Transaction::factory()->create([
        'type'       => 666,
        'type_group' => 666,
        'asset'      => $asset,
    ]));

    expect($subject->$method())->toBeFalse();
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
        [],
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
        'isEntityRegistration',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'action' => MagistrateTransactionEntityActionEnum::REGISTER,
        ],
    ], [
        'isEntityResignation',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'action' => MagistrateTransactionEntityActionEnum::RESIGN,
        ],
    ], [
        'isEntityUpdate',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'action' => MagistrateTransactionEntityActionEnum::UPDATE,
        ],
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
            'type'    => MagistrateTransactionEntityTypeEnum::PLUGIN,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::REGISTER,
        ],
    ], [
        'isModuleEntityResignation',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::PLUGIN,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::RESIGN,
        ],
    ], [
        'isModuleEntityUpdate',
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::PLUGIN,
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
