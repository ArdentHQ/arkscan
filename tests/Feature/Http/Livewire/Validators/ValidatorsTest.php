<?php

declare(strict_types=1);

use App\Enums\SortDirection;
use App\Facades\Network;
use App\Http\Livewire\Validators\Validators;
use App\Models\ForgingStats;
use App\Models\State;
use App\Models\Wallet;
use App\Services\Cache\ValidatorCache;
use App\Services\Cache\WalletCache;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Compilers\BladeCompiler;
use Livewire\Livewire;
use function Tests\faker;

beforeEach(function () {
    State::factory()->create();

    ForgingStats::truncate();
});

it('should render', function () {
    Livewire::test(Validators::class)
        ->assertSet('isReady', false)
        ->assertSee('Showing 0 results');
});

it('should render with validators', function () {
    Wallet::factory(Network::validatorCount())->activeValidator()->create();

    Livewire::test(Validators::class)
        ->assertSee('Showing 0 results')
        ->call('setIsReady')
        ->assertSee('Showing '.Network::validatorCount().' results');
});

it('should not defer loading if disabled', function () {
    Livewire::test(Validators::class, ['deferLoading' => false])
        ->assertSet('isReady', true)
        ->assertSee('Showing 0 results');
});

it('should show no results message if no validators', function () {
    Livewire::test(Validators::class, ['deferLoading' => false])
        ->assertSee(trans('tables.validators.no_results.no_results'));
});

it('should have slightly different per-page options', function () {
    $instance = Livewire::test(Validators::class, ['deferLoading' => false])
        ->instance();

    expect($instance->perPageOptions())->toBe([
        10,
        25,
        Network::validatorCount(),
        100,
    ]);
});

it('should toggle all filters when "select all" is selected', function () {
    Livewire::test(Validators::class)
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
    Livewire::test(Validators::class)
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

it('should filter active validators', function () {
    $active   = Wallet::factory()->activeValidator()->create();
    $standby  = Wallet::factory()->standbyValidator(false)->create();
    $resigned = Wallet::factory()->standbyValidator()->create();

    Livewire::test(Validators::class)
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

it('should filter standby validators', function () {
    $active   = Wallet::factory()->activeValidator()->create();
    $standby  = Wallet::factory()->standbyValidator(false)->create();
    $resigned = Wallet::factory()->standbyValidator()->create();

    Livewire::test(Validators::class)
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

it('should filter resigned validators', function () {
    $active   = Wallet::factory()->activeValidator()->create();
    $standby  = Wallet::factory()->standbyValidator(false)->create();
    $resigned = Wallet::factory()->standbyValidator()->create();

    Livewire::test(Validators::class)
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
    Livewire::test(Validators::class)
        ->call('setIsReady')
        ->set('filter', [
            'active'   => false,
            'standby'  => false,
            'resigned' => false,
        ])
        ->assertSee(trans('tables.validators.no_results.no_filters'));
});

it('should show correct message when there are no results', function () {
    Livewire::test(Validators::class)
        ->call('setIsReady')
        ->assertSee(trans('tables.validators.no_results.no_results'));
});

it('should show the correct styling for "success" on missed blocks', function () {
    $wallet = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorRank'           => 1,
            'username'                => 'validator-1',
            'validatorVoteBalance'    => 10000 * 1e18,
            'validatorPublicKey'      => 'publicKey',
            'validatorProducedBlocks' => 1000,
        ],
    ]);

    (new WalletCache())->setProductivity($wallet->public_key, (1 - (1 / 1001)) * 100);
    (new WalletCache())->setMissedBlocks($wallet->public_key, 1);

    Livewire::test(Validators::class)
        ->call('setIsReady')
        ->assertSee($wallet->address)
        ->assertSee('bg-theme-success-100 border-theme-success-100 text-theme-success-700 dark:border-theme-success-700 dark:text-theme-success-500');
});

it('should show the correct styling for "warning" on missed blocks', function () {
    $wallet = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorRank'           => 1,
            'username'                => 'validator-1',
            'validatorVoteBalance'    => 10000 * 1e18,
            'validatorPublicKey'      => 'publicKey',
            'validatorProducedBlocks' => 1000,
        ],
    ]);

    (new WalletCache())->setProductivity($wallet->public_key, (1 - (10 / 1001)) * 100);
    (new WalletCache())->setMissedBlocks($wallet->public_key, 10);

    Livewire::test(Validators::class)
        ->call('setIsReady')
        ->assertSee($wallet->address)
        ->assertSee('bg-theme-orange-light border-theme-orange-light text-theme-orange-dark dark:!border-theme-warning-600 dark:text-theme-warning-400 dim:text-theme-warning-400');
});

it('should show the correct styling for "danger" on missed blocks', function () {
    $wallet = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorRank'           => 1,
            'username'                => 'validator-1',
            'validatorVoteBalance'    => 10000 * 1e18,
            'validatorPublicKey'      => 'publicKey',
            'validatorProducedBlocks' => 1000,
        ],
    ]);

    (new WalletCache())->setProductivity($wallet->public_key, (1 - (50 / 1001)) * 100);
    (new WalletCache())->setMissedBlocks($wallet->public_key, 50);

    Livewire::test(Validators::class)
        ->call('setIsReady')
        ->assertSee($wallet->address)
        ->assertSee('bg-theme-danger-100 border-theme-danger-100 text-theme-danger-700 dark:border-[#AA6868] dark:text-[#F39B9B]');
});

it('should sort by rank by default', function () {
    $wallet2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorRank'           => 2,
            'username'                => 'validator-2',
            'validatorVoteBalance'    => 4000 * 1e18,
            'validatorPublicKey'      => 'publicKey',
            'validatorProducedBlocks' => 1000,
        ],
    ]);

    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorRank'           => 1,
            'username'                => 'validator-1',
            'validatorVoteBalance'    => 10000 * 1e18,
            'validatorPublicKey'      => 'publicKey',
            'validatorProducedBlocks' => 1000,
        ],
    ]);

    Livewire::test(Validators::class)
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
    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorRank'           => 1,
            'username'                => 'validator-1',
            'validatorVoteBalance'    => 10000 * 1e18,
            'validatorPublicKey'      => 'publicKey',
            'validatorProducedBlocks' => 1000,
        ],
    ]);

    $wallet2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorRank'           => 2,
            'username'                => 'validator-2',
            'validatorVoteBalance'    => 4000 * 1e18,
            'validatorPublicKey'      => 'publicKey',
            'validatorProducedBlocks' => 1000,
        ],
    ]);

    Livewire::test(Validators::class)
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
    $wallet2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorRank'           => 2,
            'username'                => 'validator-2',
            'validatorVoteBalance'    => 4000 * 1e18,
            'validatorPublicKey'      => 'publicKey',
            'validatorProducedBlocks' => 1000,
        ],
    ]);

    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorRank'           => 1,
            'username'                => 'validator-1',
            'validatorVoteBalance'    => 10000 * 1e18,
            'validatorPublicKey'      => 'publicKey',
            'validatorProducedBlocks' => 1000,
        ],
    ]);

    Livewire::test(Validators::class)
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
    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorRank'           => 1,
            'username'                => 'validator-1',
            'validatorVoteBalance'    => 10000 * 1e18,
            'validatorPublicKey'      => 'publicKey',
            'validatorProducedBlocks' => 1000,
        ],
    ]);

    $wallet2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorRank'           => 2,
            'username'                => 'validator-2',
            'validatorVoteBalance'    => 4000 * 1e18,
            'validatorPublicKey'      => 'publicKey',
            'validatorProducedBlocks' => 1000,
        ],
    ]);

    Livewire::test(Validators::class)
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
    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorRank'           => 1,
            'username'                => 'validator-1',
            'validatorVoteBalance'    => 10000 * 1e18,
            'validatorPublicKey'      => 'publicKey',
            'validatorProducedBlocks' => 1000,
        ],
    ]);

    $wallet2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorRank'           => 2,
            'username'                => 'validator-2',
            'validatorVoteBalance'    => 4000 * 1e18,
            'validatorPublicKey'      => 'publicKey',
            'validatorProducedBlocks' => 1000,
        ],
    ]);

    $walletWithoutVotes = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorRank'           => 3,
            'username'                => 'validator-3',
            'validatorVoteBalance'    => 0,
            'validatorPublicKey'      => 'publicKey',
            'validatorProducedBlocks' => 1000,
        ],
    ]);

    $validatorCache = new ValidatorCache();
    $validatorCache->setAllVoterCounts([
        $wallet1->public_key => 30,
        $wallet2->public_key => 10,
    ]);

    Livewire::test(Validators::class)
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
    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorRank'           => 1,
            'username'                => 'validator-1',
            'validatorVoteBalance'    => 10000 * 1e18,
            'validatorPublicKey'      => 'publicKey',
            'validatorProducedBlocks' => 1000,
        ],
    ]);

    $wallet2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorRank'           => 2,
            'username'                => 'validator-2',
            'validatorVoteBalance'    => 4000 * 1e18,
            'validatorPublicKey'      => 'publicKey',
            'validatorProducedBlocks' => 1000,
        ],
    ]);

    $walletWithoutVotes = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorRank'           => 3,
            'username'                => 'validator-3',
            'validatorVoteBalance'    => 0,
            'validatorPublicKey'      => 'publicKey',
            'validatorProducedBlocks' => 1000,
        ],
    ]);

    $validatorCache = new ValidatorCache();
    $validatorCache->setAllVoterCounts([
        $wallet1->public_key => 30,
        $wallet2->public_key => 10,
    ]);

    Livewire::test(Validators::class)
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
    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorRank'           => 1,
            'username'                => 'validator-1',
            'validatorVoteBalance'    => 10000 * 1e18,
            'validatorPublicKey'      => 'publicKey',
            'validatorProducedBlocks' => 1000,
        ],
    ]);

    $wallet2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorRank'           => 2,
            'username'                => 'validator-2',
            'validatorVoteBalance'    => 4000 * 1e18,
            'validatorPublicKey'      => 'publicKey',
            'validatorProducedBlocks' => 1000,
        ],
    ]);

    $walletWithoutVotes = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorRank'           => 3,
            'username'                => 'validator-3',
            'validatorVoteBalance'    => 0,
            'validatorPublicKey'      => 'publicKey',
            'validatorProducedBlocks' => 1000,
        ],
    ]);

    Livewire::test(Validators::class)
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
    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorRank'           => 1,
            'username'                => 'validator-1',
            'validatorVoteBalance'    => 10000 * 1e18,
            'validatorPublicKey'      => 'publicKey',
            'validatorProducedBlocks' => 1000,
        ],
    ]);

    $wallet2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorRank'           => 2,
            'username'                => 'validator-2',
            'validatorVoteBalance'    => 4000 * 1e18,
            'validatorPublicKey'      => 'publicKey',
            'validatorProducedBlocks' => 1000,
        ],
    ]);

    Livewire::test(Validators::class)
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
    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorRank'           => 1,
            'username'                => 'validator-1',
            'validatorVoteBalance'    => 10000 * 1e18,
            'validatorPublicKey'      => 'publicKey',
            'validatorProducedBlocks' => 1000,
        ],
    ]);

    $wallet2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorRank'           => 2,
            'username'                => 'validator-2',
            'validatorVoteBalance'    => 4000 * 1e18,
            'validatorPublicKey'      => 'publicKey',
            'validatorProducedBlocks' => 1000,
        ],
    ]);

    Livewire::test(Validators::class)
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
    $wallet1 = Wallet::factory()->activeValidator()->create([
        'address'    => 'wallet-1',
        'attributes' => [
            'validatorRank'           => 1,
            'username'                => 'validator-1',
            'validatorVoteBalance'    => 10000 * 1e18,
            'validatorPublicKey'      => 'publicKey',
        ],
    ]);

    $wallet2 = Wallet::factory()->activeValidator()->create([
        'address'    => 'wallet-2',
        'attributes' => [
            'validatorRank'           => 2,
            'username'                => 'validator-2',
            'validatorVoteBalance'    => 10000 * 1e18,
            'validatorPublicKey'      => 'publicKey',
        ],
    ]);

    $wallet3 = Wallet::factory()->activeValidator()->create([
        'address'    => 'wallet-3',
        'attributes' => [
            'validatorRank'           => 3,
            'username'                => 'validator-3',
            'validatorVoteBalance'    => 4000 * 1e18,
            'validatorPublicKey'      => 'publicKey',
        ],
    ]);

    $inactiveWallets = collect();
    foreach (range(60, 72) as $rank) {
        $inactiveWallets[] = Wallet::factory()->activeValidator()->create([
            'attributes' => [
                'validatorRank'           => $rank,
                'username'                => 'validator-'.$rank,
                'validatorVoteBalance'    => 0,
                'validatorPublicKey'      => 'publicKey',
            ],
        ]);
    }

    $walletWithoutMissedBlocks = Wallet::factory()->activeValidator()->create([
        'address'    => 'wallet-4',
        'attributes' => [
            'validatorRank'           => 4,
            'username'                => 'validator-4',
            'validatorVoteBalance'    => 0,
            'validatorPublicKey'      => 'publicKey',
        ],
    ]);

    $walletLast = Wallet::factory()->activeValidator()->create([
        'address'    => 'wallet-last',
        'attributes' => [
            'validatorRank'           => Network::validatorCount(),
            'username'                => 'validator-last',
            'validatorVoteBalance'    => 4000 * 1e8,
            'validatorPublicKey'      => 'publicKey',
        ],
    ]);

    ForgingStats::factory(2)->create([
        'address' => $wallet2->address,
    ]);

    ForgingStats::factory(5)->create([
        'address' => $wallet3->address,
    ]);

    Livewire::test(Validators::class)
        ->call('setIsReady')
        ->set('sortKey', 'missed_blocks')
        ->assertSet('sortDirection', SortDirection::ASC)
        ->assertSeeInOrder([
            $wallet1->address,
            $walletWithoutMissedBlocks->address,
            $walletLast->address,
            $wallet2->address,
            $wallet3->address,
            ...$inactiveWallets->pluck('address'),
            $wallet1->address,
            $walletWithoutMissedBlocks->address,
            $walletLast->address,
            $wallet2->address,
            $wallet3->address,
            ...$inactiveWallets->pluck('address'),
        ]);
});

it('should sort missed blocks in descending order grouped by rank', function () {
    $wallet1 = Wallet::factory()->activeValidator()->create([
        'address'    => 'wallet-1',
        'attributes' => [
            'validatorRank'           => 1,
            'username'                => 'validator-1',
            'validatorVoteBalance'    => 10000 * 1e18,
            'validatorPublicKey'      => 'publicKey',
        ],
    ]);

    $wallet2 = Wallet::factory()->activeValidator()->create([
        'address'    => 'wallet-2',
        'attributes' => [
            'validatorRank'           => 2,
            'username'                => 'validator-2',
            'validatorVoteBalance'    => 10000 * 1e18,
            'validatorPublicKey'      => 'publicKey',
        ],
    ]);

    $wallet3 = Wallet::factory()->activeValidator()->create([
        'address'    => 'wallet-3',
        'attributes' => [
            'validatorRank'           => 3,
            'username'                => 'validator-3',
            'validatorVoteBalance'    => 4000 * 1e18,
            'validatorPublicKey'      => 'publicKey',
        ],
    ]);

    $inactiveWallets = collect();
    foreach (range(60, 72) as $rank) {
        $inactiveWallets[] = Wallet::factory()->activeValidator()->create([
            'attributes' => [
                'validatorRank'           => $rank,
                'username'                => 'validator-'.$rank,
                'validatorVoteBalance'    => 0,
                'validatorPublicKey'      => 'publicKey',
            ],
        ]);
    }

    $walletWithoutMissedBlocks = Wallet::factory()->activeValidator()->create([
        'address'    => 'wallet-4',
        'attributes' => [
            'validatorRank'           => 4,
            'username'                => 'validator-4',
            'validatorVoteBalance'    => 0,
            'validatorPublicKey'      => 'publicKey',
        ],
    ]);

    $walletLast = Wallet::factory()->activeValidator()->create([
        'address'    => 'wallet-last',
        'attributes' => [
            'validatorRank'           => Network::validatorCount(),
            'username'                => 'validator-last',
            'validatorVoteBalance'    => 4000 * 1e8,
            'validatorPublicKey'      => 'publicKey',
        ],
    ]);

    ForgingStats::factory(2)->create([
        'address' => $wallet2->address,
    ]);

    ForgingStats::factory(5)->create([
        'address' => $wallet3->address,
    ]);

    Livewire::test(Validators::class)
        ->call('setIsReady')
        ->set('sortKey', 'missed_blocks')
        ->set('sortDirection', SortDirection::DESC)
        ->assertSeeInOrder([
            $wallet3->address,
            $wallet2->address,
            $wallet1->address,
            $walletWithoutMissedBlocks->address,
            $walletLast->address,
            ...$inactiveWallets->pluck('address'),
            $wallet3->address,
            $wallet2->address,
            $wallet1->address,
            $walletWithoutMissedBlocks->address,
            $walletLast->address,
            ...$inactiveWallets->pluck('address'),
        ]);
});

it('should alternate sorting direction', function () {
    $validatorCache = new ValidatorCache();
    $validatorCache->setAllVoterCounts(
        Wallet::factory(Network::validatorCount())
            ->activeValidator()
            ->create()
            ->mapWithKeys(fn ($validator) => [$validator->public_key => 1])
            ->toArray()
    );

    ForgingStats::factory(24)->create();

    $component = Livewire::test(Validators::class)
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
    $validatorCache = new ValidatorCache();
    $validatorCache->setAllVoterCounts(
        Wallet::factory(Network::validatorCount())
            ->activeValidator()
            ->create()
            ->mapWithKeys(fn ($validator) => [$validator->public_key => 1])
            ->toArray()
    );

    ForgingStats::factory(24)->create();

    $component = Livewire::test(Validators::class)
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
    Livewire::test(Validators::class)
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
    Route::get('/test-validators', function () {
        return BladeCompiler::render('<livewire:validators.validators :defer-loading="false" />');
    });

    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorRank'           => 2,
            'username'                => 'validator-1',
            'validatorVoteBalance'    => 10000 * 1e18,
            'validatorPublicKey'      => 'publicKey',
            'validatorProducedBlocks' => 1000,
        ],
    ]);

    $wallet2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorRank'           => 1,
            'username'                => 'validator-2',
            'validatorVoteBalance'    => 4000 * 1e18,
            'validatorPublicKey'      => 'publicKey',
            'validatorProducedBlocks' => 1000,
        ],
    ]);

    $this->get('/test-validators?sort=name&sort-direction=asc')
        ->assertSeeInOrder([
            $wallet1->address,
            $wallet2->address,
        ]);

    $this->get('/test-validators?sort=name&sort-direction=desc')
        ->assertSeeInOrder([
            $wallet2->address,
            $wallet1->address,
        ]);
});

it('should force ascending if invalid query string value', function () {
    Route::get('/test-validators', function () {
        return BladeCompiler::render('<livewire:validators.validators :defer-loading="false" />');
    });

    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorRank'           => 2,
            'username'                => 'validator-1',
            'validatorVoteBalance'    => 10000 * 1e18,
            'validatorPublicKey'      => 'publicKey',
            'validatorProducedBlocks' => 1000,
        ],
    ]);

    $wallet2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorRank'           => 1,
            'username'                => 'validator-2',
            'validatorVoteBalance'    => 4000 * 1e18,
            'validatorPublicKey'      => 'publicKey',
            'validatorProducedBlocks' => 1000,
        ],
    ]);

    $this->get('/test-validators?sort=name&sort-direction=desc')
        ->assertSeeInOrder([
            $wallet2->address,
            $wallet1->address,
        ]);

    $this->get('/test-validators?sort=name&sort-direction=testing')
        ->assertSeeInOrder([
            $wallet1->address,
            $wallet2->address,
        ]);
});

it('should handle sorting several pages of validators without cached data', function ($columnSortBy, $modelSortBy) {
    $validatorData = [];
    foreach (range(1, 145) as $rank) {
        $wallet          = faker()->wallet;
        $validatorData[] = [
            'id'                => faker()->uuid,
            'balance'           => faker()->numberBetween(1, 1000) * 1e18,
            'nonce'             => faker()->numberBetween(1, 1000),
            'attributes'        => [
                'username'                => faker()->userName,
                'validatorVoteBalance'    => faker()->numberBetween(1, 1000) * 1e18,
                'validatorPublicKey'      => 'publicKey',
                'validatorProducedBlocks' => faker()->numberBetween(1, 1000),
                'validatorMissedBlocks'   => faker()->numberBetween(1, 1000),
            ],
            'updated_at'       => faker()->numberBetween(1, 1000),

            'address'    => $wallet['address'],
            'public_key' => $wallet['publicKey'],
            'attributes' => json_encode([
                'validatorRank'           => $rank,
                'username'                => 'validator-'.$rank,
                'validatorVoteBalance'    => random_int(1000, 10000) * 1e18,
                'validatorPublicKey'      => 'publicKey',
            ]),
        ];
    }

    Wallet::insert($validatorData);

    $validators = Wallet::all()->sort(function ($a, $b) use ($modelSortBy) {
        $bValue = Arr::get($b, $modelSortBy);
        $aValue = Arr::get($a, $modelSortBy);

        if (is_numeric($bValue) && is_numeric($aValue)) {
            if ($aValue > $bValue) {
                return 1;
            }

            return $aValue < $bValue ? -1 : 0;
        }

        return strcmp($aValue, $bValue);
    });

    $component = Livewire::test(Validators::class)
        ->call('setIsReady')
        ->call('sortBy', $columnSortBy)
        ->set('sortDirection', SortDirection::ASC);

    foreach (range(1, 3) as $page) {
        $pageValidators = $validators->chunk(Network::validatorCount())->get($page - 1)->pluck('address');

        $component->call('gotoPage', $page)
            ->assertSeeInOrder([
                ...$pageValidators,
                ...$pageValidators,
            ]);
    }
})->with([
    'rank'             => ['rank', 'attributes.validatorRank'],
    'name'             => ['name', 'attributes.username'],
    'no_of_voters'     => ['no_of_voters', 'attributes.validatorRank'],
    'votes'            => ['votes', 'attributes.validatorVoteBalance'],
    'percentage_votes' => ['percentage_votes', 'attributes.validatorVoteBalance'],
    'missed_blocks'    => ['missed_blocks', 'attributes.validatorRank'],
]);

it('should handle sorting several pages of validators with cached data', function ($columnSortBy, $modelSortBy) {
    $validatorData = [];
    foreach (range(1, 145) as $rank) {
        $wallet          = faker()->wallet;
        $validatorData[] = [
            'id'                => faker()->uuid,
            'balance'           => faker()->numberBetween(1, 1000) * 1e18,
            'nonce'             => faker()->numberBetween(1, 1000),
            'updated_at'        => faker()->numberBetween(1, 1000),

            'address'    => 'wallet-'.$rank,
            'public_key' => $wallet['publicKey'],
            'attributes' => json_encode([
                'validatorRank'           => $rank,
                'username'                => 'validator-'.$rank,
                'validatorVoteBalance'    => random_int(1000, 10000) * 1e18,
                'validatorPublicKey'      => 'publicKey',
                'validatorProducedBlocks' => faker()->numberBetween(1, 1000),
                'validatorMissedBlocks'   => faker()->numberBetween(1, 1000),
            ]),
        ];
    }

    Wallet::insert($validatorData);

    $validators = Wallet::all();

    $voterCounts        = [];
    $missedBlocks       = [];
    $missedBlockCounter = 0;

    $missedBlocksData = [];

    foreach ($validators as $validator) {
        $missedBlockCount = random_int(2, 4);
        foreach (range(1, $missedBlockCount) as $_) {
            $missedBlocksData[] = [
                'timestamp'     => Carbon::now()->subHours($missedBlockCounter)->getTimestampMs(),
                'public_key'    => $validator->address,
                'forged'        => faker()->boolean(),
                'missed_height' => faker()->numberBetween(1, 10000),
            ];

            $missedBlockCounter++;
        }

        $voterCounts[$validator->address] = random_int(10, 100);
    }

    ForgingStats::insert($missedBlocksData);

    $missedBlocks = ForgingStats::all()->groupBy('address');

    $validatorCache = new ValidatorCache();
    $validatorCache->setAllVoterCounts($voterCounts);

    $validators = $validators->sort(function ($a, $b) use ($modelSortBy, $missedBlocks, $voterCounts) {
        if ($modelSortBy === 'missed_blocks') {
            $aRank = Arr::get($a, 'attributes.validatorRank');
            $bRank = Arr::get($b, 'attributes.validatorRank');
            if ($aRank <= Network::validatorCount() && $bRank > Network::validatorCount()) {
                return -1;
            } elseif ($aRank > Network::validatorCount() && $bRank <= Network::validatorCount()) {
                return 1;
            }

            $aValue = count($missedBlocks[$a->address]);
            $bValue = count($missedBlocks[$b->address]);

            if ($aValue === $bValue) {
                return $aRank - $bRank;
            }
        } elseif ($modelSortBy === 'no_of_voters') {
            $aValue = $voterCounts[$a->address];
            $bValue = $voterCounts[$b->address];
        } else {
            $aValue = Arr::get($a, $modelSortBy);
            $bValue = Arr::get($b, $modelSortBy);
        }

        if (is_numeric($bValue) && is_numeric($aValue)) {
            if ($aValue > $bValue) {
                return 1;
            }

            return $aValue < $bValue ? -1 : 0;
        }

        return strcmp($aValue, $bValue);
    });

    $component = Livewire::test(Validators::class)
        ->call('setIsReady')
        ->call('sortBy', $columnSortBy)
        ->set('sortDirection', SortDirection::ASC);

    foreach (range(1, 3) as $page) {
        $pageValidators = $validators->chunk(Network::validatorCount())->get($page - 1)->pluck('address');

        $component->call('gotoPage', $page)
            ->assertSeeInOrder([
                ...$pageValidators,
                ...$pageValidators,
            ]);
    }
})->with([
    'rank'             => ['rank', 'attributes.validatorRank'],
    'name'             => ['name', 'attributes.username'],
    'no_of_voters'     => ['no_of_voters', 'no_of_voters'],
    'votes'            => ['votes', 'attributes.validatorVoteBalance'],
    'percentage_votes' => ['percentage_votes', 'attributes.validatorVoteBalance'],
    'missed_blocks'    => ['missed_blocks', 'missed_blocks'],
]);
