<?php

declare(strict_types=1);

use App\Http\Livewire\WalletBlockTable;
use App\Http\Livewire\WalletTables;
use App\Http\Livewire\WalletTransactionTable;
use App\Http\Livewire\WalletVoterTable;
use App\Livewire\SupportQueryString;
use App\Models\Wallet;
use App\ViewModels\WalletViewModel;
use Livewire\Features\SupportLifecycleHooks\SupportLifecycleHooks;
use Livewire\Livewire;

it('should render all tabs for validators', function () {
    $wallet = Wallet::factory()->activeValidator()->create();

    Livewire::test(WalletTables::class, [new WalletViewModel($wallet)])
        ->assertSeeHtml("tab === 'transactions'")
        ->assertSeeHtml("tab === 'voters'");
});

it('should not render tabs for non-validators', function () {
    $wallet = Wallet::factory()->create([
        'attributes' => [],
    ]);

    Livewire::test(WalletTables::class, [new WalletViewModel($wallet)])
        ->assertSeeHtml("tab === 'transactions'")
        ->assertDontSeeHtml("tab === 'voters'");
});

it('should change view with event', function () {
    $wallet = Wallet::factory()->activeValidator()->create();

    Livewire::test(WalletTables::class, [new WalletViewModel($wallet)])
        ->assertSet('view', 'transactions')
        ->dispatch('showWalletView', 'voters')
        ->assertSet('view', 'voters')
        ->dispatch('showWalletView', 'blocks')
        ->assertSet('view', 'blocks')
        ->dispatch('showWalletView', 'transactions')
        ->assertSet('view', 'transactions');
});

it('should track querystring between tabs', function () {
    $wallet = Wallet::factory()->activeValidator()->create();

    $component = Livewire::test(WalletTables::class, [new WalletViewModel($wallet)])
        ->set('view', 'transactions')

        ->assertSet('tabQueryData', [
            'transactions' => [
                'paginators.page' => 1,
                'perPage'         => WalletTransactionTable::defaultPerPage(),
                'outgoing'        => true,
                'incoming'        => true,
                'transfers'       => true,
                'votes'           => true,
                'others'          => true,
            ],

            'blocks' => [
                'paginators.page' => 1,
                'perPage'         => WalletBlockTable::defaultPerPage(),
            ],

            'voters' => [
                'paginators.page' => 1,
                'perPage'         => WalletVoterTable::defaultPerPage(),
            ],
        ])

        ->assertSet('outgoing', true)
        ->set('tabQueryData.transactions.outgoing', false)
        ->assertSet('outgoing', false)
        ->call('gotoPage', 2)
        ->assertSet('savedQueryData.transactions.outgoing', false);

    expect($component->instance()->tabQueryData['transactions']['paginators.page'])->toBe(2);
    expect($component->instance()->paginators['page'])->toBe(2);

    $component->set('view', 'blocks');

    expect($component->instance()->tabQueryData['blocks']['paginators.page'])->toBe(1);
    expect($component->instance()->paginators['page'])->toBe(1);

    $component->call('gotoPage', 3)
        ->set('view', 'transactions')
        ->assertSet('tabQueryData.transactions.outgoing', false)
        ->set('view', 'blocks')
        ->assertSet('paginators.page', 3)
        ->set('view', 'voters');

    expect($component->instance()->tabQueryData['voters']['paginators.page'])->toBe(1);
    expect($component->instance()->paginators['page'])->toBe(1);

    $component->call('gotoPage', 4)
        ->set('view', 'transactions')
        ->set('view', 'voters')
        ->assertSet('paginators.page', 4)

        ->assertSet('savedQueryData.transactions.outgoing', false)
        ->set('view', 'transactions')
        ->assertSet('outgoing', null)

        ->set('view', 'transactions');

    expect($component->instance()->tabQueryData['transactions']['paginators.page'])->toBe(2);
    expect($component->instance()->paginators['page'])->toBe(2);
});

// it('should be able to get the property of the previous view', function () {
//     $wallet = Wallet::factory()->activeValidator()->create();

//     $instance = Livewire::test(WalletTables::class, [new WalletViewModel($wallet)])
//         ->set('view', 'transactions')
//         ->instance();

//     $instance->outgoing = false;

//     expect($instance->tabQueryData['transactions']['outgoing'])->toBeFalse();

//     $instance->updatingView('voters');
//     $instance->showWalletView('voters');
//     $instance->updatedView();

//     expect($instance->outgoing)->toBeTrue();
//     expect($instance->tabQueryData['transactions']['outgoing'])->toBeTrue();
// });

it('should try to get property if not part of the querystring properties', function () {
    $wallet = Wallet::factory()->activeValidator()->create();

    Livewire::test(WalletTables::class, [new WalletViewModel($wallet)])
        ->set('view', 'transactions')
        ->assertSet('testProperty', null);
});

it('should trigger is ready event for current tab view', function () {
    $wallet = Wallet::factory()->activeValidator()->create();

    Livewire::test(WalletTables::class, [new WalletViewModel($wallet)])
        ->call('triggerViewIsReady')
        ->assertDispatched('setTransactionsReady');
});

it('should trigger is ready event when changing tab view', function () {
    $wallet = Wallet::factory()->activeValidator()->create();

    Livewire::test(WalletTables::class, [new WalletViewModel($wallet)])
        ->call('triggerViewIsReady')
        ->assertDispatched('setTransactionsReady')
        ->set('view', 'blocks')
        ->assertDispatched('setBlocksReady');
});

it('should not trigger is ready event if tab view does not exist', function () {
    $wallet = Wallet::factory()->activeValidator()->create();

    Livewire::test(WalletTables::class, [new WalletViewModel($wallet)])
        ->set('view', 'testing')
        ->assertNotDispatched('setTestingReady');
});

it('should not trigger is ready event more than once', function () {
    $wallet = Wallet::factory()->activeValidator()->create();

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
    $wallet = Wallet::factory()->activeValidator()->create();

    Livewire::withQueryParams(['perPage' => 1234])
        ->test(WalletTables::class, [new WalletViewModel($wallet)])
        ->call('triggerViewIsReady')
        ->assertSet('tabQueryData.transactions.perPage', 25);
});

it('should not update initial page if view does not exist', function () {
    $wallet = Wallet::factory()->activeValidator()->create();

    $instance = Livewire::test(WalletTables::class, [new WalletViewModel($wallet)])
        ->set('view', 'testing')
        ->set('tabQueryData', [])
        ->instance();

    $support = new SupportLifecycleHooks();
    $support->setComponent($instance);
    $support->mount([new WalletViewModel($wallet)]);

    expect($instance->tabQueryData)->toBe([
        'transactions' => [
            'paginators.page' => 1,
            'perPage'         => WalletTransactionTable::defaultPerPage(),
            'outgoing'        => true,
            'incoming'        => true,
            'transfers'       => true,
            'votes'           => true,
            'others'          => true,
        ],

        'blocks' => [
            'paginators.page' => 1,
            'perPage'         => WalletBlockTable::defaultPerPage(),
        ],

        'voters' => [
            'paginators.page' => 1,
            'perPage'         => WalletVoterTable::defaultPerPage(),
        ],
    ]);
});

it('should parse perPage from URL', function () {
    $wallet = Wallet::factory()->activeValidator()->create();

    Livewire::withQueryParams(['perPage' => 10])
        ->test(WalletTables::class, [new WalletViewModel($wallet)])
        ->assertSet('perPage', 10)
        ->assertSet('tabQueryData.transactions.perPage', 10);
});

it('should apply url values to component', function () {
    $wallet = Wallet::factory()->activeValidator()->create();

    $instance = Livewire::withUrlParams(['view' => 'blocks', 'page' => 3])
        ->test(WalletTables::class, [new WalletViewModel($wallet)])
        ->assertSet('view', 'blocks')
        ->instance();

    $support = new SupportQueryString();
    $support->setComponent($instance);
    $support->mergeQueryStringWithRequest();

    expect($instance->view)->toBe('blocks');
    expect($instance->getPage())->toBe(3);
    expect($instance->paginators['page'])->toBe(3);
});

it('should run hooks when property is updated with syncInput', function () {
    $wallet = Wallet::factory()->activeValidator()->create();

    $component = Livewire::test(WalletTables::class, [new WalletViewModel($wallet)]);
    $instance  = $component->instance();

    $support = new SupportLifecycleHooks();
    $support->setComponent($instance);
    $support->mount([new WalletViewModel($wallet)]);

    expect($instance->alreadyLoadedViews['voters'])->toBeFalse();

    $instance->syncInput('view', 'voters');

    expect($instance->alreadyLoadedViews['voters'])->toBeTrue();
});
