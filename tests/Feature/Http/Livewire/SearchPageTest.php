<?php

declare(strict_types=1);

use App\Enums\CoreTransactionTypeEnum;
use App\Enums\TransactionTypeGroupEnum;
use App\Http\Livewire\SearchPage;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Cache\NetworkCache;
use Livewire\Livewire;
use function Tests\configureExplorerDatabase;

beforeEach(function () {
    configureExplorerDatabase();

    (new NetworkCache())->setSupply(strval(10e8));
});

it('should search for blocks', function () {
    $block = Block::factory()->create();

    Livewire::test(SearchPage::class)
        ->set('state.type', 'block')
        ->set('state.term', $block->id)
        ->call('performSearch')
        ->assertSee($block->id);
});

it('should search for transactions', function () {
    $transaction = Transaction::factory()->create([
        'type'       => CoreTransactionTypeEnum::TRANSFER,
        'type_group' => TransactionTypeGroupEnum::CORE,
    ]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'transaction')
        ->set('state.term', $transaction->id)
        ->call('performSearch')
        ->assertSee($transaction->id);
});

it('should search for wallets', function () {
    $wallet = Wallet::factory()->create();

    Livewire::test(SearchPage::class)
        ->set('state.type', 'wallet')
        ->set('state.term', $wallet->address)
        ->call('performSearch')
        ->assertSee($wallet->address);
});
