<?php

declare(strict_types=1);

use App\Models\Wallet;

use App\ViewModels\WalletViewModel;
use Illuminate\Support\Facades\Http;
use function Tests\configureExplorerDatabase;

beforeEach(function () {
    configureExplorerDatabase();

    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'balance'      => 1000 * 1e8,
        'vote_balance' => 1000 * 1e8,
    ]));
});

it('should get the balance', function () {
    expect($this->subject->balance())->toBeString();
    expect($this->subject->balance())->toBe('ARK 1,000.00');
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
