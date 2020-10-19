<?php

declare(strict_types=1);

use App\Models\Wallet;
use App\ViewModels\WalletViewModel;

use function Tests\configureExplorerDatabase;

beforeEach(function () {
    configureExplorerDatabase();

    $this->subject = new WalletViewModel(Wallet::factory()->create([
        'balance'      => 1000 * 1e8,
        'vote_balance' => 2000 * 1e8,
    ]));
});

it('should get the balance', function () {
    expect($this->subject->balance())->toBeString();
    expect($this->subject->balance())->toBe('ARK 1,000.00');
});

it('should get the vote_balance', function () {
    expect($this->subject->voteBalance())->toBeString();
    expect($this->subject->voteBalance())->toBe('ARK 2,000.00');
});
