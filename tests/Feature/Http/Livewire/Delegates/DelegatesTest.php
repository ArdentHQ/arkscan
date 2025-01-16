<?php

declare(strict_types=1);

use App\Enums\SortDirection;
use App\Facades\Network;
use App\Http\Livewire\Delegates\Delegates;
use App\Models\ForgingStats;
use App\Models\Wallet;
use App\Services\Cache\DelegateCache;
use App\Services\Cache\WalletCache;
use App\Services\Timestamp;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Compilers\BladeCompiler;
use Livewire\Livewire;
use function Tests\faker;

beforeEach(function () {
    ForgingStats::truncate();
});

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
            'resigned' => false,
        ])
        ->set('filter.resigned', true)
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
            'resigned' => false,
        ])
        ->set('filter.resigned', true)
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
        ->set('filter.active', true)
        ->set('filter.standby', false)
        ->set('filter.resigned', false)
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
        ->set('filter.active', false)
        ->set('filter.standby', true)
        ->set('filter.resigned', false)
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
        ->set('filter.active', false)
        ->set('filter.standby', false)
        ->set('filter.resigned', true)
        ->assertSee($resigned->address)
        ->assertDontSee($active->address)
        ->assertDontSee($standby->address);
});

it('should show correct message when no filters are selected', function () {
    Livewire::test(Delegates::class)
        ->call('setIsReady')
        ->set('filter.active', false)
        ->set('filter.standby', false)
        ->set('filter.resigned', false)
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
        ->assertSee('bg-theme-orange-light border-theme-orange-light text-theme-orange-dark dark:!border-theme-warning-600 dark:text-theme-warning-400 dim:text-theme-warning-400');
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

it('should handle no cached votes when sorting by number of voters', function () {
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

    Livewire::test(Delegates::class)
        ->call('setIsReady')
        ->set('sortKey', 'votes')
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

it('should sort missed blocks in ascending order grouped by rank', function () {
    $wallet1 = Wallet::factory()->activeDelegate()->create([
        'address'    => 'wallet-1',
        'attributes' => [
            'delegate' => [
                'rank'        => 1,
                'username'    => 'delegate-1',
                'voteBalance' => 10000 * 1e8,
            ],
        ],
    ]);

    $wallet2 = Wallet::factory()->activeDelegate()->create([
        'address'    => 'wallet-2',
        'attributes' => [
            'delegate' => [
                'rank'        => 2,
                'username'    => 'delegate-2',
                'voteBalance' => 10000 * 1e8,
            ],
        ],
    ]);

    $wallet3 = Wallet::factory()->activeDelegate()->create([
        'address'    => 'wallet-3',
        'attributes' => [
            'delegate' => [
                'rank'        => 3,
                'username'    => 'delegate-3',
                'voteBalance' => 4000 * 1e8,
            ],
        ],
    ]);

    $inactiveWallets = collect();
    foreach (range(60, 72) as $rank) {
        $inactiveWallets[] = Wallet::factory()->activeDelegate()->create([
            'attributes' => [
                'delegate' => [
                    'rank'        => $rank,
                    'username'    => 'delegate-'.$rank,
                    'voteBalance' => 0,
                ],
            ],
        ]);
    }

    $walletWithoutMissedBlocks = Wallet::factory()->activeDelegate()->create([
        'address'    => 'wallet-4',
        'attributes' => [
            'delegate' => [
                'rank'        => 4,
                'username'    => 'delegate-4',
                'voteBalance' => 0,
            ],
        ],
    ]);

    $wallet51 = Wallet::factory()->activeDelegate()->create([
        'address'    => 'wallet-51',
        'attributes' => [
            'delegate' => [
                'rank'        => 51,
                'username'    => 'delegate-51',
                'voteBalance' => 4000 * 1e8,
            ],
        ],
    ]);

    ForgingStats::factory(2)->create([
        'public_key' => $wallet2->public_key,
    ]);

    ForgingStats::factory(5)->create([
        'public_key' => $wallet3->public_key,
    ]);

    Livewire::test(Delegates::class)
        ->call('setIsReady')
        ->set('sortKey', 'missed_blocks')
        ->assertSet('sortDirection', SortDirection::ASC)
        ->assertSeeInOrder([
            $wallet1->address,
            $walletWithoutMissedBlocks->address,
            $wallet51->address,
            $wallet2->address,
            $wallet3->address,
            ...$inactiveWallets->pluck('address'),
            $wallet1->address,
            $walletWithoutMissedBlocks->address,
            $wallet51->address,
            $wallet2->address,
            $wallet3->address,
            ...$inactiveWallets->pluck('address'),
        ]);
});

it('should sort missed blocks in descending order grouped by rank', function () {
    $wallet1 = Wallet::factory()->activeDelegate()->create([
        'address'    => 'wallet-1',
        'attributes' => [
            'delegate' => [
                'rank'        => 1,
                'username'    => 'delegate-1',
                'voteBalance' => 10000 * 1e8,
            ],
        ],
    ]);

    $wallet2 = Wallet::factory()->activeDelegate()->create([
        'address'    => 'wallet-2',
        'attributes' => [
            'delegate' => [
                'rank'        => 2,
                'username'    => 'delegate-2',
                'voteBalance' => 10000 * 1e8,
            ],
        ],
    ]);

    $wallet3 = Wallet::factory()->activeDelegate()->create([
        'address'    => 'wallet-3',
        'attributes' => [
            'delegate' => [
                'rank'        => 3,
                'username'    => 'delegate-3',
                'voteBalance' => 4000 * 1e8,
            ],
        ],
    ]);

    $inactiveWallets = collect();
    foreach (range(60, 72) as $rank) {
        $inactiveWallets[] = Wallet::factory()->activeDelegate()->create([
            'attributes' => [
                'delegate' => [
                    'rank'        => $rank,
                    'username'    => 'delegate-'.$rank,
                    'voteBalance' => 0,
                ],
            ],
        ]);
    }

    $walletWithoutMissedBlocks = Wallet::factory()->activeDelegate()->create([
        'address'    => 'wallet-4',
        'attributes' => [
            'delegate' => [
                'rank'        => 4,
                'username'    => 'delegate-4',
                'voteBalance' => 0,
            ],
        ],
    ]);

    $wallet51 = Wallet::factory()->activeDelegate()->create([
        'address'    => 'wallet-51',
        'attributes' => [
            'delegate' => [
                'rank'        => 51,
                'username'    => 'delegate-51',
                'voteBalance' => 4000 * 1e8,
            ],
        ],
    ]);

    ForgingStats::factory(2)->create([
        'public_key' => $wallet2->public_key,
    ]);

    ForgingStats::factory(5)->create([
        'public_key' => $wallet3->public_key,
    ]);

    Livewire::test(Delegates::class)
        ->call('setIsReady')
        ->set('sortKey', 'missed_blocks')
        ->set('sortDirection', SortDirection::DESC)
        ->assertSeeInOrder([
            $wallet3->address,
            $wallet2->address,
            $wallet1->address,
            $walletWithoutMissedBlocks->address,
            $wallet51->address,
            ...$inactiveWallets->pluck('address'),
            $wallet3->address,
            $wallet2->address,
            $wallet1->address,
            $walletWithoutMissedBlocks->address,
            $wallet51->address,
            ...$inactiveWallets->pluck('address'),
        ]);
});

it('should alternate sorting direction', function () {
    $delegateCache = new DelegateCache();
    $delegateCache->setAllVoterCounts(
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

it('should handle sorting an empty table', function () {
    $delegateCache = new DelegateCache();
    $delegateCache->setAllVoterCounts(
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
        ->assertSet('paginators.page', 1)
        ->assertSet('sortKey', 'rank')
        ->assertSet('sortDirection', SortDirection::ASC)
        ->call('gotoPage', 12)
        ->call('sortBy', 'rank')
        ->assertSet('paginators.page', 1)
        ->assertSet('sortKey', 'rank')
        ->assertSet('sortDirection', SortDirection::DESC)
        ->call('gotoPage', 12)
        ->call('sortBy', 'rank')
        ->assertSet('paginators.page', 1)
        ->assertSet('sortKey', 'rank')
        ->assertSet('sortDirection', SortDirection::ASC);
});

it('should parse sorting direction from query string', function () {
    Route::get('/test-delegates', function () {
        return BladeCompiler::render('<livewire:delegates.delegates :defer-loading="false" />');
    });

    $wallet1 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'rank'           => 2,
                'username'       => 'delegate-1',
                'voteBalance'    => 10000 * 1e8,
                'producedBlocks' => 1000,
            ],
        ],
    ]);

    $wallet2 = Wallet::factory()->activeDelegate()->create([
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
            $wallet1->address,
            $wallet2->address,
        ]);

    $this->get('/test-delegates?sort=name&sort-direction=desc')
        ->assertSeeInOrder([
            $wallet2->address,
            $wallet1->address,
        ]);
});

it('should force ascending if invalid query string value', function () {
    Route::get('/test-delegates', function () {
        return BladeCompiler::render('<livewire:delegates.delegates :defer-loading="false" />');
    });

    $wallet1 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'rank'           => 2,
                'username'       => 'delegate-1',
                'voteBalance'    => 10000 * 1e8,
                'producedBlocks' => 1000,
            ],
        ],
    ]);

    $wallet2 = Wallet::factory()->activeDelegate()->create([
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
            $wallet2->address,
            $wallet1->address,
        ]);

    $this->get('/test-delegates?sort=name&sort-direction=testing')
        ->assertSeeInOrder([
            $wallet1->address,
            $wallet2->address,
        ]);
});

it('should handle sorting several pages of delegates without cached data', function ($columnSortBy, $modelSortBy) {
    $delegateData = [];
    foreach (range(1, 145) as $rank) {
        $wallet         = faker()->wallet;
        $delegateData[] = [
            'id'                => faker()->uuid,
            'balance'           => faker()->numberBetween(1, 1000) * 1e8,
            'nonce'             => faker()->numberBetween(1, 1000),
            'attributes'        => [
                'delegate'        => [
                    'username'       => faker()->userName,
                    'voteBalance'    => faker()->numberBetween(1, 1000) * 1e8,
                    'producedBlocks' => faker()->numberBetween(1, 1000),
                    'missedBlocks'   => faker()->numberBetween(1, 1000),
                ],
            ],
            'updated_at'       => faker()->dateTimeBetween('-1 year', 'now'),

            'address'    => $wallet['address'],
            'public_key' => $wallet['publicKey'],
            'attributes' => json_encode([
                'delegate' => [
                    'rank'        => $rank,
                    'username'    => 'delegate-'.$rank,
                    'voteBalance' => random_int(1000, 10000) * 1e8,
                ],
            ]),
        ];
    }

    Wallet::insert($delegateData);

    $delegates = Wallet::all()->sort(function ($a, $b) use ($modelSortBy) {
        $bValue = Arr::get($b, $modelSortBy);
        $aValue = Arr::get($a, $modelSortBy);

        if (is_numeric($bValue) && is_numeric($aValue)) {
            return (int) $aValue - (int) $bValue;
        }

        return strcmp($aValue, $bValue);
    });

    $component = Livewire::test(Delegates::class)
        ->call('setIsReady')
        ->call('sortBy', $columnSortBy)
        ->set('sortDirection', SortDirection::ASC);

    foreach (range(1, 3) as $page) {
        $pageDelegates = $delegates->chunk(51)->get($page - 1)->pluck('address');

        $component->call('gotoPage', $page)
            ->assertSeeInOrder([
                ...$pageDelegates,
                ...$pageDelegates,
            ]);
    }
})->with([
    'rank'             => ['rank', 'attributes.delegate.rank'],
    'name'             => ['name', 'attributes.delegate.username'],
    'no_of_voters'     => ['no_of_voters', 'attributes.delegate.rank'],
    'votes'            => ['votes', 'attributes.delegate.voteBalance'],
    'percentage_votes' => ['percentage_votes', 'attributes.delegate.voteBalance'],
    'missed_blocks'    => ['missed_blocks', 'attributes.delegate.rank'],
]);

it('should handle sorting several pages of delegates with cached data', function ($columnSortBy, $modelSortBy) {
    $delegateData = [];
    foreach (range(1, 145) as $rank) {
        $wallet         = faker()->wallet;
        $delegateData[] = [
            'id'                => faker()->uuid,
            'balance'           => faker()->numberBetween(1, 1000) * 1e8,
            'nonce'             => faker()->numberBetween(1, 1000),
            'attributes'        => [
                'delegate'        => [
                    'username'       => faker()->userName,
                    'voteBalance'    => faker()->numberBetween(1, 1000) * 1e8,
                    'producedBlocks' => faker()->numberBetween(1, 1000),
                    'missedBlocks'   => faker()->numberBetween(1, 1000),
                ],
            ],
            'updated_at'       => faker()->dateTimeBetween('-1 year', 'now'),

            'address'    => 'wallet-'.$rank,
            'public_key' => $wallet['publicKey'],
            'attributes' => json_encode([
                'delegate' => [
                    'rank'        => $rank,
                    'username'    => 'delegate-'.$rank,
                    'voteBalance' => random_int(1000, 10000) * 1e8,
                ],
            ]),
        ];
    }

    Wallet::insert($delegateData);

    $delegates = Wallet::all();

    $voterCounts        = [];
    $missedBlocks       = [];
    $missedBlockCounter = 0;

    $missedBlocksData = [];

    foreach ($delegates as $delegate) {
        $missedBlockCount = random_int(2, 4);
        foreach (range(1, $missedBlockCount) as $_) {
            $missedBlocksData[] = [
                'timestamp'     => Timestamp::fromUnix(Carbon::now()->subHours($missedBlockCounter)->unix())->unix(),
                'public_key'    => $delegate->public_key,
                'forged'        => faker()->boolean(),
                'missed_height' => faker()->numberBetween(1, 10000),
            ];

            $missedBlockCounter++;
        }

        $voterCounts[$delegate->public_key] = random_int(10, 100);
    }

    ForgingStats::insert($missedBlocksData);

    $missedBlocks = ForgingStats::all()->groupBy('public_key');

    $delegateCache = new DelegateCache();
    $delegateCache->setAllVoterCounts($voterCounts);

    $delegates = $delegates->sort(function ($a, $b) use ($modelSortBy, $missedBlocks, $voterCounts) {
        if ($modelSortBy === 'missed_blocks') {
            $aRank = Arr::get($a, 'attributes.delegate.rank');
            $bRank = Arr::get($b, 'attributes.delegate.rank');
            if ($aRank <= Network::delegateCount() && $bRank > Network::delegateCount()) {
                return -1;
            } elseif ($aRank > Network::delegateCount() && $bRank <= Network::delegateCount()) {
                return 1;
            }

            $aValue = count($missedBlocks[$a->public_key]);
            $bValue = count($missedBlocks[$b->public_key]);

            if ($aValue === $bValue) {
                return $aRank - $bRank;
            }
        } elseif ($modelSortBy === 'no_of_voters') {
            $aValue = $voterCounts[$a->public_key];
            $bValue = $voterCounts[$b->public_key];
        } else {
            $aValue = Arr::get($a, $modelSortBy);
            $bValue = Arr::get($b, $modelSortBy);
        }

        if (is_numeric($bValue) && is_numeric($aValue)) {
            return (int) $aValue - (int) $bValue;
        }

        return strcmp($aValue, $bValue);
    });

    $component = Livewire::test(Delegates::class)
        ->call('setIsReady')
        ->call('sortBy', $columnSortBy)
        ->set('sortDirection', SortDirection::ASC);

    foreach (range(1, 3) as $page) {
        $pageDelegates = $delegates->chunk(51)->get($page - 1)->pluck('address');

        $component->call('gotoPage', $page)
            ->assertSeeInOrder([
                ...$pageDelegates,
                ...$pageDelegates,
            ]);
    }
})->with([
    'rank'             => ['rank', 'attributes.delegate.rank'],
    'name'             => ['name', 'attributes.delegate.username'],
    'no_of_voters'     => ['no_of_voters', 'no_of_voters'],
    'votes'            => ['votes', 'attributes.delegate.voteBalance'],
    'percentage_votes' => ['percentage_votes', 'attributes.delegate.voteBalance'],
    'missed_blocks'    => ['missed_blocks', 'missed_blocks'],
]);
