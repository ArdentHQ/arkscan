<?php

declare(strict_types=1);

use App\DTO\Payment;
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
use App\ViewModels\WalletViewModel;

use function Spatie\Snapshots\assertMatchesSnapshot;
use function Tests\configureExplorerDatabase;

beforeEach(function () {
    configureExplorerDatabase();

    $this->block = Block::factory()->create(['height' => 1]);
    Block::factory()->create(['height' => 5000000]);

    $this->subject = new TransactionViewModel(Transaction::factory()->create([
        'block_id'          => $this->block->id,
        'fee'               => '100000000',
        'amount'            => '200000000',
        'sender_public_key' => Wallet::factory()->create(['address' => 'sender'])->public_key,
        'recipient_id'      => Wallet::factory()->create(['address' => 'recipient'])->address,
    ]));
});

it('should get the url', function () {
    expect($this->subject->url())->toBeString();
    expect($this->subject->url())->toBe(route('transaction', $this->subject->id()));
});

it('should determine if the transaction is incoming', function () {
    expect($this->subject->isReceived('recipient'))->toBeTrue();
    expect($this->subject->isReceived('sender'))->toBeFalse();
});

it('should determine if the transaction is outgoing', function () {
    expect($this->subject->isSent('sender'))->toBeTrue();
    expect($this->subject->isSent('recipient'))->toBeFalse();
});

it('should get the timestamp', function () {
    expect($this->subject->timestamp())->toBeString();
    expect($this->subject->timestamp())->toBe('19 Oct 2020 04:54:16');
});

it('should get the block ID', function () {
    expect($this->subject->blockId())->toBeString();
    expect($this->subject->blockId())->toBe($this->block->id);
});

it('should get the fee', function () {
    expect($this->subject->fee())->toBeString();

    assertMatchesSnapshot($this->subject->fee());
});

it('should get the amount', function () {
    expect($this->subject->amount())->toBeString();

    assertMatchesSnapshot($this->subject->amount());
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

it('should determine if the transaction is self-receiving', function (int $type, int $typeGroup, array $asset) {
    $subject = new TransactionViewModel(Transaction::factory()->create([
        'type'       => $type,
        'type_group' => $typeGroup,
        'asset'      => $asset,
    ]));

    expect($subject->isSelfReceiving())->toBeTrue();

    $subject = new TransactionViewModel(Transaction::factory()->create([
        'type'       => 666,
        'type_group' => 666,
        'asset'      => $asset,
    ]));

    expect($subject->isSelfReceiving())->toBeFalse();
})->with([
    [
        CoreTransactionTypeEnum::SECOND_SIGNATURE,
        TransactionTypeGroupEnum::CORE,
        [],
    ], [
        CoreTransactionTypeEnum::DELEGATE_REGISTRATION,
        TransactionTypeGroupEnum::CORE,
        [],
    ], [
        CoreTransactionTypeEnum::VOTE,
        TransactionTypeGroupEnum::CORE,
        [
            'votes' => ['+publicKey'],
        ],
    ], [
        CoreTransactionTypeEnum::VOTE,
        TransactionTypeGroupEnum::CORE,
        [
            'votes' => ['-publicKey'],
        ],
    ], [
        CoreTransactionTypeEnum::VOTE,
        TransactionTypeGroupEnum::CORE,
        [
            'votes' => ['+publicKey', '-publicKey'],
        ],
    ], [
        CoreTransactionTypeEnum::DELEGATE_RESIGNATION,
        TransactionTypeGroupEnum::CORE,
        [],
    ], [
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'action' => MagistrateTransactionEntityActionEnum::REGISTER,
        ],
    ], [
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'action' => MagistrateTransactionEntityActionEnum::RESIGN,
        ],
    ], [
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'action' => MagistrateTransactionEntityActionEnum::UPDATE,
        ],
    ], [
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::BUSINESS,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::REGISTER,
        ],
    ], [
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::BUSINESS,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::RESIGN,
        ],
    ], [
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::BUSINESS,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::UPDATE,
        ],
    ], [
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::PRODUCT,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::REGISTER,
        ],
    ], [
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::PRODUCT,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::RESIGN,
        ],
    ], [
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::PRODUCT,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::UPDATE,
        ],
    ], [
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::PLUGIN,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::REGISTER,
        ],
    ], [
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::PLUGIN,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::RESIGN,
        ],
    ], [
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::PLUGIN,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::UPDATE,
        ],
    ], [
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::MODULE,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::REGISTER,
        ],
    ], [
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::MODULE,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::RESIGN,
        ],
    ], [
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::MODULE,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::UPDATE,
        ],
    ], [
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::DELEGATE,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::REGISTER,
        ],
    ], [
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::DELEGATE,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::RESIGN,
        ],
    ], [
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::DELEGATE,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::UPDATE,
        ],
    ], [
        MagistrateTransactionTypeEnum::BUSINESS_REGISTRATION,
        TransactionTypeGroupEnum::MAGISTRATE,
        [],
    ], [
        MagistrateTransactionTypeEnum::BUSINESS_RESIGNATION,
        TransactionTypeGroupEnum::MAGISTRATE,
        [],
    ], [
        MagistrateTransactionTypeEnum::BUSINESS_UPDATE,
        TransactionTypeGroupEnum::MAGISTRATE,
        [],
    ], [
        MagistrateTransactionTypeEnum::BRIDGECHAIN_REGISTRATION,
        TransactionTypeGroupEnum::MAGISTRATE,
        [],
    ], [
        MagistrateTransactionTypeEnum::BRIDGECHAIN_RESIGNATION,
        TransactionTypeGroupEnum::MAGISTRATE,
        [],
    ], [
        MagistrateTransactionTypeEnum::BRIDGECHAIN_UPDATE,
        TransactionTypeGroupEnum::MAGISTRATE,
        [],
    ],
]);

it('should determine the state icon', function () {
    expect($this->subject->iconState())->toBeString();
});

it('should determine the type icon', function () {
    expect($this->subject->iconType())->toBeString();
});

it('should determine the direction icon', function () {
    expect($this->subject->iconDirection('sender'))->toBeString();
});

it('should fail to get the confirmations', function () {
    $this->subject = new TransactionViewModel(Transaction::factory()->create([
        'block_id' => 'unknown',
    ]));

    expect($this->subject->confirmations())->toBeString();
    expect($this->subject->confirmations())->toBe('0');
});

it('should fail to get the sender', function () {
    $this->subject = new TransactionViewModel(Transaction::factory()->create([
        'sender_public_key' => 'unknown',
    ]));

    expect($this->subject->sender())->toBeNull();
});

it('should fallback to the sender if no recipient exists', function () {
    $this->subject = new TransactionViewModel(Transaction::factory()->create([
        'recipient_id' => 'unknown',
    ]));

    expect($this->subject->recipient())->toEqual($this->subject->sender());
});

it('should get the voted delegate', function () {
    $wallet = Wallet::factory()->create(['public_key' => 'publicKey']);

    $subject = new TransactionViewModel(Transaction::factory()->create([
        'type'       => CoreTransactionTypeEnum::VOTE,
        'type_group' => TransactionTypeGroupEnum::CORE,
        'asset'      => ['votes' => ['+publicKey']],
    ]));

    expect($subject->voted())->toBeInstanceOf(WalletViewModel::class);
});

it('should fail to get the voted delegate if the transaction is not an unvote', function () {
    $subject = new TransactionViewModel(Transaction::factory()->create([
        'type'       => CoreTransactionTypeEnum::VOTE,
        'type_group' => TransactionTypeGroupEnum::CORE,
        'asset'      => ['votes' => ['-publicKey']],
    ]));

    expect($subject->voted())->toBeNull();
});

it('should fail to get the voted delegate if the transaction asset is empty', function ($asset) {
    $subject = new TransactionViewModel(Transaction::factory()->create([
        'type'       => CoreTransactionTypeEnum::VOTE,
        'type_group' => TransactionTypeGroupEnum::CORE,
        'asset'      => $asset,
    ]));

    expect($subject->voted())->toBeNull();
})->with([[[]], null]);

it('should get the unvoted delegate', function () {
    $wallet = Wallet::factory()->create(['public_key' => 'publicKey']);

    $subject = new TransactionViewModel(Transaction::factory()->create([
        'type'       => CoreTransactionTypeEnum::VOTE,
        'type_group' => TransactionTypeGroupEnum::CORE,
        'asset'      => ['votes' => ['-publicKey']],
    ]));

    expect($subject->unvoted())->toBeInstanceOf(WalletViewModel::class);
});

it('should fail to get the unvoted delegate if the transaction is not an unvote', function () {
    $subject = new TransactionViewModel(Transaction::factory()->create([
        'type'       => CoreTransactionTypeEnum::VOTE,
        'type_group' => TransactionTypeGroupEnum::CORE,
        'asset'      => ['votes' => ['+publicKey']],
    ]));

    expect($subject->unvoted())->toBeNull();
});

it('should fail to get the unvoted delegate if the transaction asset is empty', function ($asset) {
    $subject = new TransactionViewModel(Transaction::factory()->create([
        'type'       => CoreTransactionTypeEnum::VOTE,
        'type_group' => TransactionTypeGroupEnum::CORE,
        'asset'      => $asset,
    ]));

    expect($subject->unvoted())->toBeNull();
})->with([[[]], null]);

it('should get the nonce', function () {
    expect($this->subject->nonce())->toBeString();
});

it('should get the multi signature address', function () {
    expect($this->subject->multiSignatureAddress())->toBeNull();

    $this->subject = new TransactionViewModel(Transaction::factory()->create([
        'type'       => CoreTransactionTypeEnum::MULTI_SIGNATURE,
        'type_group' => TransactionTypeGroupEnum::CORE,
        'asset'      => null,
    ]));

    expect($this->subject->multiSignatureAddress())->toBeNull();

    $this->subject = new TransactionViewModel(Transaction::factory()->create([
        'type'       => CoreTransactionTypeEnum::MULTI_SIGNATURE,
        'type_group' => TransactionTypeGroupEnum::CORE,
        'asset'      => [
            'multiSignature' => [
                'min'        => 3,
                'publicKeys' => [
                    '02fb3def2593a00c5b84620addf28ff21bac452bd71a37d4d8e24f301683a81b56',
                    '02bc9f661fcc8abca65fe9aff4614036867b7fdcc5730085ccc5cb854664d0194b',
                    '03c44c6b6cc9893ae21ca606712fd0f6f03c41ce81c4f6ce5a640f4b0b82ec1ce0',
                    '020300039e973baf5e46b945777cfae330d6392cdb039b1cebc5c3382d421166c3',
                    '03b050073621b9b5caec9461d44d6bcf21a858c47dd88230ce723e25c1bc75c219',
                ],
            ],
        ],
    ]));

    expect($this->subject->multiSignatureAddress())->toBeString();
});

it('should get the payments', function () {
    $this->subject = new TransactionViewModel(Transaction::factory()->create([
        'type'       => CoreTransactionTypeEnum::MULTI_PAYMENT,
        'type_group' => TransactionTypeGroupEnum::CORE,
        'asset'      => null,
    ]));

    expect($this->subject->payments())->toBeEmpty();

    $A = Wallet::factory()->create();
    $B = Wallet::factory()->create();
    $C = Wallet::factory()->create();
    $D = Wallet::factory()->create();
    $E = Wallet::factory()->create();

    $model = Transaction::factory()->create([
        'type'       => CoreTransactionTypeEnum::MULTI_PAYMENT,
        'type_group' => TransactionTypeGroupEnum::CORE,
        'asset'      => [
            'payments' => [
                [
                    'amount'      => '1000000000',
                    'recipientId' => $A->address,
                ], [
                    'amount'      => '2000000000',
                    'recipientId' => $B->address,
                ], [
                    'amount'      => '3000000000',
                    'recipientId' => $C->address,
                ], [
                    'amount'      => '4000000000',
                    'recipientId' => $D->address,
                ], [
                    'amount'      => '5000000000',
                    'recipientId' => $E->address,
                ],
            ],
        ],
    ]);

    $this->subject = new TransactionViewModel($model);

    expect($this->subject->payments()[0])->toEqual(new Payment((int) $model->timestamp, '10 DARK', $A->address, $A->attributes['delegate']['username']));
    expect($this->subject->payments()[1])->toEqual(new Payment((int) $model->timestamp, '20 DARK', $B->address, $B->attributes['delegate']['username']));
    expect($this->subject->payments()[2])->toEqual(new Payment((int) $model->timestamp, '30 DARK', $C->address, $C->attributes['delegate']['username']));
    expect($this->subject->payments()[3])->toEqual(new Payment((int) $model->timestamp, '40 DARK', $D->address, $D->attributes['delegate']['username']));
    expect($this->subject->payments()[4])->toEqual(new Payment((int) $model->timestamp, '50 DARK', $E->address, $E->attributes['delegate']['username']));
});

it('should fail to get the payments if the transaction is not a multi payment', function () {
    $this->subject = new TransactionViewModel(Transaction::factory()->create([
        'type'       => CoreTransactionTypeEnum::TRANSFER,
        'type_group' => TransactionTypeGroupEnum::CORE,
    ]));

    expect($this->subject->payments())->toBeEmpty();
});

it('should get the recipients count', function () {
    $this->subject = new TransactionViewModel(Transaction::factory()->create([
        'type'       => CoreTransactionTypeEnum::MULTI_PAYMENT,
        'type_group' => TransactionTypeGroupEnum::CORE,
        'asset'      => null,
    ]));

    expect($this->subject->recipientsCount())->toBe('0');

    $this->subject = new TransactionViewModel(Transaction::factory()->create([
        'type'       => CoreTransactionTypeEnum::MULTI_PAYMENT,
        'type_group' => TransactionTypeGroupEnum::CORE,
        'asset'      => ['payments' => []],
    ]));

    expect($this->subject->recipientsCount())->toBe('0');

    $this->subject = new TransactionViewModel(Transaction::factory()->create([
        'type'       => CoreTransactionTypeEnum::MULTI_PAYMENT,
        'type_group' => TransactionTypeGroupEnum::CORE,
        'asset'      => [
            'payments' => [
                ['amount' => 10, 'recipientId' => 'ABC'],
                ['amount' => 10, 'recipientId' => 'ABC'],
                ['amount' => 10, 'recipientId' => 'ABC'],
                ['amount' => 10, 'recipientId' => 'ABC'],
                ['amount' => 10, 'recipientId' => 'ABC'],
            ],
        ],
    ]));

    expect($this->subject->recipientsCount())->toBe('5');
});

it('should fail to get the recipients count if the transaction is not a multi payment', function () {
    $this->subject = new TransactionViewModel(Transaction::factory()->create([
        'type'       => CoreTransactionTypeEnum::TRANSFER,
        'type_group' => TransactionTypeGroupEnum::CORE,
    ]));

    expect($this->subject->recipientsCount())->toBe('0');
});

it('should get the participants', function () {
    $this->subject = new TransactionViewModel(Transaction::factory()->create([
        'type'       => CoreTransactionTypeEnum::MULTI_SIGNATURE,
        'type_group' => TransactionTypeGroupEnum::CORE,
        'asset'      => null,
    ]));

    expect($this->subject->participants())->toHaveCount(0);

    $this->subject = new TransactionViewModel(Transaction::factory()->create([
        'type'       => CoreTransactionTypeEnum::MULTI_SIGNATURE,
        'type_group' => TransactionTypeGroupEnum::CORE,
        'asset'      => [
            'multiSignature' => [
                'min'        => 3,
                'publicKeys' => [
                    '02fb3def2593a00c5b84620addf28ff21bac452bd71a37d4d8e24f301683a81b56',
                    '02bc9f661fcc8abca65fe9aff4614036867b7fdcc5730085ccc5cb854664d0194b',
                    '03c44c6b6cc9893ae21ca606712fd0f6f03c41ce81c4f6ce5a640f4b0b82ec1ce0',
                    '020300039e973baf5e46b945777cfae330d6392cdb039b1cebc5c3382d421166c3',
                    '03b050073621b9b5caec9461d44d6bcf21a858c47dd88230ce723e25c1bc75c219',
                ],
            ],
        ],
    ]));

    expect($this->subject->participants())->toHaveCount(5);
});

it('should fail to get the participants if the transaction is not a multi signature registrations', function () {
    $this->subject = new TransactionViewModel(Transaction::factory()->create([
        'type'       => CoreTransactionTypeEnum::TRANSFER,
        'type_group' => TransactionTypeGroupEnum::CORE,
    ]));

    expect($this->subject->participants())->toBeEmpty();
});

it('should get the type component', function () {
    $this->subject = new TransactionViewModel(Transaction::factory()->create([
        'type'       => CoreTransactionTypeEnum::TRANSFER,
        'type_group' => TransactionTypeGroupEnum::CORE,
    ]));

    expect($this->subject->typeComponent())->toBe('transaction.details.transfer');

    $this->subject = new TransactionViewModel(Transaction::factory()->create([
        'type'       => CoreTransactionTypeEnum::TIMELOCK,
        'type_group' => TransactionTypeGroupEnum::CORE,
    ]));

    expect($this->subject->typeComponent())->toBe('transaction.details.fallback');
});

it('should get the extra component', function () {
    expect($this->subject->extraComponent())->toBeString();
});

it('should determine if the transaction has extra data', function (bool $outcome, int $type, int $typeGroup, array $asset) {
    $subject = new TransactionViewModel(Transaction::factory()->create([
        'type'       => $type,
        'type_group' => $typeGroup,
        'asset'      => $asset,
    ]));

    expect($subject->hasExtraData())->toBe($outcome);
})->with([
    [
        false,
        CoreTransactionTypeEnum::TRANSFER,
        TransactionTypeGroupEnum::CORE,
        [],
    ], [
        false,
        CoreTransactionTypeEnum::SECOND_SIGNATURE,
        TransactionTypeGroupEnum::CORE,
        [],
    ], [
        false,
        CoreTransactionTypeEnum::DELEGATE_REGISTRATION,
        TransactionTypeGroupEnum::CORE,
        [],
    ], [
        false,
        CoreTransactionTypeEnum::VOTE,
        TransactionTypeGroupEnum::CORE,
        [
            'votes' => ['+publicKey'],
        ],
    ], [
        false,
        CoreTransactionTypeEnum::VOTE,
        TransactionTypeGroupEnum::CORE,
        [
            'votes' => ['-publicKey'],
        ],
    ], [
        true,
        CoreTransactionTypeEnum::VOTE,
        TransactionTypeGroupEnum::CORE,
        [
            'votes' => ['+publicKey', '-publicKey'],
        ],
    ], [
        true,
        CoreTransactionTypeEnum::MULTI_SIGNATURE,
        TransactionTypeGroupEnum::CORE,
        [],
    ], [
        false,
        CoreTransactionTypeEnum::IPFS,
        TransactionTypeGroupEnum::CORE,
        [],
    ], [
        false,
        CoreTransactionTypeEnum::DELEGATE_RESIGNATION,
        TransactionTypeGroupEnum::CORE,
        [],
    ], [
        true,
        CoreTransactionTypeEnum::MULTI_PAYMENT,
        TransactionTypeGroupEnum::CORE,
        [],
    ], [
        false,
        CoreTransactionTypeEnum::TIMELOCK,
        TransactionTypeGroupEnum::CORE,
        [],
    ], [
        false,
        CoreTransactionTypeEnum::TIMELOCK_CLAIM,
        TransactionTypeGroupEnum::CORE,
        [],
    ], [
        false,
        CoreTransactionTypeEnum::TIMELOCK_REFUND,
        TransactionTypeGroupEnum::CORE,
        [],
    ], [
        false,
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'action' => MagistrateTransactionEntityActionEnum::REGISTER,
        ],
    ], [
        false,
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'action' => MagistrateTransactionEntityActionEnum::RESIGN,
        ],
    ], [
        false,
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'action' => MagistrateTransactionEntityActionEnum::UPDATE,
        ],
    ], [
        false,
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::BUSINESS,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::REGISTER,
        ],
    ], [
        false,
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::BUSINESS,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::RESIGN,
        ],
    ], [
        false,
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::BUSINESS,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::UPDATE,
        ],
    ], [
        false,
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::PRODUCT,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::REGISTER,
        ],
    ], [
        false,
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::PRODUCT,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::RESIGN,
        ],
    ], [
        false,
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::PRODUCT,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::UPDATE,
        ],
    ], [
        false,
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::PLUGIN,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::REGISTER,
        ],
    ], [
        false,
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::PLUGIN,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::RESIGN,
        ],
    ], [
        false,
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::PLUGIN,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::UPDATE,
        ],
    ], [
        false,
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::MODULE,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::REGISTER,
        ],
    ], [
        false,
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::MODULE,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::RESIGN,
        ],
    ], [
        false,
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::MODULE,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::UPDATE,
        ],
    ], [
        false,
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::DELEGATE,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::REGISTER,
        ],
    ], [
        false,
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::DELEGATE,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::RESIGN,
        ],
    ], [
        false,
        MagistrateTransactionTypeEnum::ENTITY,
        TransactionTypeGroupEnum::MAGISTRATE,
        [
            'type'    => MagistrateTransactionEntityTypeEnum::DELEGATE,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::UPDATE,
        ],
    ], [
        false,
        MagistrateTransactionTypeEnum::BUSINESS_REGISTRATION,
        TransactionTypeGroupEnum::MAGISTRATE,
        [],
    ], [
        false,
        MagistrateTransactionTypeEnum::BUSINESS_RESIGNATION,
        TransactionTypeGroupEnum::MAGISTRATE,
        [],
    ], [
        false,
        MagistrateTransactionTypeEnum::BUSINESS_UPDATE,
        TransactionTypeGroupEnum::MAGISTRATE,
        [],
    ], [
        false,
        MagistrateTransactionTypeEnum::BRIDGECHAIN_REGISTRATION,
        TransactionTypeGroupEnum::MAGISTRATE,
        [],
    ], [
        false,
        MagistrateTransactionTypeEnum::BRIDGECHAIN_RESIGNATION,
        TransactionTypeGroupEnum::MAGISTRATE,
        [],
    ], [
        false,
        MagistrateTransactionTypeEnum::BRIDGECHAIN_UPDATE,
        TransactionTypeGroupEnum::MAGISTRATE,
        [],
    ],
]);

it('should get the entity name', function () {
    $subject = new TransactionViewModel(Transaction::factory()->create([
        'type'       => MagistrateTransactionTypeEnum::ENTITY,
        'type_group' => TransactionTypeGroupEnum::MAGISTRATE,
        'asset'      => [
            'type'    => MagistrateTransactionEntityTypeEnum::MODULE,
            'subType' => MagistrateTransactionEntitySubTypeEnum::NONE,
            'action'  => MagistrateTransactionEntityActionEnum::REGISTER,
            'data'    => [
                'name' => 'john',
            ],
        ],
    ]));

    expect($subject->entityName())->toBe('john');
});

it('should determine if the transaction type is unknown', function () {
    $subject = new TransactionViewModel(Transaction::factory()->create([
        'type'       => 123,
        'type_group' => 456,
    ]));

    expect($subject->isUnknown())->toBeTrue();
});
