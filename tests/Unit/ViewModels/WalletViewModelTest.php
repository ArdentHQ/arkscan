<?php

declare(strict_types=1);

use App\Enums\MagistrateTransactionEntityActionEnum;

use App\Enums\MagistrateTransactionTypeEnum;
use App\Enums\TransactionTypeGroupEnum;
use App\Models\Block;

use App\Models\Transaction;
use App\Models\Wallet;
use App\ViewModels\WalletViewModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use function Spatie\Snapshots\assertMatchesSnapshot;
use function Tests\configureExplorerDatabase;
use function Tests\fakeKnownWallets;

beforeEach(function () {
    configureExplorerDatabase();

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
    expect($this->subject->balance())->toBeString();

    assertMatchesSnapshot($this->subject->balance());
});

it('should get the nonce', function () {
    expect($this->subject->nonce())->toBeString();
    expect($this->subject->nonce())->toBe('1,000');
});

it('should get the balance as percentage from supply', function () {
    Http::fakeSequence()->push([
        'data' => [
            'supply' => '1000000000000',
        ],
    ]);

    expect($this->subject->balancePercentage())->toBeString();
    expect($this->subject->balancePercentage())->toBe('10%');
});

it('should get the votes', function () {
    expect($this->subject->votes())->toBeString();

    assertMatchesSnapshot($this->subject->votes());
});

it('should get the votes as percentage from supply', function () {
    Http::fakeSequence()->push([
        'data' => [
            'supply' => '1000000000000',
        ],
    ]);

    expect($this->subject->votesPercentage())->toBeString();
    expect($this->subject->votesPercentage())->toBe('10%');
});

it('should sum up the amount forged', function () {
    expect($this->subject->amountForged())->toBeString();

    assertMatchesSnapshot($this->subject->amountForged());
});

it('should sum up the fees forged', function () {
    expect($this->subject->feesForged())->toBeString();

    assertMatchesSnapshot($this->subject->feesForged());
});

it('should sum up the rewards forged', function () {
    expect($this->subject->rewardsForged())->toBeString();

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

    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'attributes' => [
            'vote' => $vote = Wallet::factory()->create()->public_key,
        ],
    ]));

    expect($this->subject->vote())->toBeInstanceOf(WalletViewModel::class);
});
