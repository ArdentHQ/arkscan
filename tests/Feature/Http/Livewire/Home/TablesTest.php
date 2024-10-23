<?php

declare(strict_types=1);

use App\Http\Livewire\Home\Tables;
use App\Models\Wallet;
use Livewire\Livewire;

it('should render all tabs', function () {
    Wallet::factory()->activeDelegate()->create();

    Livewire::test(Tables::class)
        ->assertSeeHtml("tab === 'transactions'")
        ->assertSeeHtml("tab === 'blocks'");
});

it('should trigger is ready event for current tab view', function () {
    Wallet::factory()->activeDelegate()->create();

    Livewire::test(Tables::class)
        ->call('triggerViewIsReady')
        ->assertDispatched('setTransactionsReady');
});

it('should trigger is ready event when changing tab view', function () {
    Wallet::factory()->activeDelegate()->create();

    Livewire::test(Tables::class)
        ->call('triggerViewIsReady')
        ->assertDispatched('setTransactionsReady')
        ->set('view', 'blocks')
        ->assertDispatched('setBlocksReady');
});

it('should not trigger is ready event if tab view does not exist', function () {
    Wallet::factory()->activeDelegate()->create();

    Livewire::test(Tables::class)
        ->set('view', 'testing')
        ->assertNotDispatched('setTestingReady');
});

it('should not trigger is ready event more than once', function () {
    Wallet::factory()->activeDelegate()->create();

    Livewire::test(Tables::class)
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

it('should not trigger is ready if same view is loaded', function () {
    Livewire::test(Tables::class)
        ->set('view', 'blocks')
        ->assertDispatched('setBlocksReady')
        ->set('view', 'blocks')
        ->assertNotDispatched('setBlocksReady');
});
