<?php

declare(strict_types=1);

use App\Http\Livewire\SearchModal;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Arr;
use Livewire\Livewire;

class SearchModalExceptionStub extends \Livewire\Component
{
    use App\Http\Livewire\Concerns\ManagesSearch;
    use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasModal;
    use App\Http\Livewire\Concerns\HandlesSearchModal;
}

it('should search for a wallet and redirect', function () {
    $wallet = Wallet::factory()->create();
    Transaction::factory()->create();
    $block = Block::factory()->create();
    Transaction::factory()->create(['block_id' => $block->id]);

    Livewire::test(SearchModal::class)
        ->set('state.term', $wallet->address)
        ->set('state.type', 'wallet')
        ->call('performSearch')
        ->assertEmitted('redirectToPage', 'wallet', $wallet->address);
});

it('should search redirect when event emitted', function () {
    $wallet = Wallet::factory()->create();

    Livewire::test(SearchModal::class)
        ->emit('redirectToPage', 'wallet', $wallet->address)
        ->assertRedirect(route('wallet', $wallet->address));
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

    Livewire::test(SearchModal::class)
        ->set('state.term', Arr::get($wallet, 'attributes.delegate.username'))
        ->call('performSearch')
        ->assertEmitted('redirectToPage', 'wallet', $wallet->address);
});

it('should do a basic search for a transaction and redirect', function () {
    Transaction::factory()->create();
    $block = Block::factory()->create();
    Transaction::factory()->create(['block_id' => $block->id]);
    $transaction = Transaction::factory()->create();

    Livewire::test(SearchModal::class)
        ->set('state.term', $transaction->id)
        ->call('performSearch')
        ->assertEmitted('redirectToPage', 'transaction', $transaction->id);
});

it('should flag as not redirecting on exception', function () {
    $wallet = Wallet::factory()->create();

    $mock = $this->mock(SearchModalExceptionStub::class)->makePartial();
    $mock->shouldAllowMockingProtectedMethods()
        ->shouldReceive('searchWallet')
        ->andThrow(new \Exception('Failed to search for wallet'))
        ->once();

    $mock->state['term'] = $wallet->address;
    $mock->performSearch();
});

it('should flag as redirecting', function () {
    $wallet = Wallet::factory()->create();

    Livewire::test(SearchModal::class)
        ->set('state.term', $wallet->address)
        ->set('state.type', 'wallet')
        ->call('performSearch')
        ->assertSet('isRedirecting', true);
});

it('should do an advanced search for a transaction and redirect', function () {
    Transaction::factory()->create();
    $block = Block::factory()->create();
    Transaction::factory()->create(['block_id' => $block->id]);
    $transaction = Transaction::factory()->create();

    Livewire::test(SearchModal::class)
        ->set('state.term', $transaction->id)
        ->set('state.type', 'transaction')
        ->call('performSearch')
        ->assertEmitted('redirectToPage', 'transaction', $transaction->id);
});

it('should do a basic search for a block and redirect', function () {
    Transaction::factory()->create();
    $block = Block::factory()->create();
    Transaction::factory()->create(['block_id' => $block->id]);

    Livewire::test(SearchModal::class)
        ->set('state.term', $block->id)
        ->call('performSearch')
        ->assertEmitted('redirectToPage', 'block', $block->id);
});

it('should do an advanced search for a block and redirect', function () {
    Transaction::factory()->create();
    $block = Block::factory()->create();
    Transaction::factory()->create(['block_id' => $block->id]);

    Livewire::test(SearchModal::class)
        ->set('state.term', $block->id)
        ->set('state.type', 'block')
        ->call('performSearch')
        ->assertEmitted('redirectToPage', 'block', $block->id);
});

it('should redirect to the basic search page if there are no results', function () {
    Livewire::test(SearchModal::class)
        ->set('state.term', 'unknown')
        ->call('performSearch')
        ->assertEmitted('redirectToPage', 'search', [
            'state' => [
                'term' => 'unknown',
                'type' => 'block',
            ],
            'advanced' => 'false',
        ]);
});

it('should redirect to the advanced search page if there are no results', function () {
    Livewire::test(SearchModal::class)
        ->set('state.term', 'unknown')
        ->set('state.type', 'block')
        ->set('isAdvanced', true)
        ->call('performSearch')
        ->assertEmitted('redirectToPage', 'search', [
            'state' => [
                'term' => 'unknown',
                'type' => 'block',
            ],
            'advanced' => 'true',
        ]);
});

it('should redirect to the basic search page if the term is null', function () {
    Livewire::test(SearchModal::class)
        ->set('state.term', null)
        ->set('state.type', 'block')
        ->call('performSearch')
        ->assertEmitted('redirectToPage', 'search', [
            'state' => [
                'term' => null,
                'type' => 'block',
            ],
            'advanced' => 'false',
        ]);
});

it('should redirect to the advanced search page if the term is null', function () {
    Livewire::test(SearchModal::class)
        ->set('state.term', null)
        ->set('state.type', 'block')
        ->set('isAdvanced', true)
        ->call('performSearch')
        ->assertEmitted('redirectToPage', 'search', [
            'state' => [
                'term' => null,
                'type' => 'block',
            ],
            'advanced' => 'true',
        ]);
});

it('should redirect to the basic search page if the term is empty', function () {
    Livewire::test(SearchModal::class)
        ->set('state.term', '')
        ->set('state.type', 'block')
        ->call('performSearch')
        ->assertEmitted('redirectToPage', 'search', [
            'state' => [
                'term' => '',
                'type' => 'block',
            ],
            'advanced' => 'false',
        ]);
});

it('should redirect to the advanced search page if the term is empty', function () {
    Livewire::test(SearchModal::class)
        ->set('state.term', '')
        ->set('state.type', 'block')
        ->set('isAdvanced', true)
        ->call('performSearch')
        ->assertEmitted('redirectToPage', 'search', [
            'state' => [
                'term' => '',
                'type' => 'block',
            ],
            'advanced' => 'true',
        ]);
});

it('should redirect to the advanced search page if there are more than 2 criteria', function () {
    Livewire::test(SearchModal::class)
        ->set('state.term', 'address')
        ->set('state.type', 'transaction')
        ->set('isAdvanced', true)
        ->set('state.amountFrom', 1)
        ->call('performSearch')
        ->assertEmitted('redirectToPage', 'search', [
            'state' => [
                'term'       => 'address',
                'type'       => 'transaction',
                'amountFrom' => 1,
            ],
            'advanced' => 'true',
        ]);
});

it('should toggle advanced filters', function () {
    Livewire::test(SearchModal::class)
        ->assertSet('isAdvanced', false)
        ->call('toggleAdvanced')
        ->assertSet('isAdvanced', true);
});
