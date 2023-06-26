<?php

declare(strict_types=1);

use App\Http\Livewire\WalletBlockTable;
use App\Http\Livewire\WalletTables;
use App\Http\Livewire\WalletTransactionTable;
use App\Http\Livewire\WalletVoterTable;
use App\Models\Wallet;
use App\ViewModels\WalletViewModel;
use Livewire\Livewire;

it('should render all tabs for delegates', function () {
    $wallet = Wallet::factory()->activeDelegate()->create();

    Livewire::test(WalletTables::class, [new WalletViewModel($wallet)])
        ->assertSeeHtml("tab === 'transactions'")
        ->assertSeeHtml("tab === 'voters'");
});

it('should not render tabs for non-delegates', function () {
    $wallet = Wallet::factory()->create([
        'attributes' => [],
    ]);

    Livewire::test(WalletTables::class, [new WalletViewModel($wallet)])
        ->assertSeeHtml("tab === 'transactions'")
        ->assertDontSeeHtml("tab === 'voters'");
});

it('should change view with event', function () {
    $wallet = Wallet::factory()->activeDelegate()->create();

    Livewire::test(WalletTables::class, [new WalletViewModel($wallet)])
        ->assertSet('view', 'transactions')
        ->emit('showWalletView', 'voters')
        ->assertSet('view', 'voters')
        ->emit('showWalletView', 'blocks')
        ->assertSet('view', 'blocks')
        ->emit('showWalletView', 'transactions')
        ->assertSet('view', 'transactions');
});

it('should track querystring between tabs', function () {
    $wallet = Wallet::factory()->activeDelegate()->create();

    Livewire::test(WalletTables::class, [new WalletViewModel($wallet)])
        ->set('view', 'transactions')

        ->assertSet('tabQueryData', [
            'transactions' => [
                'page'          => 1,
                'perPage'       => WalletTransactionTable::defaultPerPage(),
                'outgoing'      => true,
                'incoming'      => true,
                'transfers'     => true,
                'votes'         => true,
                'multipayments' => true,
                'others'        => true,
            ],

            'blocks' => [
                'page'    => 1,
                'perPage' => WalletBlockTable::defaultPerPage(),
            ],

            'voters' => [
                'page'    => 1,
                'perPage' => WalletVoterTable::defaultPerPage(),
            ],
        ])

        ->set('tabQueryData.transactions.outgoing', false)
        ->assertSet('outgoing', false)
        ->set('tabQueryData.transactions.page', 2)

        ->set('view', 'blocks')
        ->assertSet('tabQueryData.blocks.page', 1)
        ->assertSet('page', 1)
        ->set('tabQueryData.blocks.page', 3)
        ->assertSet('page', 3)

        ->set('view', 'transactions')
        ->assertSet('tabQueryData.transactions.page', 2)
        ->assertSet('page', 2);
});
