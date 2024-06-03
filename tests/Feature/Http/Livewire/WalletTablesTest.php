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

        ->set('view', 'voters')
        ->assertSet('tabQueryData.voters.page', 1)
        ->assertSet('page', 1)
        ->set('tabQueryData.voters.page', 4)
        ->assertSet('page', 4)

        ->assertSet('savedQueryData.transactions.outgoing', false)
        ->assertSet('outgoing', null)

        ->set('view', 'transactions')
        ->assertSet('tabQueryData.transactions.page', 2)
        ->assertSet('page', 2);
});

it('should be able to get the property of the previous view', function () {
    $wallet = Wallet::factory()->activeDelegate()->create();

    $instance = Livewire::test(WalletTables::class, [new WalletViewModel($wallet)])
        ->instance();

    $instance->outgoing = false;

    expect($instance->tabQueryData['transactions']['outgoing'])->toBeFalse();

    $instance->updatingView('voters');
    $instance->showWalletView('voters');
    $instance->updatedView();

    expect($instance->outgoing)->toBeTrue();
    expect($instance->tabQueryData['transactions']['outgoing'])->toBeTrue();
});

it('should try to get property if not part of the querystring properties', function () {
    $wallet = Wallet::factory()->activeDelegate()->create();

    Livewire::test(WalletTables::class, [new WalletViewModel($wallet)])
        ->set('view', 'transactions')
        ->assertSet('testProperty', null);
});

it('should trigger is ready event for current tab view', function () {
    $wallet = Wallet::factory()->activeDelegate()->create();

    Livewire::test(WalletTables::class, [new WalletViewModel($wallet)])
        ->call('triggerViewIsReady')
        ->assertDispatched('setTransactionsReady');
});

it('should trigger is ready event when changing tab view', function () {
    $wallet = Wallet::factory()->activeDelegate()->create();

    Livewire::test(WalletTables::class, [new WalletViewModel($wallet)])
        ->call('triggerViewIsReady')
        ->assertDispatched('setTransactionsReady')
        ->set('view', 'blocks')
        ->assertDispatched('setBlocksReady');
});

it('should not trigger is ready event if tab view does not exist', function () {
    $wallet = Wallet::factory()->activeDelegate()->create();

    Livewire::test(WalletTables::class, [new WalletViewModel($wallet)])
        ->set('view', 'testing')
        ->assertNotDispatched('setTestingReady');
});

it('should not trigger is ready event more than once', function () {
    $wallet = Wallet::factory()->activeDelegate()->create();

    Livewire::test(WalletTables::class, [new WalletViewModel($wallet)])
        ->call('triggerViewIsReady')
        ->assertDispatched('setTransactionsReady')
        ->call('triggerViewIsReady')
        ->assertNotDispatched('setTransactionsReady')
        ->call('triggerViewIsReady')
        ->assertNotDispatched('setTransactionsReady')
        ->set('view', 'blocks')
        ->assertDispatched('setBlocksReady')
        ->set('view', 'transactions')
        ->assertNotDispatched('setTransactionsReady');
});

it('should not allow invalid per page value', function () {
    $wallet = Wallet::factory()->activeDelegate()->create();

    Livewire::test(WalletTables::class, [new WalletViewModel($wallet)])
        ->set('tabQueryData.transactions.perPage', 1234)
        ->call('triggerViewIsReady')
        ->assertSet('tabQueryData.transactions.perPage', 25);
});

it('should not update initial page if view does not exist', function () {
    $wallet = Wallet::factory()->activeDelegate()->create();

    $instance = Livewire::test(WalletTables::class, [new WalletViewModel($wallet)])
        ->set('view', 'testing')
        ->set('tabQueryData', [])
        ->instance();

    $instance->boot();

    expect($instance->tabQueryData)->toBe([
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
    ]);
});
