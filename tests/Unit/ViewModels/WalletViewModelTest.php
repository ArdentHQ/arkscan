<?php

declare(strict_types=1);

use App\Contracts\Network as Contract;
use App\Facades\Network;
use App\Facades\Rounds;
use App\Models\Block;
use App\Models\Round;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\Blockchain\NetworkFactory;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\ValidatorCache;
use App\Services\Cache\WalletCache;
use App\ViewModels\WalletViewModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use function Spatie\Snapshots\assertMatchesSnapshot;
use function Tests\fakeKnownWallets;
use Tests\Stubs\RoundsMock;

beforeEach(function () {
    $this->app->singleton(Contract::class, fn () => NetworkFactory::make('production'));

    $this->wallet = Wallet::factory()->create([
        'balance'    => 1000 * 1e18,
        'nonce'      => 1000,
        'attributes' => [
            'validatorVoteBalance' => 1000 * 1e18,
            'validatorPublicKey'   => 'publickey',
        ],
    ]);

    $this->subject = new WalletViewModel($this->wallet);

    Block::factory()->create([
        'total_amount'         => 10 * 1e18,
        'total_fee'            => 8 * 1e18,
        'reward'               => 2 * 1e18,
        'generator_public_key' => $this->wallet->public_key,
    ]);
});

it('should get the url', function () {
    expect($this->subject->url())->toBeString();
    expect($this->subject->url())->toBe(route('wallet', $this->subject->address()));
});

it('should get the address', function () {
    expect($this->subject->address())->toBe($this->wallet->address);
});

it('should get an id from the address', function () {
    expect($this->subject->id())->toBe($this->wallet->address);
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
    (new NetworkCache())->setSupply(fn () => 10000 * 1e18);

    expect($this->subject->balancePercentage())->toBeFloat();
    expect($this->subject->balancePercentage())->toBe(10.0);
});

it('should get the votes', function () {
    expect($this->subject->votes())->toBeFloat();

    assertMatchesSnapshot($this->subject->votes());
});

it('should get the votes as percentage from supply', function () {
    (new NetworkCache())->setSupply(fn () => 10000 * 1e18);

    expect($this->subject->votesPercentage())->toBeFloat();
    expect($this->subject->votesPercentage())->toBe(10.0);
});

it('should sum up the total forged', function () {
    (new ValidatorCache())->setTotalFees([$this->subject->publicKey() => 10 * 1e18]);
    (new ValidatorCache())->setTotalRewards([$this->subject->publicKey() => 10 * 1e18]);

    expect($this->subject->totalForged())->toBeFloat();

    assertMatchesSnapshot($this->subject->totalForged());
});

it('should sum up the amount forged', function () {
    (new ValidatorCache())->setTotalAmounts([$this->subject->publicKey() => 10 * 1e18]);

    expect($this->subject->amountForged())->toBeInstanceOf(BigNumber::class);

    assertMatchesSnapshot($this->subject->amountForged()->valueOf()->__toString());
});

it('should sum up the fees forged', function () {
    (new ValidatorCache())->setTotalFees([$this->subject->publicKey() => 8 * 1e18]);

    expect($this->subject->feesForged())->toBeInstanceOf(BigNumber::class);

    assertMatchesSnapshot($this->subject->feesForged()->valueOf()->__toString());
});

it('should sum up the rewards forged', function () {
    (new ValidatorCache())->setTotalRewards([$this->subject->publicKey() => 2 * 1e18]);

    expect($this->subject->rewardsForged())->toBeInstanceOf(BigNumber::class);

    assertMatchesSnapshot($this->subject->rewardsForged()->valueOf()->__toString());
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

it('should determine if the wallet has a special type when known', function () {
    fakeKnownWallets();

    $subject = new WalletViewModel(Wallet::factory()
        ->activeValidator()
        ->create(['address' => 'AagJoLEnpXYkxYdYkmdDSNMLjjBkLJ6T67']));

    expect($subject->isKnown())->toBeTrue();
    expect($subject->hasMultiSignature())->toBeFalse();
    expect($subject->hasSecondSignature())->toBeFalse();
    expect($subject->isOwnedByExchange())->toBeFalse();
    expect($subject->hasSpecialType())->toBeTrue();

    $subject = new WalletViewModel(Wallet::factory()
        ->activeValidator()
        ->create(['address' => 'unknown']));

    expect($subject->hasSpecialType())->toBeFalse();
});

it('should determine if the wallet has a special type if multisignature', function () {
    fakeKnownWallets();

    $subject = new WalletViewModel(Wallet::factory()
        ->multiSignature()
        ->create(['address' => 'AHLAT5XDfzZ1gkQVCrW8pKfYdfyMQ9t7ra']));

    expect($subject->isKnown())->toBeFalse();
    expect($subject->hasMultiSignature())->toBeTrue();
    expect($subject->isOwnedByExchange())->toBeFalse();
    expect($subject->hasSpecialType())->toBeTrue();

    $subject = new WalletViewModel(Wallet::factory()
        ->activeValidator()
        ->create(['address' => 'unknown']));

    expect($subject->hasSpecialType())->toBeFalse();
});

it('should determine if the wallet has a special type if second signature', function () {
    fakeKnownWallets();

    $subject = new WalletViewModel(Wallet::factory()->create(['address' => 'AHLAT5XDfzZ1gkQVCrW8pKfYdfyMQ9t7ra']));

    expect($subject->isKnown())->toBeFalse();
    expect($subject->hasSecondSignature())->toBeTrue();
    expect($subject->isOwnedByExchange())->toBeFalse();
    expect($subject->hasSpecialType())->toBeTrue();

    $subject = new WalletViewModel(Wallet::factory()
        ->activeValidator()
        ->create(['address' => 'unknown']));

    expect($subject->hasSpecialType())->toBeFalse();
});

it('should determine if the wallet has a special type if exchange', function () {
    fakeKnownWallets();

    $subject = new WalletViewModel(Wallet::factory()->create(['address' => 'AFrPtEmzu6wdVpa2CnRDEKGQQMWgq8nE9V']));

    expect($subject->isKnown())->toBeTrue();
    expect($subject->isOwnedByExchange())->toBeTrue();
    expect($subject->hasSpecialType())->toBeTrue();

    $subject = new WalletViewModel(Wallet::factory()
        ->activeValidator()
        ->create(['address' => 'unknown']));

    expect($subject->hasSpecialType())->toBeFalse();
});

it('should determine if the wallet is a validator', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'attributes' => [],
    ]));

    expect($this->subject->isValidator())->toBeFalse();

    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'attributes' => [
            'validatorPublicKey' => 'publickey',
        ],
    ]));

    expect($this->subject->isValidator())->toBeTrue();
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

it('should get the wallet model', function () {
    expect($this->subject->model())->toBe($this->wallet);
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

it('should get the performance if the wallet is a validator', function () {
    Rounds::swap(new RoundsMock());

    $wallet = Wallet::factory()
        ->activeValidator()
        ->create([
            'balance'      => 1000 * 1e18,
            'nonce'        => 1000,
            'attributes'   => [
                'validatorPublicKey' => 'publicKey',
            ],
        ]);

    (new WalletCache())->setPerformance($wallet->public_key, [true, true]);

    $this->subject = new WalletViewModel($wallet);

    expect($this->subject->performance())->toBe([
        true,
        false,
    ]);
});

it('should fail to get the performance if the wallet is not a validator', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'balance'      => 1000 * 1e18,
        'nonce'        => 1000,
        'attributes'   => [],
    ]));

    expect($this->subject->performance())->toBeEmpty();
    expect($this->subject->hasForged())->toBeFalse();
});

it('should fail to get the performance if the wallet has no public key', function () {
    $this->subject = new WalletViewModel(Wallet::factory()
        ->activeValidator()
        ->create([
            'public_key' => null,
        ]));

    expect($this->subject->performance())->toBeEmpty();
});

it('should determine if a new validator has forged', function () {
    $block = Block::factory()->create([
        'generator_public_key' => $this->wallet->public_key,
    ]);

    Rounds::swap(new RoundsMock());

    (new WalletCache())->setPerformance($this->subject->publicKey(), [false, false]);

    expect($this->subject->hasForged())->toBeFalse();

    Cache::flush();

    Rounds::swap(new RoundsMock($block));

    (new WalletCache())->setPerformance($this->subject->publicKey(), [false, true]);

    expect($this->subject->hasForged())->toBeTrue();

    Cache::flush();

    Rounds::swap(new RoundsMock());

    (new WalletCache())->setPerformance($this->subject->publicKey(), [false, false]);

    expect($this->subject->hasForged())->toBeFalse();
});

it('should determine if the validator just missed a block', function () {
    $block = Block::factory()->create([
        'generator_public_key' => $this->wallet->public_key,
    ]);

    Rounds::swap(new RoundsMock($block));

    (new WalletCache())->setPerformance($this->subject->publicKey(), [true, true]);

    expect($this->subject->justMissed())->toBeFalse();

    Cache::flush();

    (new WalletCache())->setPerformance($this->subject->publicKey(), [false, true]);

    expect($this->subject->justMissed())->toBeFalse();

    Cache::flush();

    Rounds::swap(new RoundsMock());

    (new WalletCache())->setPerformance($this->subject->publicKey(), [true, false]);

    expect($this->subject->justMissed())->toBeTrue();
});

it('should determine if the validator is missing blocks', function () {
    Rounds::swap(new RoundsMock());

    Round::factory()->create([
        'round'        => 112167,
        'round_height' => 112167 * Network::validatorCount(),
        'validators'   => [$this->wallet->public_key],
    ]);

    Cache::tags('wallet')->put(md5("performance/{$this->subject->publicKey()}"), [false, true]);

    expect($this->subject->keepsMissing())->toBeFalse();

    Cache::tags('wallet')->put(md5("performance/{$this->subject->publicKey()}"), [false, false]);

    expect($this->subject->keepsMissing())->toBeTrue();
});

it('should get the resignation id', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'balance'      => 1000 * 1e18,
        'nonce'        => 1000,
        'attributes'   => [
            'validatorResigned' => true,
        ],
    ]));

    $transaction = Transaction::factory()->validatorResignation()->create([
        'sender_public_key' => $this->subject->publicKey(),
    ]);

    (new WalletCache())->setResignationId($this->subject->publicKey(), $transaction->id);

    expect($this->subject->resignationId())->toBeString();
});

it('should fail to get the resignation id if the validator is not resigned', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'balance'      => 1000 * 1e18,
        'nonce'        => 1000,
        'attributes'   => [
            'validator' => [],
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
            'validatorVoteBalance' => 10 * 1e18,
        ],
    ]);

    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'balance'    => 1e18,
        'attributes' => [
            'vote' => $vote->public_key,
        ],
    ]));

    expect($this->subject->votePercentage())->toBeNull();

    (new WalletCache())->setVote($vote->public_key, $vote);

    expect($this->subject->votePercentage())->toBeFloat();
    expect($this->subject->votePercentage())->toBe(10.0);
});

it('should handle vote weight percentage with 0 vote balance', function () {
    expect($this->subject->votePercentage())->toBeNull();

    $vote = Wallet::factory()->create([
        'attributes' => [
            'validatorVoteBalance' => 0,
        ],
    ]);

    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'balance'    => 0,
        'attributes' => [
            'vote' => $vote->public_key,
        ],
    ]));

    expect($this->subject->votePercentage())->toBeNull();

    (new WalletCache())->setVote($vote->public_key, $vote);

    expect($this->subject->votePercentage())->toBeNull();
});

it('should handle vote weight percentage with 1 arktoshi vote balance', function () {
    expect($this->subject->votePercentage())->toBeNull();

    $vote = Wallet::factory()->create([
        'attributes' => [
            'validatorVoteBalance' => 1e18,
        ],
    ]);

    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'balance'    => 1e18,
        'attributes' => ['vote' => $vote->public_key],
    ]));

    expect($this->subject->votePercentage())->toBeNull();

    (new WalletCache())->setVote($vote->public_key, $vote);

    expect($this->subject->votePercentage())->toBeFloat();
    expect($this->subject->votePercentage())->toBe(100.0);
});

it('should fail to get the vote weight as percentage if the wallet has no public key', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'public_key' => null,
    ]));

    expect($this->subject->votePercentage())->toBeNull();
});

it('should get the productivity if the wallet is a validator', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'balance'    => 1e18,
        'attributes' => [
            'validatorPublicKey' => 'publickey',
        ],
    ]));

    expect($this->subject->productivity())->toBeFloat();
    expect($this->subject->productivity())->toBe(0.0);

    (new WalletCache())->setProductivity($this->subject->publicKey(), 10);

    expect($this->subject->productivity())->toBeFloat();
    expect($this->subject->productivity())->toBe(10.0);
});

it('should fail to get the productivity if the wallet is not a validator', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'attributes' => [],
    ]));

    expect($this->subject->productivity())->toBeFloat();
    expect($this->subject->productivity())->toBe(0.0);
});

it('should fail to get the productivity if the wallet has no public key', function () {
    $this->subject = new WalletViewModel(Wallet::factory()
        ->activeValidator()
        ->create([
            'public_key' => null,
        ]));

    expect($this->subject->productivity())->toBeFloat();
    expect($this->subject->productivity())->toBe(0.0);
});

it('should return 0 for productivity if the cached value is less than 0', function () {
    $this->subject = new WalletViewModel(Wallet::factory()
        ->activeValidator()
        ->create());

    (new WalletCache())->setProductivity($this->subject->publicKey(), -1);

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

it('should get the known wallet name before username', function () {
    fakeKnownWallets();

    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'address'    => 'AagJoLEnpXYkxYdYkmdDSNMLjjBkLJ6T67',
        'attributes' => [
            'validatorPublicKey' => 'publicKey',
            'username'           => 'john',
        ],
    ]));

    expect($this->subject->usernameIfNotKnown())->toBe('ACF Hot Wallet');
});

it('should get username if wallet not know', function () {
    fakeKnownWallets();

    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'address'    => 'random-address',
        'attributes' => [
            'validatorPublicKey' => 'publicKey',
            'username'           => 'john',
        ],
    ]));

    expect($this->subject->usernameIfNotKnown())->toBe('john');
});

it('should get the username if the wallet is a validator', function () {
    fakeKnownWallets();

    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'attributes'   => [
            'username' => 'John',
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

it('should get the validator user name', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'attributes' => [
            'username' => 'john',
        ],
    ]));

    expect($this->subject->username())->toBe('john');
});

it('should get the vote url with validator', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'public_key' => 'wallet-public-key',
        'attributes' => [
            'validatorPublicKey' => 'validator-public-key',
            'username'           => 'john',
        ],
    ]));

    expect($this->subject->voteUrl())->toStartWith('https://app.arkvault.io/#/?coin=Mainsail&nethash=');
    expect($this->subject->voteUrl())->toContain('&method=vote');
    expect($this->subject->voteUrl())->toContain('&validator=wallet-public-key');
});

it('should get whether validator is standby', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'attributes'   => [
            'username'      => 'John',
            'validatorRank' => 54,
        ],
    ]));

    expect($this->subject->isStandby())->toBeTrue();
});

it('should get whether validator is active', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'attributes' => [
            'username'           => 'John',
            'validatorRank'      => 50,
            'validatorPublicKey' => 'publickey',
        ],
    ]));

    expect($this->subject->isActive())->toBeTrue();
});

it('should get that resigned validator is not an active validator', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'attributes' => [
            'validatorResigned' => true,
        ],
    ]));

    expect($this->subject->isActive())->toBeFalse();
});

it('should get that non validator is not an active validator', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'attributes' => [],
    ]));

    expect($this->subject->isActive())->toBeFalse();
});

it('should get validator name for wallet name', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'attributes'   => [
            'username'      => 'John',
            'validatorRank' => 50,
        ],
    ]));

    expect($this->subject->usernameBeforeKnown())->toBe('John');
});

it('should get known wallet name for wallet name', function () {
    $wallet = Wallet::factory()->create([
        'attributes' => [],
    ]);

    Http::fake([
        'githubusercontent.com/*' => [
            [
                'type'    => 'exchange',
                'name'    => 'Test Wallet',
                'address' => $wallet->address,
            ],
        ],
    ]);

    $this->subject = new WalletViewModel($wallet);

    expect($this->subject->usernameBeforeKnown())->toBe('Test Wallet');
});

it('should get validator name before known wallet name for a wallet', function () {
    $wallet = Wallet::factory()->create([
        'attributes' => [
            'username'      => 'John',
            'validatorRank' => 50,
        ],
    ]);

    Http::fake([
        'githubusercontent.com/*' => [
            [
                'type'    => 'exchange',
                'name'    => 'Test Wallet',
                'address' => $wallet->address,
            ],
        ],
    ]);

    $this->subject = new WalletViewModel($wallet);

    expect($this->subject->usernameBeforeKnown())->toBe('John');
});

it('should get no name if a standard wallet', function () {
    fakeKnownWallets();

    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'attributes' => [],
    ]));

    expect($this->subject->usernameBeforeKnown())->toBeNull();
});

it('should get forged block count for validator', function () {
    $wallet = new WalletViewModel(Wallet::factory()->create([
        'balance'    => 1000 * 1e18,
        'nonce'      => 1000,
        'attributes' => [
            'validatorProducedBlocks' => 54321,
        ],
    ]));

    expect($wallet->forgedBlocks())->toBe(54321);
});

it('should get missed block count for validator', function () {
    (new WalletCache())->setMissedBlocks($this->subject->publicKey(), 12345);

    expect($this->subject->missedBlocks())->toBe(12345);
});

it('should return zero if validator has no public key', function () {
    $wallet = new WalletViewModel(Wallet::factory()->create([
        'public_key' => null,
        'balance'    => 1000 * 1e18,
        'nonce'      => 1000,
        'attributes' => [
            'validatorProducedBlocks' => 54321,
        ],
    ]));

    expect($wallet->missedBlocks())->toBe(0);
});

it('should return null for blocks since last forged if not forged', function () {
    $wallet = new WalletViewModel(Wallet::factory()->create([
        'public_key'   => null,
        'balance'      => 1000 * 1e18,
        'nonce'        => 1000,
        'attributes'   => [
            'validator' => [
                'producedBlocks' => 54321,
            ],
        ],
    ]));

    expect($wallet->blocksSinceLastForged())->toBe(null);
});

it('should return count for blocks since last forged', function () {
    $wallet = new WalletViewModel(Wallet::factory()->create([
        'balance'      => 1000 * 1e18,
        'nonce'        => 1000,
        'attributes'   => [
            'validator' => [
                'producedBlocks' => 54321,
            ],
        ],
    ]));

    $block = Block::factory()->create([
        'generator_public_key' => $wallet->publicKey(),
        'height'               => 10,
    ]);

    (new WalletCache())->setLastBlock($wallet->publicKey(), [
        'id'     => $block->id,
        'height' => $block->height->toNumber(),
    ]);

    (new NetworkCache())->setHeight(fn (): int => 100);

    expect($wallet->blocksSinceLastForged())->toBe(90);

    (new WalletCache())->setLastBlock($wallet->publicKey(), []);

    expect($wallet->blocksSinceLastForged())->toBe(null);
});

it('should return null for time since last forged if not forged', function () {
    $wallet = new WalletViewModel(Wallet::factory()->create([
        'public_key'   => null,
        'balance'      => 1000 * 1e18,
        'nonce'        => 1000,
        'attributes'   => [
            'validator' => [
                'producedBlocks' => 54321,
            ],
        ],
    ]));

    expect($wallet->durationSinceLastForged())->toBe(null);
});

it('should return count for time since last forged', function () {
    $wallet = new WalletViewModel(Wallet::factory()->create([
        'balance'      => 1000 * 1e18,
        'nonce'        => 1000,
        'attributes'   => [
            'validatorProducedBlocks' => 54321,
        ],
    ]));

    $block = Block::factory()->create([
        'timestamp'            => Carbon::parse('2021-04-14 13:02:04')->getTimestampMs(),
        'generator_public_key' => $wallet->publicKey(),
        'height'               => 10,
    ]);

    (new WalletCache())->setLastBlock($wallet->publicKey(), [
        'id'        => $block->id,
        'height'    => $block->height->toNumber(),
        'timestamp' => $block->timestamp,
    ]);

    $this->travelTo(Carbon::parse('2021-04-14 13:02:14'));

    expect($wallet->durationSinceLastForged())->toBe('~ 1 min');

    $this->travelTo(Carbon::parse('2021-04-14 13:12:14'));

    expect($wallet->durationSinceLastForged())->toBe('~ 11 min');

    $this->travelTo(Carbon::parse('2021-04-14 14:02:04'));

    expect($wallet->durationSinceLastForged())->toBe('~ 1h');

    $this->travelTo(Carbon::parse('2021-04-14 15:02:14'));

    expect($wallet->durationSinceLastForged())->toBe('~ 2h 1 min');

    $this->travelTo(Carbon::parse('2021-04-16 15:02:14'));

    expect($wallet->durationSinceLastForged())->toBe('more than a day');
});
