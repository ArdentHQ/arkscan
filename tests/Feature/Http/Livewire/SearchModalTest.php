<?php

declare(strict_types=1);

use App\Http\Livewire\SearchModal;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use Livewire\Livewire;

it('should search for a wallet', function () {
    $wallet      = Wallet::factory()->create();
    $otherWallet = Wallet::factory()->create();
    Transaction::factory()->create();
    $block = Block::factory()->create();
    Transaction::factory()->create(['block_hash' => $block->hash]);

    Livewire::test(SearchModal::class)
        ->dispatch('openSearchModal')
        ->set('query', $wallet->address)
        ->assertSee($wallet->address)
        ->assertDontSee($otherWallet->address);
});

it('should search for a wallet username over a block generator', function () {
    $wallet = Wallet::factory()->create([
        'attributes' => [
            'username' => 'pieface',
        ],
    ]);
    $block = Block::factory()->create([
        'proposer' => $wallet->address,
    ]);
    Transaction::factory()->create(['block_hash' => $block->hash]);

    Livewire::test(SearchModal::class)
        ->dispatch('openSearchModal')
        ->set('query', $wallet->address)
        ->assertSee($wallet->address);
});

it('should search for a transaction', function () {
    Transaction::factory()->create();
    $block = Block::factory()->create();
    Transaction::factory()->create(['block_hash' => $block->hash]);
    $transaction = Transaction::factory()->create();

    Livewire::test(SearchModal::class)
        ->dispatch('openSearchModal')
        ->set('query', $transaction->hash)
        ->assertSee($transaction->hash);
});

it('should search for a block', function () {
    $block = Block::factory()->create();

    Livewire::test(SearchModal::class)
        ->dispatch('openSearchModal')
        ->set('query', $block->hash)
        ->assertSee($block->hash);
});

it('should clear search when modal is closed', function () {
    $block = Block::factory()->create();

    Livewire::test(SearchModal::class)
        ->dispatch('openSearchModal')
        ->set('query', $block->hash)
        ->call('closeModal')
        ->assertSet('query', '');
});
