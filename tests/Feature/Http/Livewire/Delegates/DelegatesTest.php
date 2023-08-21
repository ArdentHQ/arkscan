<?php

declare(strict_types=1);

use App\Http\Livewire\Delegates\Delegates;
use App\Models\Wallet;
use App\Services\Cache\WalletCache;
use Livewire\Livewire;

it('should render', function () {
    Livewire::test(Delegates::class)
        ->assertSet('isReady', false)
        ->assertSee('Showing 0 results');
});

it('should render with delegates', function () {
    Wallet::factory(51)->activeDelegate()->create();

    Livewire::test(Delegates::class)
        ->assertSee('Showing 0 results')
        ->call('setIsReady')
        ->assertSee('Showing 51 results');
});

it('should not defer loading if disabled', function () {
    Livewire::test(Delegates::class, ['deferLoading' => false])
        ->assertSet('isReady', true)
        ->assertSee('Showing 0 results');
});

it('should show no results message if no delegates', function () {
    Livewire::test(Delegates::class, ['deferLoading' => false])
        ->assertSee(trans('tables.delegates.no_results.no_results'));
});

it('should not show missed blocks on page 2', function () {
    Wallet::factory(102)->activeDelegate()->create();

    Livewire::test(Delegates::class, ['deferLoading' => false])
        ->assertSee(trans('tables.delegates.missed_blocks'))
        ->call('gotoPage', 2)
        ->assertDontSee(trans('tables.delegates.missed_blocks'));
});

it('should have slightly different per-page options', function () {
    $instance = Livewire::test(Delegates::class, ['deferLoading' => false])
        ->instance();

    expect($instance->perPageOptions())->toBe([
        10,
        25,
        51,
        100,
    ]);
});

it('should toggle all filters when "select all" is selected', function () {
    Livewire::test(Delegates::class)
        ->call('setIsReady')
        ->assertSet('filter', [
            'active'   => true,
            'standby'  => true,
            'resigned' => true,
        ])
        ->assertSet('selectAllFilters', true)
        ->set('filter.active', true)
        ->assertSet('selectAllFilters', true)
        ->set('selectAllFilters', false)
        ->assertSet('filter', [
            'active'   => false,
            'standby'  => false,
            'resigned' => false,
        ])
        ->set('selectAllFilters', true)
        ->assertSet('filter', [
            'active'   => true,
            'standby'  => true,
            'resigned' => true,
        ]);
});

it('should toggle "select all" when all filters are selected', function () {
    Livewire::test(Delegates::class)
        ->call('setIsReady')
        ->assertSet('filter', [
            'active'   => true,
            'standby'  => true,
            'resigned' => true,
        ])
        ->assertSet('selectAllFilters', true)
        ->set('filter.outgoing', false)
        ->assertSet('selectAllFilters', false)
        ->set('filter.outgoing', true)
        ->assertSet('selectAllFilters', true);
});

it('should filter active delegates', function () {
    $active   = Wallet::factory()->activeDelegate()->create();
    $standby  = Wallet::factory()->standbyDelegate(false)->create();
    $resigned = Wallet::factory()->standbyDelegate()->create();

    Livewire::test(Delegates::class)
        ->call('setIsReady')
        ->set('filter', [
            'active'   => true,
            'standby'  => false,
            'resigned' => false,
        ])
        ->assertSee($active->address)
        ->assertDontSee($standby->address)
        ->assertDontSee($resigned->address);
});

it('should filter standby delegates', function () {
    $active   = Wallet::factory()->activeDelegate()->create();
    $standby  = Wallet::factory()->standbyDelegate(false)->create();
    $resigned = Wallet::factory()->standbyDelegate()->create();

    Livewire::test(Delegates::class)
        ->call('setIsReady')
        ->set('filter', [
            'active'   => false,
            'standby'  => true,
            'resigned' => false,
        ])
        ->assertSee($standby->address)
        ->assertDontSee($active->address)
        ->assertDontSee($resigned->address);
});

it('should filter resigned delegates', function () {
    $active   = Wallet::factory()->activeDelegate()->create();
    $standby  = Wallet::factory()->standbyDelegate(false)->create();
    $resigned = Wallet::factory()->standbyDelegate()->create();

    Livewire::test(Delegates::class)
        ->call('setIsReady')
        ->set('filter', [
            'active'   => false,
            'standby'  => false,
            'resigned' => true,
        ])
        ->assertSee($resigned->address)
        ->assertDontSee($active->address)
        ->assertDontSee($standby->address);
});

it('should show correct message when no filters are selected', function () {
    Livewire::test(Delegates::class)
        ->call('setIsReady')
        ->set('filter', [
            'active'   => false,
            'standby'  => false,
            'resigned' => false,
        ])
        ->assertSee(trans('tables.delegates.no_results.no_filters'));
});

it('should show correct message when there are no results', function () {
    Livewire::test(Delegates::class)
        ->call('setIsReady')
        ->assertSee(trans('tables.delegates.no_results.no_results'));
});

it('should show the correct styling for "success" on missed blocks', function () {
    $wallet = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'rank'           => 1,
                'username'       => 'delegate-1',
                'voteBalance'    => 10000 * 1e8,
                'producedBlocks' => 1000,
            ],
        ],
    ]);

    (new WalletCache())->setProductivity($wallet->public_key, (1 - (1 / 1001)) * 100);
    (new WalletCache())->setMissedBlocks($wallet->public_key, 1);

    Livewire::test(Delegates::class)
        ->call('setIsReady')
        ->assertSee($wallet->address)
        ->assertSee('bg-theme-success-100 border-theme-success-100 text-theme-success-700 dark:border-theme-success-700 dark:text-theme-success-500');
});

it('should show the correct styling for "warning" on missed blocks', function () {
    $wallet = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'rank'           => 1,
                'username'       => 'delegate-1',
                'voteBalance'    => 10000 * 1e8,
                'producedBlocks' => 1000,
            ],
        ],
    ]);

    (new WalletCache())->setProductivity($wallet->public_key, (1 - (10 / 1001)) * 100);
    (new WalletCache())->setMissedBlocks($wallet->public_key, 10);

    Livewire::test(Delegates::class)
        ->call('setIsReady')
        ->assertSee($wallet->address)
        ->assertSee('bg-theme-orange-light border-theme-orange-light text-theme-orange-dark dark:border-theme-orange-dark dark:text-theme-warning-400');
});

it('should show the correct styling for "danger" on missed blocks', function () {
    $wallet = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'rank'           => 1,
                'username'       => 'delegate-1',
                'voteBalance'    => 10000 * 1e8,
                'producedBlocks' => 1000,
            ],
        ],
    ]);

    (new WalletCache())->setProductivity($wallet->public_key, (1 - (50 / 1001)) * 100);
    (new WalletCache())->setMissedBlocks($wallet->public_key, 50);

    Livewire::test(Delegates::class)
        ->call('setIsReady')
        ->assertSee($wallet->address)
        ->assertSee('bg-theme-danger-100 border-theme-danger-100 text-theme-danger-700 dark:border-[#AA6868] dark:text-[#F39B9B]');
});
