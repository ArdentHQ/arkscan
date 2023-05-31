<?php

declare(strict_types=1);

use App\Http\Livewire\Navbar\Search;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use Livewire\Livewire;

it('should search for a wallet', function () {
    $wallet      = Wallet::factory()->create();
    $otherWallet = Wallet::factory()->create();
    Transaction::factory()->create();
    $block = Block::factory()->create();
    Transaction::factory()->create(['block_id' => $block->id]);

    Livewire::test(Search::class)
        ->set('query', $wallet->address)
        ->assertSee($wallet->address)
        ->assertDontSee($otherWallet->address);
});

it('should search for a wallet username over a block generator', function () {
    $wallet = Wallet::factory()->create([
        'attributes' => [
            'delegate' => [
                'username' => 'pieface',
            ],
        ],
    ]);
    $block = Block::factory()->create([
        'generator_public_key' => $wallet->public_key,
    ]);
    Transaction::factory()->create(['block_id' => $block->id]);

    Livewire::test(Search::class)
        ->set('query', $wallet->address)
        ->assertSee($wallet->address);
});

it('should search for a transaction', function () {
    Transaction::factory()->create();
    $block = Block::factory()->create();
    Transaction::factory()->create(['block_id' => $block->id]);
    $transaction = Transaction::factory()->create();

    Livewire::test(Search::class)
        ->set('query', $transaction->id)
        ->assertSee($transaction->id);
});

it('should search for a block', function () {
    $block = Block::factory()->create();

    Livewire::test(Search::class)
        ->set('query', $block->id)
        ->assertSee($block->id);
});

it('should redirect on submit', function () {
    $block = Block::factory()->create();

    Livewire::test(Search::class)
        ->set('query', $block->id)
        ->assertSee($block->id)
        ->call('submit')
        ->assertRedirect(route('block', $block));
});

it('should do nothing if no results on submit', function () {
    $block = Block::factory()->create();

    Livewire::test(Search::class)
        ->set('query', 'non-existant block id')
        ->assertDontSee($block->id)
        ->call('submit')
        ->assertOk();
});
