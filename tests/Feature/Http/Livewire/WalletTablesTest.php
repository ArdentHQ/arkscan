<?php

declare(strict_types=1);

use App\Http\Livewire\WalletTables;
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

it('should sync input for non-existent property value', function () {
    $wallet = Wallet::factory()->activeValidator()->create();

    Livewire::test(WalletTables::class, [new WalletViewModel($wallet)])
        ->set('view', 'validators')
        ->call('syncInput', 'testProperty', true)
        ->assertSet('testProperty', true);
});
