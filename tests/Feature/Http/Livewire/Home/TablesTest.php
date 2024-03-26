<?php

declare(strict_types=1);

use App\Http\Livewire\Home\Tables;
use App\Models\Wallet;
use Livewire\Livewire;

it('should render all tabs', function () {
    Wallet::factory()->activeValidator()->create();

    Livewire::test(Tables::class)
        ->assertSeeHtml("tab === 'transactions'")
        ->assertSeeHtml("tab === 'blocks'");
});

it('should trigger is ready event for current tab view', function () {
    Wallet::factory()->activeValidator()->create();

    Livewire::test(Tables::class)
        ->call('triggerViewIsReady')
        ->assertEmitted('setTransactionsReady');
});

it('should trigger is ready event when changing tab view', function () {
    Wallet::factory()->activeValidator()->create();

    Livewire::test(Tables::class)
        ->call('triggerViewIsReady')
        ->assertEmitted('setTransactionsReady')
        ->set('view', 'blocks')
        ->assertEmitted('setBlocksReady');
});

it('should not trigger is ready event if tab view does not exist', function () {
    Wallet::factory()->activeValidator()->create();

    Livewire::test(Tables::class)
        ->set('view', 'testing')
        ->assertNotEmitted('setTestingReady');
});

it('should not trigger is ready event more than once', function () {
    Wallet::factory()->activeValidator()->create();

    Livewire::test(Tables::class)
        ->call('triggerViewIsReady')
        ->assertEmitted('setTransactionsReady')
        ->call('triggerViewIsReady')
        ->assertNotEmitted('setTransactionsReady')
        ->call('triggerViewIsReady')
        ->assertNotEmitted('setTransactionsReady')
        ->set('view', 'blocks')
        ->assertEmitted('setBlocksReady')
        ->set('view', 'transactions')
        ->assertNotEmitted('setTransactionsReady');
});

it('should not trigger is ready if same view is loaded', function () {
    Livewire::test(Tables::class)
        ->set('view', 'blocks')
        ->assertEmitted('setBlocksReady')
        ->set('view', 'blocks')
        ->assertNotEmitted('setBlocksReady');
});
