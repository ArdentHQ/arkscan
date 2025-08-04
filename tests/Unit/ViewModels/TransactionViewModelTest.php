<?php

declare(strict_types=1);

use App\Facades\Settings;
use App\Models\Block;
use App\Models\Receipt;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\Cache\CryptoDataCache;
use App\Services\Cache\NetworkCache;
use App\ViewModels\TransactionViewModel;
use App\ViewModels\WalletViewModel;
use Carbon\Carbon;
use Illuminate\Support\Str;
use function Spatie\Snapshots\assertMatchesSnapshot;

beforeEach(function () {
    $this->block = Block::factory()->create(['number' => 1]);
    Block::factory()->create(['number' => 5000000]);

    (new NetworkCache())->setHeight(fn () => 5000000);

    $this->sender  = Wallet::factory()->create();
    $this->subject = new TransactionViewModel(Transaction::factory()->create([
        'block_hash'               => $this->block->hash,
        'block_number'             => 1,
        'gas_price'                => 1,
        'gas'                      => 21000,
        'value'                    => 2 * 1e18,
        'sender_public_key'        => $this->sender->public_key,
        'to'                       => Wallet::factory()->create(['address' => 'recipient'])->address,
    ])->fresh());
});

it('should get the url', function () {
    expect($this->subject->url())->toBeString();
    expect($this->subject->url())->toBe(route('transaction', $this->subject->hash()));
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
        ->create([
            'sender_public_key' => $this->sender->public_key,
            'to'                => $this->sender->address,
        ]));

    expect($transaction->isSentToSelf($this->sender->address))->toBeTrue();
    expect($transaction->isSentToSelf('recipient'))->toBeFalse();
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
    expect($this->subject->blockHash())->toBeString();
    expect($this->subject->blockHash())->toBe($this->block->hash);
});

it('should get the block height', function () {
    expect($this->subject->blockHeight())->toBeInt();
    expect($this->subject->blockHeight())->toBe($this->block->number->toNumber());
});

it('should get the fee', function () {
    expect($this->subject->fee())->toBeFloat();

    assertMatchesSnapshot($this->subject->fee());
});

it('should get the amount', function () {
    expect($this->subject->amount())->toBeFloat();

    assertMatchesSnapshot($this->subject->amount());
});

it('should get the amount for itself', function () {
    $transaction = Transaction::factory()
        ->multiPayment([$this->sender->address], [BigNumber::new(30 * 1e18)])
        ->create([
            'sender_public_key' => $this->sender->public_key,
        ]);

    $viewModel = new TransactionViewModel($transaction);

    expect($viewModel->amountForItself())->toBe(30.0);
});

it('should return zero for the amount for itself when not multipayment', function () {
    expect($this->subject->amountForItself())->toBe(0.0);
});

it('should get the amount excluding itself', function () {
    $transaction = Transaction::factory()
        ->multiPayment([
            $this->sender->address,
            Wallet::factory()->create()->address,
        ], [
            BigNumber::new(30 * 1e18),
            BigNumber::new(30 * 1e18),
        ])
        ->create([
            'sender_public_key' => $this->sender->public_key,
            'value'             => BigNumber::new(60 * 1e18),
        ]);

    $viewModel = new TransactionViewModel($transaction);

    expect($viewModel->amount())->toBe(60.0);
    expect($viewModel->amountReceived($this->sender->address))->toBe(30.0);
    expect($viewModel->amountExcludingItself())->toBe(30.0);
});

it('should return zero for the amount excluding itself when not multipayment', function () {
    expect($this->subject->amountExcludingItself())->toBe(0.0);
});

it('should get the amount received for transfer transactions', function () {
    expect($this->subject->amountReceived('recipient'))->toBeFloat();

    assertMatchesSnapshot($this->subject->amountReceived('recipient'));
});

it('should get the amount including fee', function () {
    expect($this->subject->amountWithFee())->toBeFloat();

    assertMatchesSnapshot($this->subject->amountWithFee());
});

it('should get the amount as fiat', function () {
    (new CryptoDataCache())->setPrices('USD.week', collect([
        Carbon::parse($this->subject->timestamp())->format('Y-m-d') => 0.2907,
    ]));

    expect($this->subject->amountFiat())->toBe('$0.58');
});

it('should get the amount excluding self as fiat', function () {
    (new CryptoDataCache())->setPrices('USD.week', collect([
        Carbon::parse($this->subject->timestamp())->format('Y-m-d') => 0.2907,
    ]));

    $transaction = Transaction::factory()
        ->multiPayment([
            $this->sender->address,
            Wallet::factory()->create()->address,
        ], [
            BigNumber::new(30 * 1e18),
            BigNumber::new(30 * 1e18),
        ])
        ->create([
            'sender_public_key' => $this->sender->public_key,
            'value'             => BigNumber::new(60 * 1e18),
        ]);

    $viewModel = new TransactionViewModel($transaction);

    expect($viewModel->amountFiatExcludingItself())->toBe('$'.number_format(30 * 0.2907, 2));
});

it('should get the total as fiat', function () {
    (new CryptoDataCache())->setPrices('USD.week', collect([
        Carbon::parse($this->subject->timestamp())->format('Y-m-d') => 0.2907,
    ]));

    expect($this->subject->totalFiat())->toBe('$0.58');
});

it('should get small total values as fiat', function () {
    (new CryptoDataCache())->setPrices('USD.week', collect([
        Carbon::parse($this->subject->timestamp())->format('Y-m-d') => 0.2907,
    ]));

    expect($this->subject->totalFiat(true))->toBe('$0.5814');
});

it('should get the total as cryptocurrency', function () {
    Settings::shouldReceive('currency')
        ->andReturn('BTC');

    (new CryptoDataCache())->setPrices('BTC.week', collect([
        Carbon::parse($this->subject->timestamp())->format('Y-m-d') => 0.000001,
    ]));

    expect($this->subject->totalFiat())->toBe('0.000002 BTC');
});

it('should get the confirmations', function () {
    expect($this->subject->confirmations())->toBeInt();
    expect($this->subject->confirmations())->toBe(4999999);
});

it('should determine if the transaction is confirmed', function () {
    expect($this->subject->isConfirmed())->toBeTrue();
});

it('should determine the transaction type', function (string $type, ?string $walletArgument = null) {
    $wallet    = Wallet::factory()->activeValidator()->create();
    $arguments = [];
    if ($walletArgument) {
        $arguments = [$wallet->{$walletArgument}];
    }

    $transaction = Transaction::factory()->{$type}(...$arguments)->create();
    $subject     = new TransactionViewModel($transaction);

    expect($subject->{'is'.ucfirst($type)}())->toBeTrue();

    $subject = new TransactionViewModel(Transaction::factory()->withPayload('123456')->create());

    expect($subject->{'is'.ucfirst($type)}())->toBeFalse();
})->with([
    'transfer'              => ['transfer', null],
    'validatorRegistration' => ['validatorRegistration', 'public_key'],
    'validatorResignation'  => ['validatorResignation', null],
    'validatorUpdate'       => ['validatorUpdate', 'public_key'],
    'vote'                  => ['vote', 'address'],
    'unvote'                => ['unvote', null],
]);

it('should determine if the transaction is self-receiving', function (string $type) {
    $wallet = Wallet::factory()->activeValidator()->create();

    $arguments = [];
    if ($type === 'vote') {
        $arguments = [$wallet->address];
    } elseif (in_array($type, ['validatorRegistration', 'validatorUpdate'], true)) {
        $arguments = [$wallet->public_key];
    }

    $transaction = Transaction::factory()->{$type}(...$arguments)->create();
    $subject     = new TransactionViewModel($transaction);

    expect($subject->isSelfReceiving())->toBeTrue();

    $subject = new TransactionViewModel(Transaction::factory()->create());

    expect($subject->isSelfReceiving())->toBeFalse();
})->with([
    ['validatorRegistration'],
    ['validatorUpdate'],
    ['vote'],
    ['unvote'],
    ['validatorResignation'],
]);

it('should fallback to the sender if no recipient address exists', function () {
    $this->subject = new TransactionViewModel(Transaction::factory()->create([
        'to' => null,
    ]));

    expect($this->subject->recipient())->toEqual($this->subject->sender());
});

it('should fallback to receipt deployed contract address if set', function () {
    $wallet = Wallet::factory()->create(['address' => 'deployedContractAddress']);

    $receipt = Receipt::factory()
        ->state(['contract_address' => $wallet->address]);

    $this->subject = new TransactionViewModel(Transaction::factory()->has($receipt)->create([
        'to' => null,
    ]));

    expect($this->subject->recipient()->address())->toBe('deployedContractAddress');
});

it('should get the voted validator', function () {
    Wallet::factory()->create(['public_key' => 'publicKey']);

    $validator = Wallet::factory()->activeValidator()->create();

    $subject = new TransactionViewModel(Transaction::factory()->vote($validator->address)->create());

    expect($subject->voted())->toBeInstanceOf(WalletViewModel::class);
});

it('should fail to get the voted validator for unknown wallet', function () {
    Wallet::factory()->create(['public_key' => 'publicKey']);

    $transaction = Transaction::factory()
        ->vote('0x'.str_repeat('0', 64))
        ->create();

    $subject = new TransactionViewModel($transaction);

    expect($subject->voted())->toBeNull();
});

it('should fail to get the voted validator if the transaction is not an unvote', function () {
    $subject = new TransactionViewModel(Transaction::factory()->unvote()->create());

    expect($subject->voted())->toBeNull();
});

it('should get the nonce', function () {
    expect($this->subject->nonce())->toBeInt();
});

it('should get the gas', function () {
    expect($this->subject->gas())->toBeFloat();
});

describe('HasPayload trait', function () {
    it('should determine if a transaction has a payload', function () {
        $transaction = new TransactionViewModel(Transaction::factory()
            ->withPayload('1234567890')
            ->create());

        expect($transaction->hasPayload())->toBeTrue();
    });

    it('should get raw payload', function () {
        $transaction = new TransactionViewModel(Transaction::factory()
            ->withPayload('1234567890')
            ->create());

        expect($transaction->rawPayload())->toBe('1234567890');
    });

    it('should get utf-8 formatted payload', function () {
        $transaction = new TransactionViewModel(Transaction::factory()
            ->withPayload('74657374696e67')
            ->create());

        expect($transaction->utf8Payload())->toBe('testing');
    });

    it('should get null for utf-8 formatted payload if raw payload is null', function () {
        $transaction = new TransactionViewModel(Transaction::factory()
            ->withPayload('')
            ->create());

        expect($transaction->utf8Payload())->toBeNull();
    });

    it('should get formatted payload', function () {
        $transaction = new TransactionViewModel(Transaction::factory()
            ->withPayload('6dd7d8ea00000000000000000000000044083669cf29374d548b71c558ebd1e2f5dcc4de00000000000000000000000044083669cf29374d548b71c558ebd1e2f5dcc4de')
            ->create());

        expect($transaction->formattedPayload())->toBe('Function: vote(address)

MethodID: 0x6dd7d8ea
[0]: 0x44083669cf29374D548b71c558EBD1e2F5DCC4De');
    });

    it('should fail to get formatted payload if no method data', function () {
        $transaction = new TransactionViewModel(Transaction::factory()
            ->withPayload('')
            ->create());

        expect($transaction->formattedPayload())->toBeNull();
    });

    it('should get formatted payload without arguments', function () {
        $transaction = new TransactionViewModel(Transaction::factory()
            ->withPayload('6dd7d8ea')
            ->create());

        expect($transaction->formattedPayload())->toBe('Function: vote(address)

MethodID: 0x6dd7d8ea');
    });

    it('should get formatted payload without a valid function name', function () {
        $transaction = new TransactionViewModel(Transaction::factory()
            ->withPayload('12341234')
            ->create());

        expect($transaction->formattedPayload())->toBe('MethodID: 0x12341234');
    });

    it('should get formatted multi payment receipts', function () {
        $transaction = new TransactionViewModel(Transaction::factory()
        ->multiPayment([
            '0xb693449AdDa7EFc015D87944EAE8b7C37EB1690A',
            '0xb693449AdDa7EFc015D87944EAE8b7C37EB1690A',
            '0xEd0C906b8fcCDe71A19322DFfe929c6e04460cFF',
        ], [
            BigNumber::new(100000000),
            BigNumber::new(200000000),
            BigNumber::new(1234567),
        ])->create());

        expect($transaction->multiPaymentRecipients())->toEqual([
            '0' => [
                'address' => '0xb693449AdDa7EFc015D87944EAE8b7C37EB1690A',
                'amount'  => '1.0E-10',
            ],
            '1' => [
                'address' => '0xb693449AdDa7EFc015D87944EAE8b7C37EB1690A',
                'amount'  => '2.0E-10',
            ],
            '2' => [
                'address' => '0xEd0C906b8fcCDe71A19322DFfe929c6e04460cFF',
                'amount'  => '1.234567E-12',
            ],
        ]);
    });

    it('should fail to get formatted multi payment recipients if invalid a multi payment', function () {
        $transaction = new TransactionViewModel(Transaction::factory()
            ->withPayload('123456')
            ->create());

        expect(function () use ($transaction) {
            $transaction->multiPaymentRecipients();
        })->toThrow(Exception::class, 'This transaction is not a multi-payment.');
    });
});

it('should calculate fee with receipt', function () {
    $transaction = Transaction::factory()->create([
        'gas_price' => 54 * 1e9,
    ]);

    Receipt::factory()->create([
        'transaction_hash' => $transaction->hash,
        'gas_used'         => 21000,
    ]);

    $viewModel = new TransactionViewModel($transaction->fresh());

    expect($viewModel->fee())->toEqual(0.001134);
});

it('should return gas price if no receipt', function () {
    $transaction = Transaction::factory()->create([
        'gas_price' => 54 * 1e9,
    ]);

    $viewModel = new TransactionViewModel($transaction->fresh());

    expect($viewModel->fee())->toEqual(0.000000054);
});

it('should should determine if transaction failed', function () {
    $transaction = Transaction::factory()->create();

    Receipt::factory()->create([
        'transaction_hash' => $transaction->hash,
        'status'           => false,
    ]);

    $viewModel = new TransactionViewModel($transaction->fresh());

    expect($viewModel->hasFailedStatus())->toBeTrue();
});

it('should should determine if transaction failed if no receipt', function () {
    $transaction = Transaction::factory()->create();

    $viewModel = new TransactionViewModel($transaction->fresh());

    expect($viewModel->hasFailedStatus())->toBeTrue();
});

it('should should determine transaction has not failed', function () {
    $transaction = Transaction::factory()->create();

    Receipt::factory()->create([
        'transaction_hash' => $transaction->hash,
        'status'           => true,
    ]);

    $viewModel = new TransactionViewModel($transaction->fresh());

    expect($viewModel->hasFailedStatus())->toBeFalse();
});

it('should get the gas used', function () {
    $transaction = Transaction::factory()->create();

    Receipt::factory()->create([
        'transaction_hash' => $transaction->hash,
        'gas_used'         => 8,
    ]);

    $viewModel = new TransactionViewModel($transaction->fresh());

    expect($viewModel->gasUsed())->toEqual(8);
});

it('should get the gas used if no receipt', function () {
    $transaction = Transaction::factory()->create();

    $viewModel = new TransactionViewModel($transaction->fresh());

    expect($viewModel->gasUsed())->toEqual(0);
});

it('should get the username if set', function () {
    $transaction = Transaction::factory()
        ->usernameRegistration()
        ->withPayload('36a941340000000000000000000000000000000000000000000000000000000000000020000000000000000000000000000000000000000000000000000000000000000e7068705f73646b5f746573746572000000000000000000000000000000000000')
        ->create();

    $viewModel = new TransactionViewModel($transaction->fresh());

    expect($viewModel->username())->toBe('php_sdk_tester');
});

it('should get null username if not set', function () {
    $transaction = Transaction::factory()
        ->create();

    $viewModel = new TransactionViewModel($transaction->fresh());

    expect($viewModel->username())->toBeNull();
});

it('has a validator public key', function () {
    $transaction = Transaction::factory()
        ->validatorRegistration('C5a19e23E99bdFb7aae4301A009763AdC01c1b5B')
        ->create();

    $viewModel = new TransactionViewModel($transaction->fresh());

    expect($viewModel->validatorPublicKey())->toBe('000000000000000000000000c5a19e23e99bdfb7aae4301a009763adc01c1b5b');
});

it('has a validator public key for validator update', function () {
    $transaction = Transaction::factory()
        ->validatorUpdate('C5a19e23E99bdFb7aae4301A009763AdC01c1b5B')
        ->create();

    $viewModel = new TransactionViewModel($transaction->fresh());

    expect($viewModel->validatorPublicKey())->toBe('000000000000000000000000c5a19e23e99bdfb7aae4301a009763adc01c1b5b');
});

it('does not have a validator public key if is not validator registration', function () {
    $transaction = Transaction::factory()
        ->transfer()
        ->create();

    $viewModel = new TransactionViewModel($transaction->fresh());

    expect($viewModel->validatorPublicKey())->toBeNull();
});

it('should determine if is certain transaction type', function (string $type, array $params = []) {
    $transaction = Transaction::factory()
        ->{Str::camel($type)}(...$params)
        ->create();

    $viewModel = new TransactionViewModel($transaction);

    expect($viewModel->{'is'.Str::camel($type)}())->toBeTrue();
})->with([
    ['transfer'],
    ['tokenTransfer', ['0x0', 0]],
    ['vote', ['0x0']],
    ['unvote'],
    ['validatorRegistration'],
    ['validatorResignation'],
    ['validatorUpdate'],
    ['usernameRegistration'],
    ['usernameResignation'],
    ['contractDeployment'],
]);

it('should get the correct amount for a given wallet address in multipayment', function () {
    $walletAddress1 = '0xb693449AdDa7EFc015D87944EAE8b7C37EB1690A';
    $walletAddress2 = '0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B';

    $transaction = Transaction::factory()->multiPayment(
        [$walletAddress1, $walletAddress2],
        [BigNumber::new(1 * 1e18), BigNumber::new(2 * 1e18)]
    )->create();

    $viewModel = new TransactionViewModel($transaction);

    expect($viewModel->amount())->toEqual(3.0);
    expect($viewModel->amountReceived($walletAddress1))->toEqual(1.0);
    expect($viewModel->amountReceived($walletAddress2))->toEqual(2.0);
});

it('should get the correct amount for many wallet addresses in multipayment', function () {
    $wallets = [
        '0xb693449AdDa7EFc015D87944EAE8b7C37EB1690A' => BigNumber::new(10000 * 1e18),
        '0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B' => BigNumber::new(10000 * 1e18),
        '0xEd0C906b8fcCDe71A19322DFfe929c6e04460cFF' => BigNumber::new(10000 * 1e18),
        '0x1234567890abcdef1234567890abcdef12345678' => BigNumber::new(10000 * 1e18),
        '0xabcdef1234567890abcdef1234567890abcdef12' => BigNumber::new(10000 * 1e18),
        '0x7890abcdef1234567890abcdef1234567890abcd' => BigNumber::new(10000 * 1e18),
    ];

    $transaction = Transaction::factory()->multiPayment(
        array_keys($wallets),
        array_values($wallets),
    )->create([
        'value' => BigNumber::new(60000 * 1e18),
    ]);

    $viewModel = new TransactionViewModel($transaction);

    foreach ($wallets as $walletAddress => $amount) {
        expect($viewModel->amount())->toEqual(60000);
        expect($viewModel->amountReceived($walletAddress))->toEqual($amount->toFloat());
    }
});

it('should get a corresponding validator registration', function () {
    $validatorRegistration = Transaction::factory()
        ->validatorRegistration('C5a19e23E99bdFb7aae4301A009763AdC01c1b5B')
        ->create();

    $validatorResignationViewModel = new TransactionViewModel(
        Transaction::factory()
            ->validatorResignation()
            ->create([
                'sender_public_key' => $validatorRegistration->sender_public_key,
            ])
    );

    expect($validatorResignationViewModel->validatorRegistration()->hash())->toEqual($validatorRegistration->hash);
});

it('should return null if no corresponding validator registration', function () {
    $validatorResignationViewModel = new TransactionViewModel(
        Transaction::factory()
            ->validatorResignation()
            ->create()
    );

    expect($validatorResignationViewModel->validatorRegistration())->toBeNull();
});

it('should return null corresponding validator registration if not resignation', function () {
    $validatorRegistration = Transaction::factory()
        ->validatorRegistration('C5a19e23E99bdFb7aae4301A009763AdC01c1b5B')
        ->create();

    $validatorResignationViewModel = new TransactionViewModel(
        Transaction::factory()
            ->transfer()
            ->create([
                'sender_public_key' => $validatorRegistration->sender_public_key,
            ])
    );

    expect($validatorResignationViewModel->validatorRegistration())->toBeNull();
});

it('should return receipt error', function () {
    $transaction = Transaction::factory()->create();

    Receipt::factory()->create([
        'transaction_hash' => $transaction->hash,
        'status'           => false,
        'output'           => function () {
            $stream = fopen('php://temp', 'r+');
            fwrite($stream, hex2bin('cd03235e'));
            rewind($stream);

            return $stream;
        },
    ]);

    $viewModel = new TransactionViewModel($transaction);

    expect($viewModel->parseReceiptError())->toBe('CallerIsNotValidator');
});

it('should return null if no receipt error', function () {
    $transaction = Transaction::factory()->create();

    Receipt::factory()
        ->create([
            'transaction_hash' => $transaction->hash,
            'status'           => false,
        ]);

    $viewModel = new TransactionViewModel($transaction);

    expect($viewModel->parseReceiptError())->toBeNull();
});

it('should return receipt error for insufficient gas', function () {
    $transaction = Transaction::factory()->create([
        'gas' => BigNumber::new(80131),
    ]);

    Receipt::factory()->create([
        'transaction_hash' => $transaction->hash,
        'gas_used'         => BigNumber::new(79326)->valueOf(),
        'status'           => false,
    ]);

    $viewModel = new TransactionViewModel($transaction);

    expect($viewModel->parseReceiptError())->toBe('InsufficientGas');
});

it('should not return receipt error for insufficient gas if receipt did not fail', function () {
    $transaction = Transaction::factory()->create([
        'gas' => BigNumber::new(80131),
    ]);

    Receipt::factory()->create([
        'transaction_hash' => $transaction->hash,
        'gas_used'         => BigNumber::new(79326)->valueOf(),
        'status'           => true,
    ]);

    $viewModel = new TransactionViewModel($transaction);

    expect($viewModel->parseReceiptError())->toBeNull();
});

it('should not modify gas used instance when getting receipt error', function () {
    $transaction = Transaction::factory()->create([
        'gas' => BigNumber::new(80131),
    ]);

    $receipt = Receipt::factory()->create([
        'transaction_hash' => $transaction->hash,
        'gas_used'         => BigNumber::new(79326),
        'status'           => false,
    ]);

    $viewModel = new TransactionViewModel($transaction);

    expect($viewModel->parseReceiptError())->toBe('InsufficientGas');
    expect($transaction->receipt->gas_used)->toEqual($receipt->gas_used);
});
