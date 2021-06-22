<?php

declare(strict_types=1);

use App\DTO\Payment;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Blockchain\NetworkFactory;
use App\Services\Cache\CryptoCompareCache;
use App\Services\Cache\NetworkCache;
use App\ViewModels\TransactionViewModel;
use App\ViewModels\WalletViewModel;
use ArkEcosystem\Crypto\Configuration\Network as NetworkConfiguration;
use ArkEcosystem\Crypto\Identities\Address;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use function Spatie\Snapshots\assertMatchesSnapshot;
use function Tests\configureExplorerDatabase;

beforeEach(function () {
    configureExplorerDatabase();

    $this->block = Block::factory()->create(['height' => 1]);
    Block::factory()->create(['height' => 5000000]);

    (new NetworkCache())->setHeight(fn () => 5000000);

    $this->sender = Wallet::factory()->create();
    $this->subject = new TransactionViewModel(Transaction::factory()->create([
        'block_id'          => $this->block->id,
        'block_height'      => 1,
        'fee'               => '100000000',
        'amount'            => '200000000',
        'sender_public_key' => $this->sender->public_key,
        'recipient_id'      => Wallet::factory()->create(['address' => 'recipient'])->address,
    ]));
});

it('should get the url', function () {
    expect($this->subject->url())->toBeString();
    expect($this->subject->url())->toBe(route('transaction', $this->subject->id()));
});

it('should determine if the transaction is incoming', function () {
    expect($this->subject->isReceived('recipient'))->toBeTrue();
    expect($this->subject->isReceived('D6Z26L69gdk9qYmTv5uzk3uGepigtHY4ax'))->toBeFalse();
});

it('should determine if the transaction is outgoing', function () {
    expect($this->subject->isSent($this->sender->address))->toBeTrue();
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
    expect($this->subject->fee())->toBeFloat();

    assertMatchesSnapshot($this->subject->fee());
});

it('should get the amount', function () {
    expect($this->subject->amount())->toBeFloat();

    assertMatchesSnapshot($this->subject->amount());
});

it('should get the amount received for non-multipayment', function () {
    expect($this->subject->amountReceived('recipient'))->toBeFloat();

    assertMatchesSnapshot($this->subject->amountReceived('recipient'));
});

it('should get the amount for multi payments', function () {
    $this->subject = new TransactionViewModel(Transaction::factory()->multiPayment()->create([
        'asset' => [
            'payments' => [
                [
                    'amount'      => '1000000000',
                    'recipientId' => 'A',
                ], [
                    'amount'      => '2000000000',
                    'recipientId' => 'B',
                ], [
                    'amount'      => '3000000000',
                    'recipientId' => 'C',
                ], [
                    'amount'      => '4000000000',
                    'recipientId' => 'D',
                ], [
                    'amount'      => '5000000000',
                    'recipientId' => 'E',
                ],
            ],
        ],
    ]));

    expect($this->subject->amount())->toBeFloat();

    assertMatchesSnapshot($this->subject->amount());
});

it('should get the amount for multi payments excluding payment to the same address', function () {
    $sender = Wallet::factory()->create();

    $this->subject = new TransactionViewModel(Transaction::factory()->create([
        'sender_public_key' => $sender->public_key,
        'type'              => CoreTransactionTypeEnum::MULTI_PAYMENT,
        'type_group'        => TransactionTypeGroupEnum::CORE,
        'asset'             => [
            'payments' => [
                [
                    'amount'      => '1000000000',
                    'recipientId' => 'A',
                ], [
                    'amount'      => '2000000000',
                    'recipientId' => 'B',
                ], [
                    'amount'      => '3000000000',
                    'recipientId' => 'C',
                ], [
                    'amount'      => '5000000000',
                    'recipientId' => $sender->address,
                ], [
                    'amount'      => '4000000000',
                    'recipientId' => 'D',
                ], [
                    'amount'      => '6000000000',
                    'recipientId' => 'E',
                ],
            ],
        ],
    ]));

    expect($this->subject->amountExcludingItself())->toEqual(160);
});

it('should get the amount in fiat for multi payments excluding payment to the same address', function () {
    $sender = Wallet::factory()->create();

    $this->subject = new TransactionViewModel(Transaction::factory()->create([
        'sender_public_key' => $sender->public_key,
        'type'              => CoreTransactionTypeEnum::MULTI_PAYMENT,
        'type_group'        => TransactionTypeGroupEnum::CORE,
        'asset'             => [
            'payments' => [
                [
                    'amount'      => '1000000000',
                    'recipientId' => 'A',
                ], [
                    'amount'      => '2000000000',
                    'recipientId' => 'B',
                ], [
                    'amount'      => '3000000000',
                    'recipientId' => 'C',
                ], [
                    'amount'      => '5000000000',
                    'recipientId' => $sender->address,
                ], [
                    'amount'      => '4000000000',
                    'recipientId' => 'D',
                ], [
                    'amount'      => '6000000000',
                    'recipientId' => 'E',
                ],
            ],
        ],
    ]));

    (new CryptoCompareCache())->setPrices('USD', collect([
        Carbon::parse($this->subject->timestamp())->format('Y-m-d') => 0.2907,
    ]));

    expect($this->subject->amountFiatExcludingItself())->toEqual('46.51 USD');
});

it('should get the amount for itself on multi payments', function () {
    $sender = Wallet::factory()->create();

    $this->subject = new TransactionViewModel(Transaction::factory()->create([
        'sender_public_key' => $sender->public_key,
        'type'              => CoreTransactionTypeEnum::MULTI_PAYMENT,
        'type_group'        => TransactionTypeGroupEnum::CORE,
        'asset'             => [
            'payments' => [
                [
                    'amount'      => '1000000000',
                    'recipientId' => 'A',
                ], [
                    'amount'      => '2000000000',
                    'recipientId' => 'B',
                ], [
                    'amount'      => '3000000000',
                    'recipientId' => 'C',
                ], [
                    'amount'      => '5000000000',
                    'recipientId' => $sender->address,
                ], [
                    'amount'      => '4000000000',
                    'recipientId' => 'D',
                ], [
                    'amount'      => '6000000000',
                    'recipientId' => 'E',
                ],
            ],
        ],
    ]));

    expect($this->subject->amountForItself())->toEqual(50);
});

it('should get the specific multi payment amount for a wallet recipient', function () {
    $this->subject = new TransactionViewModel(Transaction::factory()->multiPayment()->create([
        'asset' => [
            'payments' => [
                [
                    'amount'      => '1000000000',
                    'recipientId' => 'A',
                ], [
                    'amount'      => '2000000000',
                    'recipientId' => 'B',
                ], [
                    'amount'      => '3000000000',
                    'recipientId' => 'C',
                ], [
                    'amount'      => '4000000000',
                    'recipientId' => 'D',
                ], [
                    'amount'      => '5000000000',
                    'recipientId' => 'E',
                ], [
                    'amount'      => '5000000000',
                    'recipientId' => 'B',
                ],
            ],
        ],
    ]));

    expect($this->subject->amountReceived('B'))->toBeFloat();

    assertMatchesSnapshot($this->subject->amountReceived('B'));
});

it('should get the amount as fiat', function () {
    $transaction = Transaction::factory()->multiPayment()->create([
        'asset' => [
            'payments' => [
                [
                    'amount'      => '1000000000',
                    'recipientId' => 'A',
                ], [
                    'amount'      => '2000000000',
                    'recipientId' => 'B',
                ], [
                    'amount'      => '3000000000',
                    'recipientId' => 'C',
                ], [
                    'amount'      => '4000000000',
                    'recipientId' => 'D',
                ], [
                    'amount'      => '5000000000',
                    'recipientId' => 'E',
                ],
            ],
        ],
    ]);

    $this->subject = new TransactionViewModel($transaction);

    (new CryptoCompareCache())->setPrices('USD', collect([
        Carbon::parse($this->subject->timestamp())->format('Y-m-d') => 0.2907,
    ]));

    expect($this->subject->amountFiat())->toBe('43.61 USD');

    assertMatchesSnapshot($this->subject->amountFiat());
});

it('should get the specific multi payment fiat amount for a wallet recipient', function () {
    $transaction = Transaction::factory()->multiPayment()->create([
        'asset' => [
            'payments' => [
                [
                    'amount'      => '1000000000',
                    'recipientId' => 'A',
                ], [
                    'amount'      => '2000000000',
                    'recipientId' => 'B',
                ], [
                    'amount'      => '3000000000',
                    'recipientId' => 'C',
                ], [
                    'amount'      => '4000000000',
                    'recipientId' => 'D',
                ], [
                    'amount'      => '5000000000',
                    'recipientId' => 'E',
                ], [
                    'amount'      => '5000000000',
                    'recipientId' => 'B',
                ],
            ],
        ],
    ]);

    $this->subject = new TransactionViewModel($transaction);

    (new CryptoCompareCache())->setPrices('USD', collect([
        Carbon::parse($this->subject->timestamp())->format('Y-m-d') => 0.2907,
    ]));

    expect($this->subject->amountReceivedFiat('B'))->toBe('20.35 USD');

    assertMatchesSnapshot($this->subject->amountReceivedFiat('B'));
});

it('should get the confirmations', function () {
    expect($this->subject->confirmations())->toBeInt();
    expect($this->subject->confirmations())->toBe(4999999);
});

it('should determine if the transaction is confirmed', function () {
    expect($this->subject->isConfirmed())->toBeTrue();
});

it('should get the ipfs hash', function () {
    expect($this->subject->ipfsHash())->toBeString();
    expect($this->subject->ipfsHash())->toBe('QmXrvSZaDr8vjLUB9b7xz26S3kpk3S3bSc8SUyZmNPvmVo');
});

it('should determine the transaction type', function (string $type) {
    $transaction = Transaction::factory()->{$type}()->create();
    $subject     = new TransactionViewModel($transaction);

    expect($subject->{'is'.ucfirst($type)}())->toBeTrue();

    $subject = new TransactionViewModel(Transaction::factory()->create([
        'type'       => 666,
        'type_group' => 666,
        'asset'      => $transaction->asset,
    ]));

    expect($subject->{'is'.ucfirst($type)}())->toBeFalse();
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
    ['entityRegistration'],
    ['entityResignation'],
    ['entityUpdate'],
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

it('should determine if the transaction is self-receiving', function (string $type) {
    $transaction = Transaction::factory()->{$type}()->create();
    $subject     = new TransactionViewModel($transaction);

    expect($subject->isSelfReceiving())->toBeTrue();

    $subject = new TransactionViewModel(Transaction::factory()->create([
        'type'       => 666,
        'type_group' => 666,
        'asset'      => $transaction->asset,
    ]));

    expect($subject->isSelfReceiving())->toBeFalse();
})->with([
    ['secondSignature'],
    ['delegateRegistration'],
    ['vote'],
    ['unvote'],
    ['voteCombination'],
    ['delegateResignation'],
    ['entityRegistration'],
    ['entityResignation'],
    ['entityUpdate'],
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

it('should determine the state icon', function () {
    expect($this->subject->iconState())->toBeString();
});

it('should determine the type icon', function () {
    expect($this->subject->iconType())->toBeString();
});

it('should determine the type label', function (string $type) {
    $subject = new TransactionViewModel(Transaction::factory()->{$type}()->create());

    expect($subject->typeLabel())->toBeString();
})->with([
    ['secondSignature'],
    ['legacyBusinessRegistration'],
]);

it('should determine legacy types', function (string $type, bool $expectation) {
    $transaction = Transaction::factory()->{$type}()->create();
    $subject     = new TransactionViewModel($transaction);

    expect($subject->isLegacyType())->toBe($expectation);
})->with([
    [
        'secondSignature',
        false,
    ],
    [
        'delegateRegistration',
        false,
    ],
    [
        'legacyBusinessRegistration',
        true,
    ],
    [
        'legacyBusinessResignation',
        true,
    ],
    [
        'legacyBusinessUpdate',
        true,
    ],
    [
        'legacyBridgechainRegistration',
        true,
    ],
    [
        'legacyBridgechainResignation',
        true,
    ],
    [
        'legacyBridgechainUpdate',
        true,
    ],
]);

it('should determine the direction icon', function () {
    expect($this->subject->iconDirection('sender'))->toBeString();
});

it('should fallback to the sender if no recipient exists', function () {
    $this->subject = new TransactionViewModel(Transaction::factory()->create([
        'recipient_id' => null,
    ]));

    expect($this->subject->recipient())->toEqual($this->subject->sender());
});

it('should get the voted delegate', function () {
    Wallet::factory()->create(['public_key' => 'publicKey']);

    $subject = new TransactionViewModel(Transaction::factory()->vote()->create());

    expect($subject->voted())->toBeInstanceOf(WalletViewModel::class);
});

it('should fail to get the voted delegate if the transaction is not an unvote', function () {
    $subject = new TransactionViewModel(Transaction::factory()->unvote()->create());

    expect($subject->voted())->toBeNull();
});

it('should fail to get the voted delegate if the transaction asset is empty', function ($asset) {
    $subject = new TransactionViewModel(Transaction::factory()->vote()->create([
        'asset' => $asset,
    ]));

    expect($subject->voted())->toBeNull();
})->with([[[]], null]);

it('should get the unvoted delegate', function () {
    Wallet::factory()->create(['public_key' => 'publicKey']);

    $subject = new TransactionViewModel(Transaction::factory()->unvote()->create());

    expect($subject->unvoted())->toBeInstanceOf(WalletViewModel::class);
});

it('should fail to get the unvoted delegate if the transaction is not an unvote', function () {
    $subject = new TransactionViewModel(Transaction::factory()->vote()->create());

    expect($subject->unvoted())->toBeNull();
});

it('should fail to get the unvoted delegate if the transaction asset is empty', function ($asset) {
    $subject = new TransactionViewModel(Transaction::factory()
        ->vote()
        ->create(['asset' => $asset]));

    expect($subject->unvoted())->toBeNull();
})->with([[[]], null]);

it('should get the nonce', function () {
    expect($this->subject->nonce())->toBeInt();
});

it('should get the multi signature address', function () {
    expect($this->subject->multiSignatureAddress())->toBeNull();

    $this->subject = new TransactionViewModel(Transaction::factory()
        ->multiSignature()
        ->create(['asset' => null]));

    expect($this->subject->multiSignatureAddress())->toBeNull();

    $this->subject = new TransactionViewModel(Transaction::factory()->multiSignature()->create([
        'asset' => [
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

it('should derive the correct multisignature address', function () {
    expect($this->subject->multiSignatureAddress())->toBeNull();

    $this->subject = new TransactionViewModel(Transaction::factory()
        ->multiSignature()
        ->create(['asset' => null]));

    expect($this->subject->multiSignatureAddress())->toBeNull();

    $this->subject = new TransactionViewModel(Transaction::factory()->multiSignature()->create([
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

    expect($this->subject->multiSignatureAddress())->toBe('DMNBBtYt1teAKxA2BpiTW9PA3gX3Ad5dyk');

    Config::set('explorer.network', 'production');

    $network = NetworkFactory::make(config('explorer.network'));
    NetworkConfiguration::set($network->config());

    expect($this->subject->multiSignatureAddress())->toBe('AXzxJ8Ts3dQ2bvBR1tPE7GUee9iSEJb8HX');

    Config::set('explorer.network', 'development');
});

it('should get the multi signature minimum', function () {
    expect($this->subject->multiSignatureMinimum())->toBeNull();

    $this->subject = new TransactionViewModel(Transaction::factory()
        ->multiSignature()
        ->create(['asset' => null]));

    expect($this->subject->multiSignatureMinimum())->toBeNull();

    $this->subject = new TransactionViewModel(Transaction::factory()->multiSignature()->create([
        'asset' => [
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

    expect($this->subject->multiSignatureMinimum())->toBeInt();
    expect($this->subject->multiSignatureMinimum())->toBe(3);
});

it('should get the multi signature participant count', function () {
    expect($this->subject->multiSignatureParticipantCount())->toBeNull();

    $this->subject = new TransactionViewModel(Transaction::factory()
        ->multiSignature()
        ->create(['asset' => null]));

    expect($this->subject->multiSignatureParticipantCount())->toBeNull();

    $this->subject = new TransactionViewModel(Transaction::factory()->multiSignature()->create([
        'asset' => [
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

    expect($this->subject->multiSignatureParticipantCount())->toBeInt();
    expect($this->subject->multiSignatureParticipantCount())->toBe(5);
});

it('should get the payments', function () {
    $this->subject = new TransactionViewModel(Transaction::factory()
        ->multiPayment()
        ->create(['asset' => null]));

    expect($this->subject->payments())->toBeEmpty();

    $A = Wallet::factory()->create();
    $B = Wallet::factory()->create();
    $C = Wallet::factory()->create();
    $D = Wallet::factory()->create();
    $E = Wallet::factory()->create();

    $model = Transaction::factory()->multiPayment()->create([
        'asset' => [
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

    expect($this->subject->payments()[0])->toEqual(new Payment((int) $model->timestamp, [
        'amount'      => '1000000000',
        'recipientId' => $A->address,
    ]));

    expect($this->subject->payments()[1])->toEqual(new Payment((int) $model->timestamp, [
        'amount'      => '2000000000',
        'recipientId' => $B->address,
    ]));

    expect($this->subject->payments()[2])->toEqual(new Payment((int) $model->timestamp, [
        'amount'      => '3000000000',
        'recipientId' => $C->address,
    ]));

    expect($this->subject->payments()[3])->toEqual(new Payment((int) $model->timestamp, [
        'amount'      => '4000000000',
        'recipientId' => $D->address,
    ]));

    expect($this->subject->payments()[4])->toEqual(new Payment((int) $model->timestamp, [
        'amount'      => '5000000000',
        'recipientId' => $E->address,
    ]));
});

it('should fail to get the payments if the transaction is not a multi payment', function () {
    $this->subject = new TransactionViewModel(Transaction::factory()->transfer()->create());

    expect($this->subject->payments())->toBeEmpty();
});

it('should get the recipients count', function () {
    $this->subject = new TransactionViewModel(Transaction::factory()
        ->multiPayment()
        ->create(['asset' => null]));

    expect($this->subject->recipientsCount())->toBe(0);

    $this->subject = new TransactionViewModel(Transaction::factory()
        ->multiPayment()
        ->create(['asset' => ['payments' => []]]));

    expect($this->subject->recipientsCount())->toBe(0);

    $this->subject = new TransactionViewModel(Transaction::factory()->multiPayment()->create([
        'asset' => [
            'payments' => [
                ['amount' => 10, 'recipientId' => 'ABC'],
                ['amount' => 10, 'recipientId' => 'ABC'],
                ['amount' => 10, 'recipientId' => 'ABC'],
                ['amount' => 10, 'recipientId' => 'ABC'],
                ['amount' => 10, 'recipientId' => 'ABC'],
            ],
        ],
    ]));

    expect($this->subject->recipientsCount())->toBe(5);
});

it('should fail to get the recipients count if the transaction is not a multi payment', function () {
    $this->subject = new TransactionViewModel(Transaction::factory()->transfer()->create());

    expect($this->subject->recipientsCount())->toBe(0);
});

it('should get the participants', function () {
    $this->subject = new TransactionViewModel(Transaction::factory()
        ->multiSignature()
        ->create(['asset' => null]));

    expect($this->subject->participants())->toHaveCount(0);

    $this->subject = new TransactionViewModel(Transaction::factory()->multiSignature()->create([
        'asset' => [
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

    Wallet::factory()->create([
        'address'    => Address::fromPublicKey('02fb3def2593a00c5b84620addf28ff21bac452bd71a37d4d8e24f301683a81b56'),
        'public_key' => '02fb3def2593a00c5b84620addf28ff21bac452bd71a37d4d8e24f301683a81b56',
    ]);

    Wallet::factory()->create([
        'address'    => Address::fromPublicKey('02bc9f661fcc8abca65fe9aff4614036867b7fdcc5730085ccc5cb854664d0194b'),
        'public_key' => '02bc9f661fcc8abca65fe9aff4614036867b7fdcc5730085ccc5cb854664d0194b',
    ]);

    Wallet::factory()->create([
        'address'    => Address::fromPublicKey('03c44c6b6cc9893ae21ca606712fd0f6f03c41ce81c4f6ce5a640f4b0b82ec1ce0'),
        'public_key' => '03c44c6b6cc9893ae21ca606712fd0f6f03c41ce81c4f6ce5a640f4b0b82ec1ce0',
    ]);

    Wallet::factory()->create([
        'address'    => Address::fromPublicKey('020300039e973baf5e46b945777cfae330d6392cdb039b1cebc5c3382d421166c3'),
        'public_key' => '020300039e973baf5e46b945777cfae330d6392cdb039b1cebc5c3382d421166c3',
    ]);

    Wallet::factory()->create([
        'address'    => Address::fromPublicKey('03b050073621b9b5caec9461d44d6bcf21a858c47dd88230ce723e25c1bc75c219'),
        'public_key' => '03b050073621b9b5caec9461d44d6bcf21a858c47dd88230ce723e25c1bc75c219',
    ]);

    expect($this->subject->participants())->toHaveCount(5);
});

it('should fail to get the participants if the transaction is not a multi signature registrations', function () {
    $this->subject = new TransactionViewModel(Transaction::factory()->transfer()->create());

    expect($this->subject->participants())->toBeEmpty();
});

it('should get the multi signature wallet', function () {
    $this->subject = new TransactionViewModel(Transaction::factory()
        ->multiSignature()
        ->create(['asset' => null]));

    expect($this->subject->participants())->toHaveCount(0);

    $this->subject = new TransactionViewModel(Transaction::factory()->multiSignature()->create([
        'asset' => [
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

    Wallet::factory()->create(['address' => 'DMNBBtYt1teAKxA2BpiTW9PA3gX3Ad5dyk']);

    $result = $this->subject->multiSignatureWallet();

    expect($result)->toBeInstanceOf(WalletViewModel::class);
    expect($result->address())->toBe('DMNBBtYt1teAKxA2BpiTW9PA3gX3Ad5dyk');
});

it('should fail to get the multi signature wallet if the transaction is not a multi signature registrations', function () {
    $this->subject = new TransactionViewModel(Transaction::factory()->transfer()->create());

    expect($this->subject->multiSignatureWallet())->toBeEmpty();
});

it('should get the type component', function () {
    $this->subject = new TransactionViewModel(Transaction::factory()->transfer()->create());

    expect($this->subject->typeComponent())->toBe('transaction.details.transfer');

    $this->subject = new TransactionViewModel(Transaction::factory()->timelock()->create());

    expect($this->subject->typeComponent())->toBe('transaction.details.fallback');
});

it('should get the extra component', function () {
    expect($this->subject->extensionComponent())->toBeString();
});

it('should determine if the transaction has extra data', function (bool $outcome, string $type) {
    $subject = new TransactionViewModel(Transaction::factory()->{$type}()->create());

    expect($subject->hasExtraData())->toBe($outcome);
})->with([
    [false, 'transfer'],
    [false, 'secondSignature'],
    [false, 'delegateRegistration'],
    [false, 'vote'],
    [false, 'unvote'],
    [true, 'voteCombination'],
    [true, 'multiSignature'],
    [false, 'ipfs'],
    [false, 'delegateResignation'],
    [true, 'multiPayment'],
    [false, 'timelock'],
    [false, 'timelockClaim'],
    [false, 'timelockRefund'],
    [false, 'entityRegistration'],
    [false, 'entityResignation'],
    [false, 'entityUpdate'],
    [false, 'businessEntityRegistration'],
    [false, 'businessEntityResignation'],
    [false, 'businessEntityUpdate'],
    [false, 'productEntityRegistration'],
    [false, 'productEntityResignation'],
    [false, 'productEntityUpdate'],
    [false, 'pluginEntityRegistration'],
    [false, 'pluginEntityResignation'],
    [false, 'pluginEntityUpdate'],
    [false, 'moduleEntityRegistration'],
    [false, 'moduleEntityResignation'],
    [false, 'moduleEntityUpdate'],
    [false, 'delegateEntityRegistration'],
    [false, 'delegateEntityResignation'],
    [false, 'delegateEntityUpdate'],
    [false, 'legacyBusinessRegistration'],
    [false, 'legacyBusinessResignation'],
    [false, 'legacyBusinessUpdate'],
    [false, 'legacyBridgechainRegistration'],
    [false, 'legacyBridgechainResignation'],
    [false, 'legacyBridgechainUpdate'],
]);

it('should determine if the transaction type is unknown', function () {
    $subject = new TransactionViewModel(Transaction::factory()->create([
        'type'       => 123,
        'type_group' => 456,
    ]));

    expect($subject->isUnknown())->toBeTrue();
});

it('should get the entity type', function () {
    $subject = new TransactionViewModel(Transaction::factory()
        ->productEntityRegistration(['name' => 'John', 'ipfsData' => 'ipfs'])
        ->create());

    expect($subject->entityType())->toBe('product-entity-registration');
});

it('should get the entity name', function () {
    $subject = new TransactionViewModel(Transaction::factory()
        ->productEntityRegistration(['name' => 'John', 'ipfsData' => 'ipfs'])
        ->create());

    expect($subject->entityName())->toBe('John');
});

it('should get the entity name for entity update types', function () {
    $registrationId = Transaction::factory()
        ->productEntityRegistration(['name' => 'John', 'ipfsData' => 'ipfs'])
        ->create()
        ->id;

    $subject = new TransactionViewModel(Transaction::factory()
        ->productEntityUpdate($registrationId)
        ->create());

    expect($subject->entityName())->toBe('John');
});

it('should get the entity category', function () {
    $subject = new TransactionViewModel(Transaction::factory()
        ->productEntityRegistration(['name' => 'John', 'ipfsData' => 'ipfs'])
        ->create());

    expect($subject->entityCategory())->toBeNull();
});

it('should get the entity hash', function () {
    $subject = new TransactionViewModel(Transaction::factory()
        ->productEntityRegistration(['name' => 'John', 'ipfsData' => 'ipfs'])
        ->create());

    expect($subject->entityHash())->toBe('ipfs');
});

it('should get the username if the transaction is not a delegate registration', function () {
    $subject = new TransactionViewModel(Transaction::factory()
        ->delegateRegistration()
        ->create(['asset' => ['delegate' => ['username' => 'john']]]));

    expect($subject->delegateUsername())->toBe('john');
});

it('should fail to get the username if the transaction is not a delegate registration', function () {
    $subject = new TransactionViewModel(Transaction::factory()->transfer()->create());

    expect($subject->delegateUsername())->toBeNull();
});

it('should get the vendor field', function () {
    configureExplorerDatabase();

    $transaction = Transaction::factory()->create([]);

    DB::connection('explorer')->update('UPDATE transactions SET vendor_field = ? WHERE id = ?', ['Hello World', $transaction->id]);

    $this->subject = new TransactionViewModel($transaction->fresh());

    expect($this->subject->vendorField())->toBe('Hello World');
});

it('should fail to get the vendor field if it is empty', function () {
    configureExplorerDatabase();

    $transaction = Transaction::factory()->create(['vendor_field' => null]);

    $this->subject = new TransactionViewModel($transaction->fresh());

    expect($this->subject->vendorField())->toBeNull();
});

it('should fail to get the vendor field if it is empty after reading it', function () {
    configureExplorerDatabase();

    $transaction = Transaction::factory()->create([]);

    DB::connection('explorer')->update('UPDATE transactions SET vendor_field = ? WHERE id = ?', ['', $transaction->id]);

    $this->subject = new TransactionViewModel($transaction->fresh());

    expect($this->subject->vendorField())->toBeNull();
});

it('should determine if the transaction is any kind of registration', function (string $type) {
    $subject = new TransactionViewModel(Transaction::factory()->{$type}()->create());

    expect($subject->isRegistration())->toBeTrue();
})->with([
    ['delegateRegistration'],
    ['businessEntityRegistration'],
    ['productEntityRegistration'],
    ['pluginEntityRegistration'],
    ['moduleEntityRegistration'],
    ['delegateEntityRegistration'],
]);

it('should determine that the transaction is not any kind of registration', function () {
    $subject = new TransactionViewModel(Transaction::factory()->create([
        'type'       => 0,
        'type_group' => 0,
    ]));

    expect($subject->isRegistration())->toBeFalse();
});
