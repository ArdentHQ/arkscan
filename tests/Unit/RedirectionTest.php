<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;

it('redirects to the correct route for transactions', function (): void {
    $transactionHash = Transaction::factory()->create()->hash;

    $this->get(sprintf('transaction/%s', $transactionHash))
        ->assertRedirect(sprintf('transactions/%s', $transactionHash));
});

it('redirects to the correct route for wallets', function (): void {
    $walletAddress = Wallet::factory()->create()->address;

    $this->get(sprintf('wallet/%s', $walletAddress))
        ->assertRedirect(sprintf('addresses/%s', $walletAddress));
});

it('redirects to the correct route for blocks', function (): void {
    $blockHash = Block::factory()->create()->hash;

    $this->get(sprintf('block/%s', $blockHash))
        ->assertRedirect(sprintf('blocks/%s', $blockHash));
});
