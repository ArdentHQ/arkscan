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
use App\Services\Cache\TokenTransferCache;
use App\ViewModels\TransactionViewModel;
use App\ViewModels\WalletViewModel;
use Carbon\Carbon;
use Illuminate\Support\Str;
use function Spatie\Snapshots\assertMatchesSnapshot;
use function Tests\faker;

beforeEach(function () {
    $this->block = Block::factory()->create(['height' => 1]);
    Block::factory()->create(['height' => 5000000]);

    (new NetworkCache())->setHeight(fn () => 5000000);

    $this->sender  = Wallet::factory()->create();
    $this->subject = new TransactionViewModel(Transaction::factory()->create([
        'block_id'               => $this->block->id,
        'block_height'           => 1,
        'gas_price'              => 1,
        'amount'                 => 2 * 1e18,
        'sender_public_key'      => $this->sender->public_key,
        'recipient_address'      => Wallet::factory()->create(['address' => 'recipient'])->address,
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
        ->create([
            'sender_public_key' => $this->sender->public_key,
            'recipient_address' => $this->sender->address,
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

it('should get the amount received for transfer transactions', function () {
    expect($this->subject->amountReceived('recipient'))->toBeFloat();

    assertMatchesSnapshot($this->subject->amountReceived('recipient'));
});

it('should get the amount including fee', function () {
    expect($this->subject->amountWithFee())->toBeFloat();

    assertMatchesSnapshot($this->subject->amountWithFee());
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

it('should determine the transaction type', function (string $type) {
    $transaction = Transaction::factory()->{$type}()->create();
    $subject     = new TransactionViewModel($transaction);

    expect($subject->{'is'.ucfirst($type)}())->toBeTrue();

    $subject = new TransactionViewModel(Transaction::factory()->withPayload('123456')->create());

    expect($subject->{'is'.ucfirst($type)}())->toBeFalse();
})->with([
    ['transfer'],
    ['validatorResignation'],
    ['unvote'],
]);

it('should determine the transaction type ', function (string $type) {
    $wallet    = Wallet::factory()->activeValidator()->create();

    $transaction = Transaction::factory()->{$type}($wallet->address)->create();
    $subject     = new TransactionViewModel($transaction);

    expect($subject->{'is'.ucfirst($type)}())->toBeTrue();

    $subject = new TransactionViewModel(Transaction::factory()->withPayload('123456')->create());

    expect($subject->{'is'.ucfirst($type)}())->toBeFalse();
})->with([
    ['vote'],
    ['validatorRegistration'],
]);

it('should determine if the transaction is self-receiving', function (string $type) {
    $wallet      = Wallet::factory()->activeValidator()->create();
    $transaction = Transaction::factory()->{$type}(when(in_array($type, ['validatorRegistration', 'vote'], true), $wallet->address))->create();
    $subject     = new TransactionViewModel($transaction);

    expect($subject->isSelfReceiving())->toBeTrue();

    $subject = new TransactionViewModel(Transaction::factory()

    ->create([
        'asset'      => $transaction->asset,
    ]));

    expect($subject->isSelfReceiving())->toBeFalse();
})->with([
    ['validatorRegistration'],
    ['vote'],
    ['unvote'],
    ['validatorResignation'],
]);

it('should fallback to the sender if no recipient address exists', function () {
    $this->subject = new TransactionViewModel(Transaction::factory()->create([
        'recipient_address' => null,
    ]));

    expect($this->subject->recipient())->toEqual($this->subject->sender());
});

it('should fallback to receipt deployed contract address if set', function () {
    $wallet = Wallet::factory()->create(['address' => 'deployedContractAddress']);

    $receipt = Receipt::factory()
        ->state(['deployed_contract_address' => $wallet->address]);

    $this->subject = new TransactionViewModel(Transaction::factory()->has($receipt)->create([
        'recipient_address' => null,
    ]));

    expect($this->subject->recipient()->address())->toBe('deployedContractAddress');
});

it('should get the voted validator', function () {
    Wallet::factory()->create(['public_key' => 'publicKey']);

    $validator    = Wallet::factory()->activeValidator()->create();

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

    it('should fail to get formatted multi payment receipts if not a multi payment', function () {
        $transaction = new TransactionViewModel(Transaction::factory()
            ->withPayload('123456')
            ->create());

        expect(function () use ($transaction) {
            $transaction->multiPaymentRecipients();
        })->toThrow(Exception::class);
    });
});

it('should calculate fee with receipt', function () {
    $transaction = Transaction::factory()->create([
        'gas_price' => 54,
    ]);

    Receipt::factory()->create([
        'id'       => $transaction->id,
        'gas_used' => 21000,
    ]);

    $viewModel = new TransactionViewModel($transaction->fresh());

    expect($viewModel->fee())->toEqual(0.001134);
});

it('should return gas price if no receipt', function () {
    $transaction = Transaction::factory()->create([
        'gas_price' => 54,
    ]);

    $viewModel = new TransactionViewModel($transaction->fresh());

    expect($viewModel->fee())->toEqual(0.000000054);
});

it('should should determine if transaction failed', function () {
    $transaction = Transaction::factory()->create();

    Receipt::factory()->create([
        'id'      => $transaction->id,
        'success' => false,
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
        'id'      => $transaction->id,
        'success' => true,
    ]);

    $viewModel = new TransactionViewModel($transaction->fresh());

    expect($viewModel->hasFailedStatus())->toBeFalse();
});

it('should get the gas used', function () {
    $transaction = Transaction::factory()->create();

    Receipt::factory()->create([
        'id'       => $transaction->id,
        'gas_used' => 8,
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
        ->validatorRegistration('0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B')
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
    ['validatorRegistration'],
    ['vote', ['0x0']],
    ['unvote'],
    ['validatorResignation'],
    ['usernameRegistration'],
    ['usernameResignation'],
    ['contractDeployment'],
]);

it('should get token name for contract deployment', function () {
    $cache = new TokenTransferCache();

    $transaction = Transaction::factory()->contractDeployment()->create();

    $contractAddress = faker()->wallet['address'];

    Receipt::factory()->create([
        'id' => $transaction->id,
        'deployed_contract_address' => $contractAddress,
    ]);

    $cache->setTokenName($contractAddress, 'TESTTOKEN');

    $viewModel = new TransactionViewModel($transaction->fresh());

    expect($viewModel->tokenName())->toBe('TESTTOKEN');
});

it('should get token name for token transfer', function () {
    $cache = new TokenTransferCache();

    $contractAddress = faker()->wallet['address'];

    $transaction = Transaction::factory()
        ->tokenTransfer('0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B', 100000, $contractAddress)
        ->create();

    $cache->setTokenName($contractAddress, 'TESTTOKEN');

    $viewModel = new TransactionViewModel($transaction->fresh());

    expect($viewModel->tokenName())->toBe('TESTTOKEN');
});

it('should return null if no token name', function () {
    $transaction = Transaction::factory()->contractDeployment()->create();

    Receipt::factory()->create([
        'id' => $transaction->id,
        'deployed_contract_address' => faker()->wallet['address'],
    ]);

    $viewModel = new TransactionViewModel($transaction->fresh());

    expect($viewModel->tokenName())->toBeNull();
});

it('should return null for token name if not a contract deployment or token transfer', function () {
    $transaction = Transaction::factory()->transfer()->create();

    $viewModel = new TransactionViewModel($transaction->fresh());

    expect($viewModel->tokenName())->toBeNull();
});

it('should return null for token name if no receipt', function () {
    $transaction = Transaction::factory()->contractDeployment()->create();

    $viewModel = new TransactionViewModel($transaction->fresh());

    expect($viewModel->tokenName())->toBeNull();
});

it('should return null for token name if receipt has no deployment_contract_address', function () {
    $transaction = Transaction::factory()->contractDeployment()->create();

    Receipt::factory()->create([
        'id' => $transaction->id,
        'deployed_contract_address' => null,
    ]);

    $viewModel = new TransactionViewModel($transaction->fresh());

    expect($viewModel->tokenName())->toBeNull();
});
