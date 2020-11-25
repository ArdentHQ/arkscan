<?php

declare(strict_types=1);

use App\Contracts\Network;
use App\Contracts\Network as Contract;
use App\Enums\CoreTransactionTypeEnum;
use App\Enums\MagistrateTransactionEntityActionEnum;
use App\Enums\MagistrateTransactionTypeEnum;
use App\Enums\TransactionTypeGroupEnum;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Blockchain\Network as Blockchain;
use App\Services\Blockchain\NetworkFactory;
use App\Services\Cache\DelegateCache;
use App\Services\Cache\MarketSquareCache;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\WalletCache;
use App\ViewModels\WalletViewModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use function Spatie\Snapshots\assertMatchesSnapshot;
use function Tests\configureExplorerDatabase;
use function Tests\fakeKnownWallets;

beforeEach(function () {
    configureExplorerDatabase();

    $this->app->singleton(Contract::class, fn () => NetworkFactory::make('production'));

    $wallet = Wallet::factory()->create([
        'balance'      => '100000000000',
        'nonce'        => 1000,
        'attributes'   => [
            'delegate' => [
                'voteBalance' => '100000000000',
            ],
        ],
    ]);

    $this->subject = new WalletViewModel($wallet);

    Block::factory()->create([
        'total_amount'         => '1000000000',
        'total_fee'            => '800000000',
        'reward'               => '200000000',
        'generator_public_key' => $wallet->public_key,
    ]);
});

it('should get the url', function () {
    expect($this->subject->url())->toBeString();
    expect($this->subject->url())->toBe(route('wallet', $this->subject->address()));
});

it('should get the balance', function () {
    expect($this->subject->balance())->toBeFloat();

    assertMatchesSnapshot($this->subject->balance());
});

it('should get the nonce', function () {
    expect($this->subject->nonce())->toBeInt();
    expect($this->subject->nonce())->toBe(1000);
});

it('should get the balance as percentage from supply', function () {
    (new NetworkCache())->setSupply(fn () => '1000000000000');

    expect($this->subject->balancePercentage())->toBeFloat();
    expect($this->subject->balancePercentage())->toBe(10.0);
});

it('should get the votes', function () {
    expect($this->subject->votes())->toBeFloat();

    assertMatchesSnapshot($this->subject->votes());
});

it('should get the votes as percentage from supply', function () {
    (new NetworkCache())->setSupply(fn () => '1000000000000');

    expect($this->subject->votesPercentage())->toBeFloat();
    expect($this->subject->votesPercentage())->toBe(10.0);
});

it('should sum up the total forged', function () {
    (new DelegateCache())->setTotalFees(fn () => [$this->subject->publicKey() => '1000000000']);
    (new DelegateCache())->setTotalRewards(fn () => [$this->subject->publicKey() => '1000000000']);

    expect($this->subject->totalForged())->toBeFloat();

    assertMatchesSnapshot($this->subject->totalForged());
});

it('should sum up the amount forged', function () {
    (new DelegateCache())->setTotalAmounts(fn () => [$this->subject->publicKey() => '1000000000']);

    expect($this->subject->amountForged())->toBeInt();

    assertMatchesSnapshot($this->subject->amountForged());
});

it('should sum up the fees forged', function () {
    (new DelegateCache())->setTotalFees(fn () => [$this->subject->publicKey() => '800000000']);

    expect($this->subject->feesForged())->toBeInt();

    assertMatchesSnapshot($this->subject->feesForged());
});

it('should sum up the rewards forged', function () {
    (new DelegateCache())->setTotalRewards(fn () => [$this->subject->publicKey() => '200000000']);

    expect($this->subject->rewardsForged())->toBeInt();

    assertMatchesSnapshot($this->subject->rewardsForged());
});

it('should determine if the wallet is known', function () {
    fakeKnownWallets();

    $subject = new WalletViewModel(Wallet::factory()->create(['address' => 'AagJoLEnpXYkxYdYkmdDSNMLjjBkLJ6T67']));

    expect($subject->isKnown())->toBeTrue();

    $subject = new WalletViewModel(Wallet::factory()->create(['address' => 'unknown']));

    expect($subject->isKnown())->toBeFalse();
});

it('should determine if the wallet is owned by the team', function () {
    fakeKnownWallets();

    $subject = new WalletViewModel(Wallet::factory()->create(['address' => 'AagJoLEnpXYkxYdYkmdDSNMLjjBkLJ6T67']));

    expect($subject->isOwnedByTeam())->toBeTrue();

    $subject = new WalletViewModel(Wallet::factory()->create(['address' => 'unknown']));

    expect($subject->isOwnedByTeam())->toBeFalse();
});

it('should determine if the wallet is owned by an exchange', function () {
    fakeKnownWallets();

    $subject = new WalletViewModel(Wallet::factory()->create(['address' => 'ANvR7ny44GrLy4NTfuVqjGYr4EAwK7vnkW']));

    expect($subject->isOwnedByExchange())->toBeTrue();

    $subject = new WalletViewModel(Wallet::factory()->create(['address' => 'unknown']));

    expect($subject->isOwnedByExchange())->toBeFalse();
});

it('should determine if the wallet is a delegate', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'attributes' => [],
    ]));

    expect($this->subject->isDelegate())->toBeFalse();

    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'attributes'   => [
            'delegate' => [
                'username' => 'John',
            ],
        ],
    ]));

    expect($this->subject->isDelegate())->toBeTrue();
});

it('should determine if the wallet has registrations', function () {
    expect($this->subject->hasRegistrations())->toBeFalse();

    Transaction::factory()->create([
        'sender_public_key' => $this->subject->publicKey(),
        'type'              => MagistrateTransactionTypeEnum::ENTITY,
        'type_group'        => TransactionTypeGroupEnum::MAGISTRATE,
        'asset'             => [
            'action' => MagistrateTransactionEntityActionEnum::REGISTER,
        ],
    ]);

    expect($this->subject->hasRegistrations())->toBeTrue();
});

it('should get the registrations', function () {
    expect($this->subject->registrations())->toBeInstanceOf(Collection::class);
});

it('should determine if the wallet is voting', function () {
    expect($this->subject->isVoting())->toBeFalse();

    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'attributes' => [
            'vote' => Wallet::factory()->create()->public_key,
        ],
    ]));

    expect($this->subject->isVoting())->toBeTrue();
});

it('should get the wallet of the vote', function () {
    expect($this->subject->vote())->toBeNull();

    $vote = Wallet::factory()->create();

    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'attributes' => [
            'vote' => $vote->public_key,
        ],
    ]));

    (new WalletCache())->setVote($vote->public_key, $vote);

    expect($this->subject->vote())->toBeInstanceOf(WalletViewModel::class);
});

it('should fail to get the wallet of the vote if the wallet has no public key', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'public_key' => null,
    ]));

    expect($this->subject->vote())->toBeNull();
});

it('should fail to get the wallet of the vote if it is not cached', function () {
    expect($this->subject->vote())->toBeNull();

    $vote = Wallet::factory()->create();

    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'attributes' => [
            'vote' => $vote->public_key,
        ],
    ]));

    expect($this->subject->vote())->toBeNull();
});

it('should get the performance if the wallet is a delegate', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'balance'      => '100000000000',
        'nonce'        => 1000,
        'attributes'   => [
            'delegate' => [],
        ],
    ]));

    expect($this->subject->performance())->toBeArray();
});

it('should fail to get the performance if the wallet is not a delegate', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'balance'      => '100000000000',
        'nonce'        => 1000,
        'attributes'   => [],
    ]));

    expect($this->subject->performance())->toBeEmpty();
});

it('should fail to get the performance if the wallet has no public key', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'public_key' => null,
    ]));

    expect($this->subject->performance())->toBeEmpty();
});

it('should determine if a new delegate has forged', function () {
    (new WalletCache())->setPerformance($this->subject->publicKey(), []);

    expect($this->subject->hasForged())->toBeFalse();

    Cache::flush();

    (new WalletCache())->setPerformance($this->subject->publicKey(), [true]);

    expect($this->subject->hasForged())->toBeTrue();

    Cache::flush();

    (new WalletCache())->setPerformance($this->subject->publicKey(), [true, false, false]);

    expect($this->subject->hasForged())->toBeFalse();
});

it('should determine if the delegate just missed a block', function () {
    (new WalletCache())->setPerformance($this->subject->publicKey(), [true, false, true, true, true]);

    expect($this->subject->justMissed())->toBeFalse();

    Cache::flush();

    (new WalletCache())->setPerformance($this->subject->publicKey(), [true, false, true, false, true]);

    expect($this->subject->justMissed())->toBeFalse();

    Cache::flush();

    (new WalletCache())->setPerformance($this->subject->publicKey(), [true, true, true, true, false]);

    expect($this->subject->justMissed())->toBeTrue();
});

it('should determine if the delegate keeps is missing blocks', function () {
    Cache::tags('wallet')->put(md5("performance/{$this->subject->publicKey()}"), [true, false, true, false, true]);

    expect($this->subject->keepsMissing())->toBeFalse();

    Cache::tags('wallet')->put(md5("performance/{$this->subject->publicKey()}"), [true, false, true, false, false]);

    expect($this->subject->keepsMissing())->toBeTrue();
});

it('should get the resignation id', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'balance'      => '100000000000',
        'nonce'        => 1000,
        'attributes'   => [
            'delegate' => [
                'resigned' => true,
            ],
        ],
    ]));

    $transaction = Transaction::factory()->create([
        'type'              => CoreTransactionTypeEnum::DELEGATE_RESIGNATION,
        'type_group'        => TransactionTypeGroupEnum::CORE,
        'sender_public_key' => $this->subject->publicKey(),
    ]);

    (new WalletCache())->setResignationId($this->subject->publicKey(), $transaction->id);

    expect($this->subject->resignationId())->toBeString();
});

it('should fail to get the resignation id if the delegate is not resigned', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'balance'      => '100000000000',
        'nonce'        => 1000,
        'attributes'   => [
            'delegate' => [],
        ],
    ]));

    expect($this->subject->resignationId())->toBeNull();
});

it('should fail to get the resignation id if the wallet has no public key', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'public_key' => null,
    ]));

    expect($this->subject->resignationId())->toBeNull();
});

it('should get the vote weight as percentage', function () {
    expect($this->subject->votePercentage())->toBeNull();

    $vote = Wallet::factory()->create([
        'attributes' => [
            'delegate' => ['voteBalance' => 10e8],
        ],
    ]);

    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'balance'    => 1e8,
        'attributes' => ['vote' => $vote->public_key],
    ]));

    expect($this->subject->votePercentage())->toBeNull();

    (new WalletCache())->setVote($vote->public_key, $vote);

    expect($this->subject->votePercentage())->toBeFloat();
    expect($this->subject->votePercentage())->toBe(10.0);
});

it('should fail to get the vote weight as percentage if the wallet has no public key', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'public_key' => null,
    ]));

    expect($this->subject->votePercentage())->toBeNull();
});

it('should fail to get the productivity if the wallet is a delegate', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'balance'    => 1e8,
        'attributes' => [
            'delegate' => [],
        ],
    ]));

    expect($this->subject->productivity())->toBeFloat();
    expect($this->subject->productivity())->toBe(0.0);

    (new WalletCache())->setProductivity($this->subject->publicKey(), 10);

    expect($this->subject->productivity())->toBeFloat();
    expect($this->subject->productivity())->toBe(10.0);
});

it('should fail to get the productivity if the wallet is not a delegate', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'attributes' => [],
    ]));

    expect($this->subject->productivity())->toBeFloat();
    expect($this->subject->productivity())->toBe(0.0);
});

it('should fail to get the productivity if the wallet has no public key', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'public_key' => null,
    ]));

    expect($this->subject->productivity())->toBeFloat();
    expect($this->subject->productivity())->toBe(0.0);
});

it('should determine if the wallet is cold', function () {
    expect($this->subject->isCold())->toBeFalse();

    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'public_key' => null,
    ]));

    expect($this->subject->isCold())->toBeTrue();
});

it('should get the voter count', function () {
    $wallet = Wallet::factory()->create();

    (new WalletCache())->setVoterCount($wallet->public_key, 5);

    $this->subject = new WalletViewModel($wallet);

    expect($this->subject->voterCount())->toBe(5);
});

it('should fail to get the voter count if the wallet has no public key', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create(['public_key' => null]));

    expect($this->subject->voterCount())->toBe(0);
});

it('should get the username if the wallet is known', function () {
    fakeKnownWallets();

    $this->subject = new WalletViewModel(Wallet::factory()->create(['address' => 'AagJoLEnpXYkxYdYkmdDSNMLjjBkLJ6T67']));

    expect($this->subject->username())->toBe('ACF Hot Wallet');
});

it('should get the username if the wallet is a delegate', function () {
    fakeKnownWallets();

    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'attributes'   => [
            'delegate' => [
                'username' => 'John',
            ],
        ],
    ]));

    expect($this->subject->username())->toBe('John');
});

it('should determine if the wallet has a second signature', function () {
    expect($this->subject->hasSecondSignature())->toBeBool();
});

it('should determine if the wallet has a multi signature', function () {
    expect($this->subject->hasMultiSignature())->toBeBool();
});

it('should determine the commission', function () {
    $this->app->singleton(Network::class, fn () => new Blockchain(config('explorer.networks.production')));

    (new MarketSquareCache())->setProfile($this->subject->address(), [
        'ipfs' => [
            'data' => [
                'meta' => [
                    'delegate' => [
                        'percentage' => [
                            'min' => 50,
                        ],
                    ],
                ],
            ],
        ],
    ]);

    expect($this->subject->commission())->toBe(50);
});

it('should determine the payout frequency', function () {
    $this->app->singleton(Network::class, fn () => new Blockchain(config('explorer.networks.production')));

    (new MarketSquareCache())->setProfile($this->subject->address(), [
        'ipfs' => [
            'data' => [
                'meta' => [
                    'delegate' => [
                        'frequency' => [
                            'type'  => 'day',
                            'value' => 5,
                        ],
                    ],
                ],
            ],
        ],
    ]);

    expect($this->subject->payoutFrequency())->toBe('Every 5 Days');
});

it('should determine the payout minimum', function () {
    $this->app->singleton(Network::class, fn () => new Blockchain(config('explorer.networks.production')));

    (new MarketSquareCache())->setProfile($this->subject->address(), [
        'ipfs' => [
            'data' => [
                'meta' => [
                    'delegate' => [
                        'distribution' => [
                            'min' => 500,
                        ],
                    ],
                ],
            ],
        ],
    ]);

    expect($this->subject->payoutMinimum())->toBe(500);
});

it('should build the MarketSquare profile URL', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'attributes'   => [
            'delegate' => [
                'username' => 'John',
            ],
        ],
    ]));

    expect($this->subject->profileUrl())->toBe('https://marketsquare.io/delegates/john');
});

it('should fail to build the MarketSquare profile URL if there is no username', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'attributes' => [],
    ]));

    expect($this->subject->profileUrl())->toBeNull();
});
