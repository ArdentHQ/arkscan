<?php

declare(strict_types=1);

use App\DTO\Payment;
use App\Facades\Settings;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Blockchain\NetworkFactory;
use App\Services\Cache\CryptoDataCache;
use App\Services\Cache\NetworkCache;
use App\ViewModels\TransactionViewModel;
use App\ViewModels\WalletViewModel;
use ArkEcosystem\Crypto\Configuration\Network as NetworkConfiguration;
use ArkEcosystem\Crypto\Identities\Address;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use function Spatie\Snapshots\assertMatchesSnapshot;

beforeEach(function () {
    $this->block = Block::factory()->create(['height' => 1]);
    Block::factory()->create(['height' => 5000000]);

    (new NetworkCache())->setHeight(fn () => 5000000);

    $this->sender  = Wallet::factory()->create();
    $this->subject = new TransactionViewModel(Transaction::factory()->transfer()->create([
        'block_id'          => $this->block->id,
        'block_height'      => 1,
        'fee'               => 1 * 1e18,
        'amount'            => 2 * 1e18,
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
    expect($this->subject->isReceived('0x6E4C6817a95263B758bbC52e87Ce8e759eD0B084'))->toBeFalse();
});

it('should determine if the transaction is outgoing', function () {
    expect($this->subject->isSent($this->sender->address))->toBeTrue();
    expect($this->subject->isSent('recipient'))->toBeFalse();
});

it('should determine if transfer transaction is sent to self', function () {
    $transaction = new TransactionViewModel(Transaction::factory()
        ->transfer()
        ->create([
            'sender_public_key' => $this->sender->public_key,
            'recipient_id'      => $this->sender->address,
        ]));

    expect($transaction->isSentToSelf($this->sender->address))->toBeTrue();
    expect($transaction->isSentToSelf('recipient'))->toBeFalse();
});

it('should determine if multipayment transaction is sent to self when sender is part of recipients', function () {
    $transaction = new TransactionViewModel(Transaction::factory()
        ->multiPayment()
        ->create([
            'sender_public_key' => $this->sender->public_key,
            'recipient_id'      => $this->sender->address,
            'asset'             => [
                'payments' => [
                    ['recipientId' => $this->sender->address],
                    ['recipientId' => 'recipient'],
                    ['recipientId' => 'recipient-2'],
                ],
            ],
        ]));

    expect($transaction->isSentToSelf($this->sender->address))->toBeFalse();
    expect($transaction->isSentToSelf('recipient-3'))->toBeFalse();
});

it('should determine if multipayment transaction is not sent to self', function () {
    $transaction = new TransactionViewModel(Transaction::factory()
        ->multiPayment()
        ->create([
            'sender_public_key' => $this->sender->public_key,
            'recipient_id'      => $this->sender->address,
            'asset'             => [
                'payments' => [
                    ['recipientId' => 'recipient'],
                    ['recipientId' => 'recipient-2'],
                ],
            ],
        ]));

    expect($transaction->isSentToSelf($this->sender->address))->toBeFalse();
});

it('should not be sent to self if not multipayment or transfer', function () {
    $transaction = new TransactionViewModel(Transaction::factory()
        ->vote()
        ->create());

    expect($transaction->isSentToSelf($this->sender->address))->toBeFalse();
});

it('should get the timestamp', function () {
    expect($this->subject->timestamp())->toBeString();
    expect($this->subject->timestamp())->toBe('19 Oct 2020 04:54:16');
});

it('should get the dateTime', function () {
    expect($this->subject->dateTime())->toBeInstanceOf(Carbon::class);
    expect($this->subject->dateTime()->format('Y-m-d H:i:s'))->toBe('2020-10-19 04:54:16');
});

it('should get the block ID', function () {
    expect($this->subject->blockId())->toBeString();
    expect($this->subject->blockId())->toBe($this->block->id);
});

it('should get the block height', function () {
    expect($this->subject->blockHeight())->toBeInt();
    expect($this->subject->blockHeight())->toBe($this->block->height->toNumber());
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

it('should get the amount including fee', function () {
    expect($this->subject->amountWithFee())->toBeFloat();

    assertMatchesSnapshot($this->subject->amountWithFee());
});

it('should get the amount for multi payments', function () {
    $this->subject = new TransactionViewModel(Transaction::factory()->multiPayment()->create([
        'asset' => [
            'payments' => [
                [
                    'amount'      => 10 * 1e18,
                    'recipientId' => 'A',
                ], [
                    'amount'      => 20 * 1e18,
                    'recipientId' => 'B',
                ], [
                    'amount'      => 30 * 1e18,
                    'recipientId' => 'C',
                ], [
                    'amount'      => 40 * 1e18,
                    'recipientId' => 'D',
                ], [
                    'amount'      => 50 * 1e18,
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

    $this->subject = new TransactionViewModel(Transaction::factory()->multiPayment()->create([
        'sender_public_key' => $sender->public_key,
        'asset'             => [
            'payments' => [
                [
                    'amount'      => 10 * 1e18,
                    'recipientId' => 'A',
                ], [
                    'amount'      => 20 * 1e18,
                    'recipientId' => 'B',
                ], [
                    'amount'      => 30 * 1e18,
                    'recipientId' => 'C',
                ], [
                    'amount'      => 50 * 1e18,
                    'recipientId' => $sender->address,
                ], [
                    'amount'      => 40 * 1e18,
                    'recipientId' => 'D',
                ], [
                    'amount'      => 60 * 1e18,
                    'recipientId' => 'E',
                ],
            ],
        ],
    ]));

    expect($this->subject->amountExcludingItself())->toEqual(160);
});

it('should get the amount in fiat for multi payments excluding payment to the same address', function () {
    $sender = Wallet::factory()->create();

    $this->subject = new TransactionViewModel(Transaction::factory()->multiPayment()->create([
        'sender_public_key' => $sender->public_key,
        'asset'             => [
            'payments' => [
                [
                    'amount'      => 10 * 1e18,
                    'recipientId' => 'A',
                ], [
                    'amount'      => 20 * 1e18,
                    'recipientId' => 'B',
                ], [
                    'amount'      => 30 * 1e18,
                    'recipientId' => 'C',
                ], [
                    'amount'      => 50 * 1e18,
                    'recipientId' => $sender->address,
                ], [
                    'amount'      => 40 * 1e18,
                    'recipientId' => 'D',
                ], [
                    'amount'      => 60 * 1e18,
                    'recipientId' => 'E',
                ],
            ],
        ],
    ]));

    (new CryptoDataCache())->setPrices('USD.week', collect([
        Carbon::parse($this->subject->timestamp())->format('Y-m-d') => 0.2907,
    ]));

    assertMatchesSnapshot($this->subject->amountFiatExcludingItself());
});

it('should get the amount for itself on multi payments', function () {
    $sender = Wallet::factory()->create();

    $this->subject = new TransactionViewModel(Transaction::factory()->multiPayment()->create([
        'sender_public_key' => $sender->public_key,
        'asset'             => [
            'payments' => [
                [
                    'amount'      => 10 * 1e18,
                    'recipientId' => 'A',
                ], [
                    'amount'      => 20 * 1e18,
                    'recipientId' => 'B',
                ], [
                    'amount'      => 30 * 1e18,
                    'recipientId' => 'C',
                ], [
                    'amount'      => 50 * 1e18,
                    'recipientId' => $sender->address,
                ], [
                    'amount'      => 40 * 1e18,
                    'recipientId' => 'D',
                ], [
                    'amount'      => 60 * 1e18,
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
                    'amount'      => 10 * 1e18,
                    'recipientId' => 'A',
                ], [
                    'amount'      => 20 * 1e18,
                    'recipientId' => 'B',
                ], [
                    'amount'      => 30 * 1e18,
                    'recipientId' => 'C',
                ], [
                    'amount'      => 40 * 1e18,
                    'recipientId' => 'D',
                ], [
                    'amount'      => 50 * 1e18,
                    'recipientId' => 'E',
                ], [
                    'amount'      => 50 * 1e18,
                    'recipientId' => 'B',
                ],
            ],
        ],
    ]));

    expect($this->subject->amountReceived('B'))->toBeFloat();

    assertMatchesSnapshot($this->subject->amountReceived('B'));
});

it('should get multi payment amount with fee', function () {
    $this->subject = new TransactionViewModel(Transaction::factory()->multiPayment()->create([
        'asset' => [
            'payments' => [
                [
                    'amount'      => 10 * 1e18,
                    'recipientId' => 'A',
                ], [
                    'amount'      => 20 * 1e18,
                    'recipientId' => 'B',
                ], [
                    'amount'      => 30 * 1e18,
                    'recipientId' => 'C',
                ], [
                    'amount'      => 40 * 1e18,
                    'recipientId' => 'D',
                ], [
                    'amount'      => 50 * 1e18,
                    'recipientId' => 'E',
                ], [
                    'amount'      => 50 * 1e18,
                    'recipientId' => 'B',
                ],
            ],
        ],
    ]));

    expect($this->subject->amountWithFee())->toBeFloat();

    assertMatchesSnapshot($this->subject->amountWithFee());
});

it('should get the amount as fiat', function () {
    $transaction = Transaction::factory()->multiPayment()->create([
        'asset' => [
            'payments' => [
                [
                    'amount'      => 10 * 1e18,
                    'recipientId' => 'A',
                ], [
                    'amount'      => 20 * 1e18,
                    'recipientId' => 'B',
                ], [
                    'amount'      => 30 * 1e18,
                    'recipientId' => 'C',
                ], [
                    'amount'      => 40 * 1e18,
                    'recipientId' => 'D',
                ], [
                    'amount'      => 50 * 1e18,
                    'recipientId' => 'E',
                ],
            ],
        ],
    ]);

    $this->subject = new TransactionViewModel($transaction);

    (new CryptoDataCache())->setPrices('USD.week', collect([
        Carbon::parse($this->subject->timestamp())->format('Y-m-d') => 0.2907,
    ]));

    assertMatchesSnapshot($this->subject->amountFiat());
});

it('should get the specific multi payment fiat amount for a wallet recipient', function () {
    $transaction = Transaction::factory()->multiPayment()->create([
        'asset' => [
            'payments' => [
                [
                    'amount'      => 10 * 1e18,
                    'recipientId' => 'A',
                ], [
                    'amount'      => 20 * 1e18,
                    'recipientId' => 'B',
                ], [
                    'amount'      => 30 * 1e18,
                    'recipientId' => 'C',
                ], [
                    'amount'      => 40 * 1e18,
                    'recipientId' => 'D',
                ], [
                    'amount'      => 50 * 1e18,
                    'recipientId' => 'E',
                ], [
                    'amount'      => 50 * 1e18,
                    'recipientId' => 'B',
                ],
            ],
        ],
    ]);

    $this->subject = new TransactionViewModel($transaction);

    (new CryptoDataCache())->setPrices('USD.week', collect([
        Carbon::parse($this->subject->timestamp())->format('Y-m-d') => 0.2907,
    ]));

    assertMatchesSnapshot($this->subject->amountReceivedFiat('B'));
});

it('should handle 256 recipients in a multipayment', function () {
    $addresses = collect(array_fill(0, 256, null))->keys();

    $addresses->each(fn ($address) => Wallet::factory()->create(['address' => 'address-'.$address]));

    $this->subject = new TransactionViewModel(
        Transaction::factory()->multiPayment()->create([
            'asset' => [
                'payments' => $addresses
                    ->map(fn ($value) => ([
                        'amount'      => (256 - $value) * 1e18,
                        'recipientId' => 'address-'.$value,
                    ]))
                    ->toArray(),
            ],
        ])
    );

    $payments = $this->subject->payments(true);

    expect($payments)->toHaveCount(256);
    expect($payments[0]->address())->toBe('address-0');
    expect($payments[255]->address())->toBe('address-255');
});

it('should get the total as fiat', function () {
    (new CryptoDataCache())->setPrices('USD.week', collect([
        Carbon::parse($this->subject->timestamp())->format('Y-m-d') => 0.2907,
    ]));

    expect($this->subject->totalFiat())->toBe('$0.87');
});

it('should get small total values as fiat', function () {
    (new CryptoDataCache())->setPrices('USD.week', collect([
        Carbon::parse($this->subject->timestamp())->format('Y-m-d') => 0.2907,
    ]));

    expect($this->subject->totalFiat(true))->toBe('$0.8721');
});

it('should get the total as cryptocurrency', function () {
    Settings::shouldReceive('currency')
        ->andReturn('BTC');

    (new CryptoDataCache())->setPrices('BTC.week', collect([
        Carbon::parse($this->subject->timestamp())->format('Y-m-d') => 0.000001,
    ]));

    expect($this->subject->totalFiat())->toBe('0.000003 BTC');
});

it('should get the confirmations', function () {
    expect($this->subject->confirmations())->toBeInt();
    expect($this->subject->confirmations())->toBe(4999999);
});

it('should determine if the transaction is confirmed', function () {
    expect($this->subject->isConfirmed())->toBeTrue();
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
    ['validatorRegistration'],
    ['vote'],
    ['unvote'],
    ['voteCombination'],
    ['multiSignature'],
    ['validatorResignation'],
    ['multiPayment'],
    ['usernameRegistration'],
    ['usernameResignation'],
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
    ['validatorRegistration'],
    ['vote'],
    ['unvote'],
    ['voteCombination'],
    ['validatorResignation'],
    ['usernameRegistration'],
    ['usernameResignation'],
]);

it('should fallback to the sender if no recipient exists', function () {
    $this->subject = new TransactionViewModel(Transaction::factory()->create([
        'recipient_id' => null,
    ]));

    expect($this->subject->recipient())->toEqual($this->subject->sender());
});

it('should get the voted validator', function () {
    Wallet::factory()->create(['public_key' => 'publicKey']);

    $subject = new TransactionViewModel(Transaction::factory()->vote()->create());

    expect($subject->voted())->toBeInstanceOf(WalletViewModel::class);
});

it('should fail to get the voted validator if the transaction is not an unvote', function () {
    $subject = new TransactionViewModel(Transaction::factory()->unvote()->create());

    expect($subject->voted())->toBeNull();
});

it('should fail to get the voted validator if the transaction asset is empty', function ($asset) {
    $subject = new TransactionViewModel(Transaction::factory()->vote()->create([
        'asset' => $asset,
    ]));

    expect($subject->voted())->toBeNull();
})->with([[[]], null]);

it('should get the unvoted validator', function () {
    Wallet::factory()->create(['public_key' => 'publicKey']);

    $subject = new TransactionViewModel(Transaction::factory()->unvote()->create());

    expect($subject->unvoted())->toBeInstanceOf(WalletViewModel::class);
});

it('should fail to get the unvoted validator if the transaction is not an unvote', function () {
    $subject = new TransactionViewModel(Transaction::factory()->vote()->create());

    expect($subject->unvoted())->toBeNull();
});

it('should fail to get the unvoted validator if the transaction asset is empty', function ($asset) {
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

    expect($this->subject->multiSignatureAddress())->toBe('0x8246206ef20b95D0a3C16704Ee971a605cb7E33E');

    Config::set('arkscan.network', 'production');

    $network = NetworkFactory::make(config('arkscan.network'));
    NetworkConfiguration::set($network->config());

    expect($this->subject->multiSignatureAddress())->toBe('0x8246206ef20b95D0a3C16704Ee971a605cb7E33E');

    Config::set('arkscan.network', 'development');
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
                    'amount'      => 10 * 1e18,
                    'recipientId' => $A->address,
                ], [
                    'amount'      => 20 * 1e18,
                    'recipientId' => $B->address,
                ], [
                    'amount'      => 30 * 1e18,
                    'recipientId' => $C->address,
                ], [
                    'amount'      => 40 * 1e18,
                    'recipientId' => $D->address,
                ], [
                    'amount'      => 50 * 1e18,
                    'recipientId' => $E->address,
                ],
            ],
        ],
    ]);

    $this->subject = new TransactionViewModel($model);

    $payments = $this->subject->payments();
    expect($payments[0])->toEqual(new Payment((int) $model->timestamp, [
        'amount'      => 10 * 1e18,
        'recipientId' => $A->address,
    ]));

    expect($payments[1])->toEqual(new Payment((int) $model->timestamp, [
        'amount'      => 20 * 1e18,
        'recipientId' => $B->address,
    ]));

    expect($payments[2])->toEqual(new Payment((int) $model->timestamp, [
        'amount'      => 30 * 1e18,
        'recipientId' => $C->address,
    ]));

    expect($payments[3])->toEqual(new Payment((int) $model->timestamp, [
        'amount'      => 40 * 1e18,
        'recipientId' => $D->address,
    ]));

    expect($payments[4])->toEqual(new Payment((int) $model->timestamp, [
        'amount'      => 50 * 1e18,
        'recipientId' => $E->address,
    ]));
});

it('should get the payments in descending order', function () {
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
                    'amount'      => 10 * 1e18,
                    'recipientId' => $A->address,
                ], [
                    'amount'      => 20 * 1e18,
                    'recipientId' => $B->address,
                ], [
                    'amount'      => 30 * 1e18,
                    'recipientId' => $C->address,
                ], [
                    'amount'      => 40 * 1e18,
                    'recipientId' => $D->address,
                ], [
                    'amount'      => 50 * 1e18,
                    'recipientId' => $E->address,
                ],
            ],
        ],
    ]);

    $this->subject = new TransactionViewModel($model);

    $payments = array_values($this->subject->payments(true));
    expect($payments[0])->toEqual(new Payment((int) $model->timestamp, [
        'amount'      => 50 * 1e18,
        'recipientId' => $E->address,
    ]));

    expect($payments[1])->toEqual(new Payment((int) $model->timestamp, [
        'amount'      => 40 * 1e18,
        'recipientId' => $D->address,
    ]));

    expect($payments[2])->toEqual(new Payment((int) $model->timestamp, [
        'amount'      => 30 * 1e18,
        'recipientId' => $C->address,
    ]));

    expect($payments[3])->toEqual(new Payment((int) $model->timestamp, [
        'amount'      => 20 * 1e18,
        'recipientId' => $B->address,
    ]));

    expect($payments[4])->toEqual(new Payment((int) $model->timestamp, [
        'amount'      => 10 * 1e18,
        'recipientId' => $A->address,
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

    Wallet::factory()->create(['address' => '0x8246206ef20b95D0a3C16704Ee971a605cb7E33E']);

    $result = $this->subject->multiSignatureWallet();

    expect($result)->toBeInstanceOf(WalletViewModel::class);
    expect($result->address())->toBe('0x8246206ef20b95D0a3C16704Ee971a605cb7E33E');
});

it('should fail to get the multi signature wallet if the transaction is not a multi signature registrations', function () {
    $this->subject = new TransactionViewModel(Transaction::factory()->transfer()->create());

    expect($this->subject->multiSignatureWallet())->toBeEmpty();
});

it('should determine if the transaction type is unknown', function () {
    $subject = new TransactionViewModel(Transaction::factory()->create([
        'type'       => 123,
        'type_group' => 456,
    ]));

    expect($subject->isUnknown())->toBeTrue();
});

it('should get the username if the transaction is not a validator registration', function () {
    $subject = new TransactionViewModel(Transaction::factory()
        ->validatorRegistration()
        ->create([
            'asset' => [
                'username' => 'john',
            ],
        ]));

    expect($subject->username())->toBe('john');
});

it('should return null for username if not specified', function () {
    $subject = new TransactionViewModel(Transaction::factory()->transfer()->create());

    expect($subject->username())->toBeNull();
});

it('should get the vendor field', function () {
    $transaction = Transaction::factory()->create([]);

    DB::connection('explorer')->update('UPDATE transactions SET vendor_field = ? WHERE id = ?', ['Hello World', $transaction->id]);

    $this->subject = new TransactionViewModel($transaction->fresh());

    expect($this->subject->vendorField())->toBe('Hello World');
});

it('should fail to get the vendor field if it is empty', function () {
    $transaction = Transaction::factory()->create(['vendor_field' => null]);

    $this->subject = new TransactionViewModel($transaction->fresh());

    expect($this->subject->vendorField())->toBeNull();
});

it('should fail to get the vendor field if it is empty after reading it', function () {
    $transaction = Transaction::factory()->create([]);

    DB::connection('explorer')->update('UPDATE transactions SET vendor_field = ? WHERE id = ?', ['', $transaction->id]);

    $this->subject = new TransactionViewModel($transaction->fresh());

    expect($this->subject->vendorField())->toBeNull();
});

it('should get the address of legacy multi signature transactions', function () {
    $this->subject = new TransactionViewModel(Transaction::factory()->multiSignature()->create([
        'sender_public_key' => $this->sender->public_key,
        'asset'             => [
            'multiSignatureLegacy' => [
                'min'       => 3,
                'lifetime'  => 24,
                'keysgroup' => [
                    '+02fb3def2593a00c5b84620addf28ff21bac452bd71a37d4d8e24f301683a81b56',
                    '+02bc9f661fcc8abca65fe9aff4614036867b7fdcc5730085ccc5cb854664d0194b',
                    '+03c44c6b6cc9893ae21ca606712fd0f6f03c41ce81c4f6ce5a640f4b0b82ec1ce0',
                    '+020300039e973baf5e46b945777cfae330d6392cdb039b1cebc5c3382d421166c3',
                    '+03b050073621b9b5caec9461d44d6bcf21a858c47dd88230ce723e25c1bc75c219',
                ],
            ],
        ],
    ]));

    expect($this->subject->multiSignatureAddress())->toBe($this->sender->address);
});

it('should get the participant count for legacy multi signature transactions', function () {
    $this->subject = new TransactionViewModel(Transaction::factory()->multiSignature()->create([
        'asset' => [
            'multiSignatureLegacy' => [
                'min'       => 3,
                'lifetime'  => 24,
                'keysgroup' => [
                    '+02fb3def2593a00c5b84620addf28ff21bac452bd71a37d4d8e24f301683a81b56',
                    '+02bc9f661fcc8abca65fe9aff4614036867b7fdcc5730085ccc5cb854664d0194b',
                    '+03c44c6b6cc9893ae21ca606712fd0f6f03c41ce81c4f6ce5a640f4b0b82ec1ce0',
                    '+020300039e973baf5e46b945777cfae330d6392cdb039b1cebc5c3382d421166c3',
                    '+03b050073621b9b5caec9461d44d6bcf21a858c47dd88230ce723e25c1bc75c219',
                ],
            ],
        ],
    ]));

    expect($this->subject->multiSignatureParticipantCount())->toBe(5);
});

it('should get the participants for legacy multi signature transactions', function () {
    $this->subject = new TransactionViewModel(Transaction::factory()->multiSignature()->create([
        'asset' => [
            'multiSignatureLegacy' => [
                'min'       => 3,
                'lifetime'  => 24,
                'keysgroup' => [
                    '+02fb3def2593a00c5b84620addf28ff21bac452bd71a37d4d8e24f301683a81b56',
                    '+02bc9f661fcc8abca65fe9aff4614036867b7fdcc5730085ccc5cb854664d0194b',
                    '+03c44c6b6cc9893ae21ca606712fd0f6f03c41ce81c4f6ce5a640f4b0b82ec1ce0',
                    '+020300039e973baf5e46b945777cfae330d6392cdb039b1cebc5c3382d421166c3',
                    '+03b050073621b9b5caec9461d44d6bcf21a858c47dd88230ce723e25c1bc75c219',
                ],
            ],
        ],
    ]));

    expect($this->subject->participants())->toHaveCount(5);
});

it('should get the minimum for legacy multi signature transactions', function () {
    $this->subject = new TransactionViewModel(Transaction::factory()->multiSignature()->create([
        'asset' => [
            'multiSignatureLegacy' => [
                'min'       => 3,
                'lifetime'  => 24,
                'keysgroup' => [
                    '+02fb3def2593a00c5b84620addf28ff21bac452bd71a37d4d8e24f301683a81b56',
                    '+02bc9f661fcc8abca65fe9aff4614036867b7fdcc5730085ccc5cb854664d0194b',
                    '+03c44c6b6cc9893ae21ca606712fd0f6f03c41ce81c4f6ce5a640f4b0b82ec1ce0',
                    '+020300039e973baf5e46b945777cfae330d6392cdb039b1cebc5c3382d421166c3',
                    '+03b050073621b9b5caec9461d44d6bcf21a858c47dd88230ce723e25c1bc75c219',
                ],
            ],
        ],
    ]));

    expect($this->subject->multiSignatureMinimum())->toBe(3);
});

it('should determine a non-legacy transaction', function ($transaction) {
    $transaction = new TransactionViewModel(Transaction::factory()->{$transaction}()->create());

    expect($transaction->isLegacy())->toBeFalse();
})->with([
    'transfer',
    'validatorRegistration',
    'validatorResignation',
    'multisignature',
    'multiPayment',
    'voteCombination',
    'vote',
    'unvote',
    'usernameRegistration',
    'usernameResignation',
]);

it('should determine a legacy transaction', function () {
    $transaction = new TransactionViewModel(Transaction::factory()->create([
        'type'       => '12345',
        'type_group' => '55555',
    ]));

    expect($transaction->isLegacy())->toBeTrue();
});
