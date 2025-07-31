<?php

declare(strict_types=1);

use App\Http\Livewire\Wallet\Tabs;
use App\Livewire\SupportQueryString;
use App\Models\Wallet;
use App\ViewModels\WalletViewModel;
use Livewire\Features\SupportLifecycleHooks\SupportLifecycleHooks;
use Livewire\Livewire;

it('should render all tabs for validators', function () {
    $wallet = Wallet::factory()->activeValidator()->create();

    Livewire::test(Tabs::class, [new WalletViewModel($wallet)])
        ->assertSeeHtml("tab === 'transactions'")
        ->assertSeeHtml("tab === 'voters'");
});

it('should not render tabs for non-validators', function () {
    $wallet = Wallet::factory()->create([
        'attributes' => [],
    ]);

    Livewire::test(Tabs::class, [new WalletViewModel($wallet)])
        ->assertSeeHtml("tab === 'transactions'")
        ->assertDontSeeHtml("tab === 'voters'");
});

it('should change view with event', function () {
    $wallet = Wallet::factory()->activeValidator()->create();

    Livewire::test(Tabs::class, [new WalletViewModel($wallet)])
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

    $instance = Livewire::test(Tabs::class, [new WalletViewModel($wallet)])
        ->set('view', 'transactions')

        ->assertSet('tabQueryData', [
            'transactions' => [
                'paginators.transactions'                  => 1,
                'paginatorsPerPage.transactions'           => config('arkscan.pagination.per_page'),
                'filters.transactions.outgoing'            => true,
                'filters.transactions.incoming'            => true,
                'filters.transactions.transfers'           => true,
                'filters.transactions.votes'               => true,
                'filters.transactions.others'              => true,
                'filters.transactions.multipayments'       => true,
                'filters.transactions.validator'           => true,
                'filters.transactions.username'            => true,
                'filters.transactions.contract_deployment' => true,
            ],

            'blocks' => [
                'paginators.blocks'                => 1,
                'paginatorsPerPage.blocks'         => config('arkscan.pagination.per_page'),
            ],

            'voters' => [
                'paginators.voters'                => 1,
                'paginatorsPerPage.voters'         => config('arkscan.pagination.per_page'),
            ],
        ])
        ->instance();

    $instance->tabQueryData['transactions']['filters.transactions.outgoing'] = false;

    expect($instance->tabQueryData['transactions']['filters.transactions.outgoing'])->toBeFalse();

    $instance->tabQueryData['transactions']['paginators.transactions'] = 2;

    $instance->syncInput('view', 'blocks');

    expect($instance->tabQueryData['blocks']['paginators.blocks'])->toBe(1);
    expect($instance->paginators['blocks'])->toBe(1);

    $instance->tabQueryData['blocks']['paginators.blocks'] = 3;

    $instance->syncInput('view', 'transactions');
    $instance->syncInput('view', 'blocks');

    expect($instance->paginators['blocks'])->toBe(3);

    $instance->syncInput('view', 'voters');

    expect($instance->tabQueryData['voters']['paginators.voters'])->toBe(1);
    expect($instance->paginators['voters'])->toBe(1);

    $instance->tabQueryData['voters']['paginators.voters'] = 4;

    $instance->syncInput('view', 'transactions');
    $instance->syncInput('view', 'voters');

    expect($instance->paginators['voters'])->toBe(4);

    expect($instance->savedQueryData['transactions']['filters.transactions.outgoing'])->toBeFalse();

    $instance->syncInput('view', 'transactions');

    expect($instance->tabQueryData['transactions']['paginators.transactions'])->toBe(2);
    expect($instance->paginators['transactions'])->toBe(2);
});

it('should be able to get the property of the previous view', function () {
    $wallet = Wallet::factory()->activeValidator()->create();

    $instance = Livewire::test(Tabs::class, [new WalletViewModel($wallet)])
        ->set('view', 'transactions')
        ->instance();

    $instance->setFilter('outgoing', false, 'transactions');

    expect($instance->tabQueryData['transactions']['filters.transactions.outgoing'])->toBeFalse();
    expect($instance->getFilter('outgoing', 'unused-property-value'))->toBeFalse();

    $instance->updatingView('voters');
    $instance->showWalletView('voters');
    $instance->updatedView();

    expect($instance->filters['transactions']['outgoing'])->toBeTrue();
    expect($instance->tabQueryData['transactions']['filters.transactions.outgoing'])->toBeTrue();
});

it('should return null if no filter does not exist on current view', function () {
    $wallet = Wallet::factory()->activeValidator()->create();

    $instance = Livewire::test(Tabs::class, [new WalletViewModel($wallet)])
        ->set('view', 'transactions')
        ->instance();

    $instance->setFilter('outgoing', false, 'transactions');

    expect($instance->tabQueryData['transactions']['filters.transactions.outgoing'])->toBeFalse();
    expect($instance->getFilter('outgoing', 'unused-property-value'))->toBeFalse();

    $instance->updatingView('voters');
    $instance->showWalletView('voters');
    $instance->updatedView();

    expect($instance->filters['transactions']['outgoing'])->toBeTrue();
    expect($instance->tabQueryData['transactions']['filters.transactions.outgoing'])->toBeTrue();
    expect($instance->getFilter('outgoing', 'unused-property-value'))->toBeNull(); // Transactions filter does not exist on voters view
});

it('should try to get property if not part of the querystring properties', function () {
    $wallet = Wallet::factory()->activeValidator()->create();

    Livewire::test(Tabs::class, [new WalletViewModel($wallet)])
        ->set('view', 'transactions')
        ->assertSet('testProperty', null);
});

it('should trigger is ready event for current tab view', function () {
    $wallet = Wallet::factory()->activeValidator()->create();

    Livewire::test(Tabs::class, [new WalletViewModel($wallet)])
        ->call('triggerViewIsReady')
        ->assertDispatched('setTransactionsReady');
});

it('should trigger is ready event when changing tab view', function () {
    $wallet = Wallet::factory()->activeValidator()->create();

    Livewire::test(Tabs::class, [new WalletViewModel($wallet)])
        ->call('triggerViewIsReady')
        ->assertDispatched('setTransactionsReady')
        ->set('view', 'blocks')
        ->assertDispatched('setBlocksReady');
});

it('should not trigger is ready event more than once', function () {
    $wallet = Wallet::factory()->activeValidator()->create();

    Livewire::test(Tabs::class, [new WalletViewModel($wallet)])
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

    $instance = Livewire::test(Tabs::class, [new WalletViewModel($wallet)])
        ->set('tabQueryData.transactions.perPage', 1234)
        ->call('triggerViewIsReady')
        ->instance();

    expect($instance->tabQueryData['transactions']['paginatorsPerPage.transactions'])->toBe(config('arkscan.pagination.per_page'));
});

it('should parse perPage from URL', function () {
    $wallet = Wallet::factory()->activeValidator()->create();

    $instance = Livewire::withQueryParams(['per-page' => 10])
        ->test(Tabs::class, [new WalletViewModel($wallet)])
        ->assertSet('paginatorsPerPage.transactions', 10)
        ->instance();

    expect($instance->tabQueryData['transactions']['paginatorsPerPage.transactions'])->toBe(10);
});

it('should apply url values to component', function () {
    $wallet = Wallet::factory()->activeValidator()->create();

    $instance = Livewire::withUrlParams(['view' => 'blocks', 'page' => 3])
        ->test(Tabs::class, [new WalletViewModel($wallet)])
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

    $component = Livewire::test(Tabs::class, [new WalletViewModel($wallet)]);
    $instance  = $component->instance();

    $support = new SupportLifecycleHooks();
    $support->setComponent($instance);
    $support->mount([new WalletViewModel($wallet)]);

    expect($instance->alreadyLoadedViews['voters'])->toBeFalse();

    $instance->syncInput('view', 'voters');

    expect($instance->alreadyLoadedViews['voters'])->toBeTrue();
});

it('should revert to transactions tab with unknown view', function () {
    $wallet = Wallet::factory()->activeValidator()->create();

    $this->get('/addresses/'.$wallet->address.'?view=unknown')
        ->assertOk();
});

it('should not have sorting querystring data', function () {
    $wallet = Wallet::factory()->activeValidator()->create();

    $instance = Livewire::test(Tabs::class, [new WalletViewModel($wallet)])
        ->instance();

    expect($instance->queryStringHasTableSorting())->toBe([]);
});

it('should get and set per-page for current view', function () {
    $wallet = Wallet::factory()->activeValidator()->create();

    $component = Livewire::test(Tabs::class, [new WalletViewModel($wallet)])
        ->set('view', 'voters')
        ->call('setPerPage', 10)
        ->assertSet('paginatorsPerPage.voters', 10)
        ->assertSet('perPage', 10)
        ->set('view', 'blocks')
        ->call('setPerPage', 50)
        ->assertSet('paginatorsPerPage.blocks', 50)
        ->assertSet('perPage', 50);

    expect($component->tabQueryData['blocks']['paginatorsPerPage.blocks'])->toBe(50);
    expect($component->savedQueryData['voters']['paginatorsPerPage.voters'])->toBe(10);
});

it('should return false for isReady if view method does not exist', function () {
    $wallet = Wallet::factory()->activeValidator()->create();

    $instance = Livewire::test(Tabs::class, [new WalletViewModel($wallet)])
        ->set('view', 'testing')
        ->assertSet('isReady', false)
        ->instance();

    expect($instance->getIsReadyProperty())->toBeFalse();
});

it('should get and set page for current view', function () {
    $wallet = Wallet::factory()->activeValidator()->create();

    $component = Livewire::test(Tabs::class, [new WalletViewModel($wallet)])
        ->set('view', 'voters')
        ->call('setPage', 3)
        ->assertSet('paginators.voters', 3)
        ->assertSet('page', 3)
        ->set('view', 'blocks')
        ->call('setPage', 8)
        ->assertSet('paginators.blocks', 8)
        ->assertSet('page', 8);

    expect($component->tabQueryData['blocks']['paginators.blocks'])->toBe(8);
    expect($component->savedQueryData['voters']['paginators.voters'])->toBe(3);
});

it('should ignore unknown tab query data', function () {
    $wallet = Wallet::factory()->activeValidator()->create();

    Livewire::test(Tabs::class, [new WalletViewModel($wallet)])
        ->set('view', 'voters')
        ->set('tabQueryData.voters.testing', 123)
        ->set('view', 'blocks')
        ->assertSet('savedQueryData.voters.testing', 123);
});
