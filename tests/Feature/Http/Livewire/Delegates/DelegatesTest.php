<?php

declare(strict_types=1);

use App\Enums\SortDirection;
use App\Http\Livewire\Delegates\Delegates;
use App\Models\ForgingStats;
use App\Models\Wallet;
use App\Services\Cache\DelegateCache;
use App\Services\Cache\WalletCache;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Compilers\BladeCompiler;
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

it('should sort by rank by default', function () {
    Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'rank'           => 2,
                'username'       => 'delegate-2',
                'voteBalance'    => 4000 * 1e8,
                'producedBlocks' => 1000,
            ],
        ],
    ]);

    Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'rank'           => 1,
                'username'       => 'delegate-1',
                'voteBalance'    => 10000 * 1e8,
                'producedBlocks' => 1000,
            ],
        ],
    ]);

    Livewire::test(Delegates::class)
        ->call('setIsReady')
        ->assertSet('sortKey', 'rank')
        ->assertSet('sortDirection', SortDirection::ASC)
        ->assertSeeInOrder([
            $wallet1->address,
            $wallet2->address,
            $wallet1->address,
            $wallet2->address,
        ]);
});

it('should sort rank in descending order', function () {
    Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'rank'           => 1,
                'username'       => 'delegate-1',
                'voteBalance'    => 10000 * 1e8,
                'producedBlocks' => 1000,
            ],
        ],
    ]);

    Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'rank'           => 2,
                'username'       => 'delegate-2',
                'voteBalance'    => 4000 * 1e8,
                'producedBlocks' => 1000,
            ],
        ],
    ]);

    Livewire::test(Delegates::class)
        ->call('setIsReady')
        ->assertSet('sortKey', 'rank')
        ->set('sortDirection', SortDirection::DESC)
        ->assertSeeInOrder([
            $wallet2->address,
            $wallet1->address,
            $wallet2->address,
            $wallet1->address,
        ]);
});

it('should sort name in ascending order', function () {
    Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'rank'           => 2,
                'username'       => 'delegate-2',
                'voteBalance'    => 4000 * 1e8,
                'producedBlocks' => 1000,
            ],
        ],
    ]);

    Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'rank'           => 1,
                'username'       => 'delegate-1',
                'voteBalance'    => 10000 * 1e8,
                'producedBlocks' => 1000,
            ],
        ],
    ]);

    Livewire::test(Delegates::class)
        ->call('setIsReady')
        ->set('sortKey', 'name')
        ->assertSet('sortDirection', SortDirection::ASC)
        ->assertSeeInOrder([
            $wallet1->address,
            $wallet2->address,
            $wallet1->address,
            $wallet2->address,
        ]);
});

it('should sort name in descending order', function () {
    Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'rank'           => 1,
                'username'       => 'delegate-1',
                'voteBalance'    => 10000 * 1e8,
                'producedBlocks' => 1000,
            ],
        ],
    ]);

    Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'rank'           => 2,
                'username'       => 'delegate-2',
                'voteBalance'    => 4000 * 1e8,
                'producedBlocks' => 1000,
            ],
        ],
    ]);

    Livewire::test(Delegates::class)
        ->call('setIsReady')
        ->set('sortKey', 'name')
        ->set('sortDirection', SortDirection::DESC)
        ->assertSeeInOrder([
            $wallet2->address,
            $wallet1->address,
            $wallet2->address,
            $wallet1->address,
        ]);
});

it('should sort number of voters in ascending order', function () {
    $wallet1 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'rank'           => 1,
                'username'       => 'delegate-1',
                'voteBalance'    => 10000 * 1e8,
                'producedBlocks' => 1000,
            ],
        ],
    ]);

    $wallet2 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'rank'           => 2,
                'username'       => 'delegate-2',
                'voteBalance'    => 4000 * 1e8,
                'producedBlocks' => 1000,
            ],
        ],
    ]);

    $walletWithoutVotes = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'rank'           => 3,
                'username'       => 'delegate-3',
                'voteBalance'    => 0,
                'producedBlocks' => 1000,
            ],
        ],
    ]);

    $delegateCache = new DelegateCache();
    $delegateCache->setAllVoterCounts([
        $wallet1->public_key => 30,
        $wallet2->public_key => 10,
    ]);

    Livewire::test(Delegates::class)
        ->call('setIsReady')
        ->set('sortKey', 'no_of_voters')
        ->assertSet('sortDirection', SortDirection::ASC)
        ->assertSeeInOrder([
            $wallet2->address,
            $wallet1->address,
            $walletWithoutVotes->address,
            $wallet2->address,
            $wallet1->address,
            $walletWithoutVotes->address,
        ]);
});

it('should sort number of voters in descending order', function () {
    $wallet1 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'rank'           => 1,
                'username'       => 'delegate-1',
                'voteBalance'    => 10000 * 1e8,
                'producedBlocks' => 1000,
            ],
        ],
    ]);

    $wallet2 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'rank'           => 2,
                'username'       => 'delegate-2',
                'voteBalance'    => 4000 * 1e8,
                'producedBlocks' => 1000,
            ],
        ],
    ]);

    $walletWithoutVotes = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'rank'           => 3,
                'username'       => 'delegate-3',
                'voteBalance'    => 0,
                'producedBlocks' => 1000,
            ],
        ],
    ]);

    $delegateCache = new DelegateCache();
    $delegateCache->setAllVoterCounts([
        $wallet1->public_key => 30,
        $wallet2->public_key => 10,
    ]);

    Livewire::test(Delegates::class)
        ->call('setIsReady')
        ->set('sortKey', 'no_of_voters')
        ->set('sortDirection', SortDirection::DESC)
        ->assertSeeInOrder([
            $wallet1->address,
            $wallet2->address,
            $walletWithoutVotes->address,
            $wallet1->address,
            $wallet2->address,
            $walletWithoutVotes->address,
        ]);
});

it('should sort votes & percentage in ascending order', function (string $sortKey) {
    Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'rank'           => 1,
                'username'       => 'delegate-1',
                'voteBalance'    => 10000 * 1e8,
                'producedBlocks' => 1000,
            ],
        ],
    ]);

    Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'rank'           => 2,
                'username'       => 'delegate-2',
                'voteBalance'    => 4000 * 1e8,
                'producedBlocks' => 1000,
            ],
        ],
    ]);

    Livewire::test(Delegates::class)
        ->call('setIsReady')
        ->set('sortKey', $sortKey)
        ->assertSet('sortDirection', SortDirection::ASC)
        ->assertSeeInOrder([
            $wallet2->address,
            $wallet1->address,
            $wallet2->address,
            $wallet1->address,
        ]);
})->with([
    'votes',
    'percentage_votes',
]);

it('should sort votes & percentage in descending order', function (string $sortKey) {
    Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'rank'           => 1,
                'username'       => 'delegate-1',
                'voteBalance'    => 10000 * 1e8,
                'producedBlocks' => 1000,
            ],
        ],
    ]);

    Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'rank'           => 2,
                'username'       => 'delegate-2',
                'voteBalance'    => 4000 * 1e8,
                'producedBlocks' => 1000,
            ],
        ],
    ]);

    Livewire::test(Delegates::class)
        ->call('setIsReady')
        ->set('sortKey', $sortKey)
        ->set('sortDirection', SortDirection::DESC)
        ->assertSeeInOrder([
            $wallet1->address,
            $wallet2->address,
            $wallet1->address,
            $wallet2->address,
        ]);
})->with([
    'votes',
    'percentage_votes',
]);

it('should sort missed blocks in ascending order', function () {
    $wallet1 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'rank'           => 1,
                'username'       => 'delegate-1',
                'voteBalance'    => 10000 * 1e8,
                'producedBlocks' => 1000,
            ],
        ],
    ]);

    $wallet2 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'rank'           => 2,
                'username'       => 'delegate-2',
                'voteBalance'    => 4000 * 1e8,
                'producedBlocks' => 1000,
            ],
        ],
    ]);

    $walletWithoutMissedBlocks = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'rank'           => 3,
                'username'       => 'delegate-3',
                'voteBalance'    => 0,
                'producedBlocks' => 1000,
            ],
        ],
    ]);

    ForgingStats::factory(24)->create([
        'public_key' => $wallet1->public_key,
    ]);

    ForgingStats::factory(59)->create([
        'public_key' => $wallet2->public_key,
    ]);

    Livewire::test(Delegates::class)
        ->call('setIsReady')
        ->set('sortKey', 'missed_blocks')
        ->assertSet('sortDirection', SortDirection::ASC)
        ->assertSeeInOrder([
            $wallet1->address,
            $wallet2->address,
            $walletWithoutMissedBlocks->address,
            $wallet1->address,
            $wallet2->address,
            $walletWithoutMissedBlocks->address,
        ]);
});

it('should sort missed blocks in descending order', function () {
    $wallet1 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'rank'           => 1,
                'username'       => 'delegate-1',
                'voteBalance'    => 10000 * 1e8,
                'producedBlocks' => 1000,
            ],
        ],
    ]);

    $wallet2 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'rank'           => 2,
                'username'       => 'delegate-2',
                'voteBalance'    => 4000 * 1e8,
                'producedBlocks' => 1000,
            ],
        ],
    ]);

    $walletWithoutMissedBlocks = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'rank'           => 3,
                'username'       => 'delegate-3',
                'voteBalance'    => 0,
                'producedBlocks' => 1000,
            ],
        ],
    ]);

    ForgingStats::factory(24)->create([
        'public_key' => $wallet1->public_key,
    ]);

    ForgingStats::factory(59)->create([
        'public_key' => $wallet2->public_key,
    ]);

    Livewire::test(Delegates::class)
        ->call('setIsReady')
        ->set('sortKey', 'missed_blocks')
        ->set('sortDirection', SortDirection::DESC)
        ->assertSeeInOrder([
            $wallet2->address,
            $wallet1->address,
            $walletWithoutMissedBlocks->address,
            $wallet2->address,
            $wallet1->address,
            $walletWithoutMissedBlocks->address,
        ]);
});

it('should alternate sorting direction', function () {
    (new DelegateCache())->setAllVoterCounts(
        Wallet::factory(51)
            ->activeDelegate()
            ->create()
            ->mapWithKeys(fn ($delegate) => [$delegate->public_key => 1])
            ->toArray()
    );

    ForgingStats::factory(24)->create();

    $component = Livewire::test(Delegates::class)
        ->call('setIsReady')
        ->assertSet('sortKey', 'rank')
        ->assertSet('sortDirection', SortDirection::ASC)
        ->call('sortBy', 'rank')
        ->assertSet('sortKey', 'rank')
        ->assertSet('sortDirection', SortDirection::DESC);

    foreach (['name', 'no_of_voters', 'votes', 'percentage_votes', 'missed_blocks'] as $column) {
        $component->call('sortBy', $column)
            ->assertSet('sortKey', $column)
            ->assertSet('sortDirection', SortDirection::ASC)
            ->call('sortBy', $column)
            ->assertSet('sortKey', $column)
            ->assertSet('sortDirection', SortDirection::DESC);
    }
});

it('should reset page on sorting change', function () {
    Livewire::test(Delegates::class)
        ->call('setIsReady')
        ->assertSet('page', 1)
        ->assertSet('sortKey', 'rank')
        ->assertSet('sortDirection', SortDirection::ASC)
        ->set('page', 12)
        ->call('sortBy', 'rank')
        ->assertSet('page', 1)
        ->assertSet('sortKey', 'rank')
        ->assertSet('sortDirection', SortDirection::DESC)
        ->set('page', 12)
        ->call('sortBy', 'rank')
        ->assertSet('page', 1)
        ->assertSet('sortKey', 'rank')
        ->assertSet('sortDirection', SortDirection::ASC);
});

it('should parse sorting direction from query string', function () {
    Route::get('/test-delegates', function () {
        return BladeCompiler::render('<livewire:delegates.delegates :defer-loading="false" />');
    });

    Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'rank'           => 2,
                'username'       => 'delegate-1',
                'voteBalance'    => 10000 * 1e8,
                'producedBlocks' => 1000,
            ],
        ],
    ]);

    Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'rank'           => 1,
                'username'       => 'delegate-2',
                'voteBalance'    => 4000 * 1e8,
                'producedBlocks' => 1000,
            ],
        ],
    ]);

    $this->get('/test-delegates?sort=name&sort-direction=asc')
        ->assertSeeInOrder([
            'delegate-1',
            'delegate-2',
        ]);

    $this->get('/test-delegates?sort=name&sort-direction=desc')
        ->assertSeeInOrder([
            'delegate-2',
            'delegate-1',
        ]);
});

it('should force ascending if invalid query string value', function () {
    Route::get('/test-delegates', function () {
        return BladeCompiler::render('<livewire:delegates.delegates :defer-loading="false" />');
    });

    Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'rank'           => 2,
                'username'       => 'delegate-1',
                'voteBalance'    => 10000 * 1e8,
                'producedBlocks' => 1000,
            ],
        ],
    ]);

    Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'rank'           => 1,
                'username'       => 'delegate-2',
                'voteBalance'    => 4000 * 1e8,
                'producedBlocks' => 1000,
            ],
        ],
    ]);

    $this->get('/test-delegates?sort=name&sort-direction=desc')
        ->assertSeeInOrder([
            'delegate-2',
            'delegate-1',
        ]);

    $this->get('/test-delegates?sort=name&sort-direction=testing')
        ->assertSeeInOrder([
            'delegate-1',
            'delegate-2',
        ]);
});
