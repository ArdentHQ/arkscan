<?php

declare(strict_types=1);

use App\Contracts\Network as Contract;
use App\Facades\Rounds;
use App\Models\Block;
use App\Models\Round;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Blockchain\NetworkFactory;
use App\Services\Cache\DelegateCache;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\WalletCache;
use App\ViewModels\WalletViewModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use function Spatie\Snapshots\assertMatchesSnapshot;
use function Tests\fakeKnownWallets;

class WalletViewModelTest
{
    public function __construct(private ?Block $block = null)
    {
        //
    }

    public function delegates()
    {
        return new class($this->block) {
            public function __construct(private ?Block $block = null)
            {
                //
            }

            public function firstWhere()
            {
                return [
                    'status' => 'done',
                    'block'  => $this->block,
                ];
            }
        };
    }
}

beforeEach(function () {
    $this->app->singleton(Contract::class, fn () => NetworkFactory::make('production'));

    $this->wallet = Wallet::factory()->create([
        'balance'      => '100000000000',
        'nonce'        => 1000,
        'attributes'   => [
            'delegate' => [
                'voteBalance' => '100000000000',
            ],
        ],
    ]);

    $this->subject = new WalletViewModel($this->wallet);

    Block::factory()->create([
        'total_amount'         => '1000000000',
        'total_fee'            => '800000000',
        'reward'               => '200000000',
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

it('should determine if the wallet has a special type when known', function () {
    fakeKnownWallets();

    $subject = new WalletViewModel(Wallet::factory()
        ->activeDelegate()
        ->create(['address' => 'AagJoLEnpXYkxYdYkmdDSNMLjjBkLJ6T67']));

    expect($subject->isKnown())->toBeTrue();
    expect($subject->hasMultiSignature())->toBeFalse();
    expect($subject->hasSecondSignature())->toBeFalse();
    expect($subject->isOwnedByExchange())->toBeFalse();
    expect($subject->hasSpecialType())->toBeTrue();

    $subject = new WalletViewModel(Wallet::factory()
        ->activeDelegate()
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
        ->activeDelegate()
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
        ->activeDelegate()
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
        ->activeDelegate()
        ->create(['address' => 'unknown']));

    expect($subject->hasSpecialType())->toBeFalse();
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

it('should get the performance if the wallet is a delegate', function () {
    Rounds::swap(new RoundsMock());

    $wallet = Wallet::factory()->create([
        'balance'      => '100000000000',
        'nonce'        => 1000,
        'attributes'   => [
            'delegate' => [],
        ],
    ]);

    (new WalletCache())->setPerformance($wallet->public_key, [true, true]);

    $this->subject = new WalletViewModel($wallet);

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

it('should determine if the delegate just missed a block', function () {
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

it('should determine if the delegate is missing blocks', function () {
    Rounds::swap(new RoundsMock());

    Round::factory()->create([
        'round'      => '112167',
        'public_key' => $this->wallet->public_key,
    ]);

    Cache::tags('wallet')->put(md5("performance/{$this->subject->publicKey()}"), [false, true]);

    expect($this->subject->keepsMissing())->toBeFalse();

    Cache::tags('wallet')->put(md5("performance/{$this->subject->publicKey()}"), [false, false]);

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

    $transaction = Transaction::factory()->delegateResignation()->create([
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

it('should handle vote weight percentage with 0 vote balance', function () {
    expect($this->subject->votePercentage())->toBeNull();

    $vote = Wallet::factory()->create([
        'attributes' => [
            'delegate' => ['voteBalance' => 0],
        ],
    ]);

    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'balance'    => 0,
        'attributes' => ['vote' => $vote->public_key],
    ]));

    expect($this->subject->votePercentage())->toBeNull();

    (new WalletCache())->setVote($vote->public_key, $vote);

    expect($this->subject->votePercentage())->toBeNull();
});

it('should handle vote weight percentage with 1 arktoshi vote balance', function () {
    expect($this->subject->votePercentage())->toBeNull();

    $vote = Wallet::factory()->create([
        'attributes' => [
            'delegate' => ['voteBalance' => 1e8],
        ],
    ]);

    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'balance'    => 1e8,
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

it('should return 0 for productivity if the cached value is less than 0', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create());

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

it('should get the delegate user name', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'attributes'   => [
            'delegate' => [
                'username' => 'john',
            ],
        ],
    ]));

    expect($this->subject->delegateUsername())->toBe('john');
});

it('should get the vote url with public key', function () {
    expect($this->subject->voteUrl())->toStartWith('https://app.arkvault.io/#/?coin=ARK&nethash=');
    expect($this->subject->voteUrl())->toContain('&method=vote');
    expect($this->subject->voteUrl())->toContain('&publicKey=');
    expect($this->subject->voteUrl())->not->toContain('&delegate=');
});

it('should get the vote url with delegate', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'attributes'   => [
            'delegate' => [
                'username' => 'john',
            ],
        ],
    ]));

    expect($this->subject->voteUrl())->toStartWith('https://app.arkvault.io/#/?coin=ARK&nethash=');
    expect($this->subject->voteUrl())->toContain('&method=vote');
    expect($this->subject->voteUrl())->not->toContain('&publicKey=');
    expect($this->subject->voteUrl())->toContain('&delegate=john');
});

it('should get whether delegate is standby', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'attributes'   => [
            'delegate' => [
                'username' => 'John',
                'rank'     => 52,
            ],
        ],
    ]));

    expect($this->subject->isStandby())->toBeTrue();
});

it('should get whether delegate is active', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'attributes'   => [
            'delegate' => [
                'username' => 'John',
                'rank'     => 50,
            ],
        ],
    ]));

    expect($this->subject->isActive())->toBeTrue();
});

it('should get that resigned delegate is not an active delegate', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'attributes'   => [
            'delegate' => [
                'resigned' => true,
            ],
        ],
    ]));

    expect($this->subject->isActive())->toBeFalse();
});

it('should get that non delegate is not an active delegate', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'attributes' => [],
    ]));

    expect($this->subject->isActive())->toBeFalse();
});

it('should get delegate name for wallet name', function () {
    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'attributes'   => [
            'delegate' => [
                'username' => 'John',
                'rank'     => 50,
            ],
        ],
    ]));

    expect($this->subject->name())->toBe('John');
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

    expect($this->subject->name())->toBe('Test Wallet');
});

it('should get delegate name before known wallet name for a wallet', function () {
    $wallet = Wallet::factory()->create([
        'attributes'   => [
            'delegate' => [
                'username' => 'John',
                'rank'     => 50,
            ],
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

    expect($this->subject->name())->toBe('John');
});

it('should get no name if a standard wallet', function () {
    fakeKnownWallets();

    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'attributes' => [],
    ]));

    expect($this->subject->name())->toBeNull();
});

it('should get forged block count for delegate', function () {
    $wallet = new WalletViewModel(Wallet::factory()->create([
        'balance'      => '100000000000',
        'nonce'        => 1000,
        'attributes'   => [
            'delegate' => [
                'producedBlocks' => 54321,
            ],
        ],
    ]));

    expect($wallet->forgedBlocks())->toBe(54321);
});

it('should get missed block count for delegate', function () {
    (new WalletCache())->setMissedBlocks($this->subject->publicKey(), 12345);

    expect($this->subject->missedBlocks())->toBe(12345);
});

it('should return zero if delegate has no public key', function () {
    $wallet = new WalletViewModel(Wallet::factory()->create([
        'public_key'   => null,
        'balance'      => '100000000000',
        'nonce'        => 1000,
        'attributes'   => [
            'delegate' => [
                'producedBlocks' => 54321,
            ],
        ],
    ]));

    expect($wallet->missedBlocks())->toBe(0);
});

function createRound(array $performances = null, bool $addBlockForNextRound = true, int $wallets = 51): void
{
    Wallet::factory($wallets)->create()->each(function ($wallet, $index) use ($performances, $addBlockForNextRound) {
        $timestamp = Carbon::now()->add($index * 8, 'seconds')->timestamp;

        $block = Block::factory()->create([
            'height'               => 5720529,
            'timestamp'            => $timestamp,
            'generator_public_key' => $wallet->public_key,
        ]);

        // Start height for round 112168
        if ($addBlockForNextRound) {
            Block::factory()->create([
                'height'               => 5720518,
                'timestamp'            => $timestamp,
                'generator_public_key' => $wallet->public_key,
            ]);
        }

        Round::factory()->create([
            'round'      => '112168',
            'public_key' => $wallet->public_key,
        ]);

        (new WalletCache())->setDelegate($wallet->public_key, $wallet);

        if (is_null($performances)) {
            for ($i = 0; $i < 2; $i++) {
                $performances[] = (bool) mt_rand(0, 1);
            }
        }

        (new WalletCache())->setPerformance($wallet->public_key, $performances);

        (new WalletCache())->setLastBlock($wallet->public_key, [
            'id'     => $block->id,
            'height' => $block->height->toNumber(),
        ]);
    });
}
