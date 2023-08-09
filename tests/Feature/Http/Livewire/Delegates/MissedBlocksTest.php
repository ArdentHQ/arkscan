<?php

declare(strict_types=1);

use App\Http\Livewire\Delegates\MissedBlocks;
use App\Models\ForgingStats;
use Livewire\Livewire;

beforeEach(function () {
    ForgingStats::truncate();
});

it('should render', function () {
    Livewire::test(MissedBlocks::class)
        ->assertSet('isReady', false)
        ->assertSee('Showing 0 results');
});

it('should render with missed blocks', function () {
    ForgingStats::factory(4)->create();

    Livewire::test(MissedBlocks::class)
        ->assertSee('Showing 0 results')
        ->call('setIsReady')
        ->assertSee('Showing 4 results');
});

it('should not defer loading if disabled', function () {
    Livewire::test(MissedBlocks::class, ['deferLoading' => false])
        ->assertSet('isReady', true)
        ->assertSee('Showing 0 results');
});

it('should show no results message if no missed blocks', function () {
    Livewire::test(MissedBlocks::class, ['deferLoading' => false])
        ->assertSee(trans('tables.missed-blocks.no_results'));
});
