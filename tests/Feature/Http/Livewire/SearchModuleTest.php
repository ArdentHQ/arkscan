<?php

declare(strict_types=1);

use App\Http\Livewire\SearchModule;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use Livewire\Livewire;

it('should search for a wallet and redirect', function () {
    $wallet = Wallet::factory()->create();

    Livewire::test(SearchModule::class)
        ->set('state.term', $wallet->address)
        ->set('state.type', 'wallet')
        ->call('performSearch')
        ->assertRedirect(route('wallet', $wallet->address));
});

it('should search for a transaction and redirect', function () {
    $transaction = Transaction::factory()->create();

    Livewire::test(SearchModule::class)
        ->set('state.term', $transaction->id)
        ->set('state.type', 'transaction')
        ->call('performSearch')
        ->assertRedirect(route('transaction', $transaction->id));
});

it('should search for a block and redirect', function () {
    $block = Block::factory()->create();

    Livewire::test(SearchModule::class)
        ->set('state.term', $block->id)
        ->set('state.type', 'block')
        ->call('performSearch')
        ->assertRedirect(route('block', $block->id));
});

it('should redirect to the advanced search page if there are no results', function () {
    Livewire::test(SearchModule::class)
        ->set('state.term', 'unknown')
        ->set('state.type', 'block')
        ->call('performSearch')
        ->assertRedirect(route('search', [
            'state[term]' => 'unknown',
            'state[type]' => 'block',
        ]));
});

it('should redirect to the advanced search page if the term is null', function () {
    Livewire::test(SearchModule::class)
        ->set('state.term', null)
        ->set('state.type', 'block')
        ->call('performSearch')
        ->assertRedirect(route('search', [
            'state[term]' => null,
            'state[type]' => 'block',
        ]));
});

it('should redirect to the advanced search page if the term is empty', function () {
    Livewire::test(SearchModule::class)
        ->set('state.term', '')
        ->set('state.type', 'block')
        ->call('performSearch')
        ->assertRedirect(route('search', [
            'state[term]' => '',
            'state[type]' => 'block',
        ]));
});

it('should redirect to the advanced search page if there are more than 2 criteria', function () {
    Livewire::test(SearchModule::class)
        ->set('state.term', 'address')
        ->set('state.type', 'transaction')
        ->set('state.amountFrom', 1)
        ->call('performSearch')
        ->assertRedirect(route('search', [
            'state[term]'       => 'address',
            'state[type]'       => 'transaction',
            'state[amountFrom]' => 1,
        ]));
});
