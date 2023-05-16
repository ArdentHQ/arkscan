<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;

it('redirects to the correct route for transactions', function (): void {
    $transactionId = Transaction::factory()->create()->id;

    $this->get(sprintf('transaction/%s', $transactionId))
        ->assertRedirect(sprintf('transactions/%s', $transactionId));
});

it('redirects to the correct route for wallets', function (): void {
    $walletAddress = Wallet::factory()->create()->address;

    $this->get(sprintf('wallet/%s', $walletAddress))
        ->assertRedirect(sprintf('address/%s', $walletAddress));
});

it('redirects to the correct route for blocks', function (): void {
    $blockId = Block::factory()->create()->id;

    $this->get(sprintf('block/%s', $blockId))
        ->assertRedirect(sprintf('blocks/%s', $blockId));
});
