<?php

declare(strict_types=1);

use App\Http\Livewire\Delegates\Delegates;
use App\Models\Wallet;
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
