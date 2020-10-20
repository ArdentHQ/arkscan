<?php

declare(strict_types=1);

use App\Models\Block;

use App\Models\Wallet;
use App\ViewModels\WalletViewModel;
use Illuminate\Support\Facades\Http;
use function Tests\configureExplorerDatabase;
use function Tests\fakeKnownWallets;

beforeEach(function () {
    configureExplorerDatabase();

    $wallet = Wallet::factory()->create([
        'balance'      => 1000 * 1e8,
        'nonce'        => 1000,
        'vote_balance' => 1000 * 1e8,
    ]);

    $this->subject = new WalletViewModel($wallet);

    Block::factory()->create([
        'total_amount'         => 10 * 1e8,
        'total_fee'            => 8 * 1e8,
        'reward'               => 2 * 1e8,
        'generator_public_key' => $wallet->public_key,
    ]);
});

it('should get the balance', function () {
    expect($this->subject->balance())->toBeString();
    expect($this->subject->balance())->toBe('ARK 1,000.00');
});

it('should get the nonce', function () {
    expect($this->subject->nonce())->toBeString();
    expect($this->subject->nonce())->toBe('1,000');
});

it('should get the balance as percentage from supply', function () {
    Http::fakeSequence()->push([
        'data' => [
            'supply' => 10000 * 1e8,
        ],
    ]);

    expect($this->subject->balancePercentage())->toBeFloat();
    expect($this->subject->balancePercentage())->toBe(10.0);
});

it('should get the votes', function () {
    expect($this->subject->votes())->toBeString();
    expect($this->subject->votes())->toBe('ARK 1,000.00');
});

it('should get the votes as percentage from supply', function () {
    Http::fakeSequence()->push([
        'data' => [
            'supply' => 10000 * 1e8,
        ],
    ]);

    expect($this->subject->votesPercentage())->toBeFloat();
    expect($this->subject->votesPercentage())->toBe(10.0);
});

it('should generate a QR Code', function () {
    expect($this->subject->qrCode())->toBeString();
    expect($this->subject->qrCode())->toContain('<svg');
});

it('should sum up the amount forged', function () {
    expect($this->subject->amountForged())->toBeString();
    expect($this->subject->amountForged())->toBe('ARK 10.00');
});

it('should sum up the fees forged', function () {
    expect($this->subject->feesForged())->toBeString();
    expect($this->subject->feesForged())->toBe('ARK 8.00');
});

it('should sum up the rewards forged', function () {
    expect($this->subject->rewardsForged())->toBeString();
    expect($this->subject->rewardsForged())->toBe('ARK 2.00');
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
