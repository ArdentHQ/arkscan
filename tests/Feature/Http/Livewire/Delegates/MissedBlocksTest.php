<?php

declare(strict_types=1);

use App\Enums\SortDirection;
use App\Http\Livewire\Delegates\MissedBlocks;
use App\Models\ForgingStats;
use App\Models\Wallet;
use App\Services\Cache\DelegateCache;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Compilers\BladeCompiler;
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

it('should sort height in ascending order', function () {
    $wallet1 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'username'    => 'delegate-1',
                'voteBalance' => 4000 * 1e8,
            ],
        ],
    ]);

    $wallet2 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'username'    => 'delegate-2',
                'voteBalance' => 4000 * 1e8,
            ],
        ],
    ]);

    ForgingStats::factory()->create([
        'public_key'    => $wallet1->public_key,
        'missed_height' => 100,
    ]);

    ForgingStats::factory()->create([
        'public_key'    => $wallet2->public_key,
        'missed_height' => 134,
    ]);

    Livewire::test(MissedBlocks::class)
        ->call('setIsReady')
        ->call('sortBy', 'height')
        ->assertSet('sortDirection', SortDirection::ASC)
        ->assertSeeInOrder([
            'delegate-1',
            'delegate-2',
            'delegate-1',
            'delegate-2',
        ]);
});

it('should sort height in descending order', function () {
    $wallet1 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'username'    => 'delegate-1',
                'voteBalance' => 4000 * 1e8,
            ],
        ],
    ]);

    $wallet2 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'username'    => 'delegate-2',
                'voteBalance' => 4000 * 1e8,
            ],
        ],
    ]);

    ForgingStats::factory()->create([
        'public_key'    => $wallet1->public_key,
        'missed_height' => 100,
    ]);

    ForgingStats::factory()->create([
        'public_key'    => $wallet2->public_key,
        'missed_height' => 134,
    ]);

    Livewire::test(MissedBlocks::class)
        ->call('setIsReady')
        ->call('sortBy', 'height')
        ->call('sortBy', 'height')
        ->assertSet('sortDirection', SortDirection::DESC)
        ->assertSeeInOrder([
            'delegate-2',
            'delegate-1',
            'delegate-2',
            'delegate-1',
        ]);
});

it('should sort by age by default', function () {
    $wallet2 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'username'    => 'delegate-2',
                'voteBalance' => 4000 * 1e8,
            ],
        ],
    ]);

    $wallet1 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'username'    => 'delegate-1',
                'voteBalance' => 10000 * 1e8,
            ],
        ],
    ]);

    ForgingStats::factory()->create([
        'public_key' => $wallet1->public_key,
        'timestamp'  => 100,
    ]);

    ForgingStats::factory()->create([
        'public_key' => $wallet2->public_key,
        'timestamp'  => 134,
    ]);

    Livewire::test(MissedBlocks::class)
        ->call('setIsReady')
        ->assertSet('sortKey', 'age')
        ->assertSet('sortDirection', SortDirection::DESC)
        ->assertSeeInOrder([
            'delegate-2',
            'delegate-1',
            'delegate-2',
            'delegate-1',
        ]);
});

it('should sort age in ascending order', function () {
    $wallet1 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'username'    => 'delegate-1',
                'voteBalance' => 10000 * 1e8,
            ],
        ],
    ]);

    $wallet2 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'username'    => 'delegate-2',
                'voteBalance' => 4000 * 1e8,
            ],
        ],
    ]);

    ForgingStats::factory()->create([
        'public_key' => $wallet1->public_key,
        'timestamp'  => 100,
    ]);

    ForgingStats::factory()->create([
        'public_key' => $wallet2->public_key,
        'timestamp'  => 134,
    ]);

    Livewire::test(MissedBlocks::class)
        ->call('setIsReady')
        ->assertSet('sortKey', 'age')
        ->call('sortBy', 'age')
        ->assertSet('sortDirection', SortDirection::ASC)
        ->assertSeeInOrder([
            'delegate-1',
            'delegate-2',
            'delegate-1',
            'delegate-2',
        ]);
});

it('should sort name in ascending order', function () {
    $wallet1 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'username'    => 'delegate-1',
                'voteBalance' => 10000 * 1e8,
            ],
        ],
    ]);

    $wallet2 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'username'    => 'delegate-2',
                'voteBalance' => 4000 * 1e8,
            ],
        ],
    ]);

    ForgingStats::factory()->create([
        'public_key' => $wallet1->public_key,
    ]);

    ForgingStats::factory()->create([
        'public_key' => $wallet2->public_key,
    ]);

    Livewire::test(MissedBlocks::class)
        ->call('setIsReady')
        ->call('sortBy', 'name')
        ->assertSet('sortDirection', SortDirection::ASC)
        ->assertSeeInOrder([
            'delegate-1',
            'delegate-2',
            'delegate-1',
            'delegate-2',
        ]);
});

it('should sort name in descending order', function () {
    $wallet1 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'username'    => 'delegate-1',
                'voteBalance' => 10000 * 1e8,
            ],
        ],
    ]);

    $wallet2 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'username'    => 'delegate-2',
                'voteBalance' => 4000 * 1e8,
            ],
        ],
    ]);

    ForgingStats::factory()->create([
        'public_key' => $wallet1->public_key,
    ]);

    ForgingStats::factory()->create([
        'public_key' => $wallet2->public_key,
    ]);

    Livewire::test(MissedBlocks::class)
        ->call('setIsReady')
        ->call('sortBy', 'name')
        ->set('sortDirection', SortDirection::DESC)
        ->assertSeeInOrder([
            'delegate-2',
            'delegate-1',
            'delegate-2',
            'delegate-1',
        ]);
});

it('should sort number of voters in ascending order', function () {
    $wallet1 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'username'    => 'delegate-1',
                'voteBalance' => 10000 * 1e8,
            ],
        ],
    ]);

    $wallet2 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'username'    => 'delegate-2',
                'voteBalance' => 4000 * 1e8,
            ],
        ],
    ]);

    $walletWithoutVoters = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'username'    => 'delegate-3',
                'voteBalance' => 4000 * 1e8,
            ],
        ],
    ]);

    ForgingStats::factory()->create([
        'public_key' => $wallet1->public_key,
    ]);

    ForgingStats::factory()->create([
        'public_key' => $wallet2->public_key,
    ]);

    ForgingStats::factory()->create([
        'public_key' => $walletWithoutVoters->public_key,
    ]);

    (new DelegateCache())->setAllVoterCounts([
        $wallet1->public_key => 30,
        $wallet2->public_key => 10,
    ]);

    Livewire::test(MissedBlocks::class)
        ->call('setIsReady')
        ->call('sortBy', 'no_of_voters')
        ->assertSet('sortDirection', SortDirection::ASC)
        ->assertSeeInOrder([
            'delegate-2',
            'delegate-1',
            'delegate-3',
            'delegate-2',
            'delegate-1',
            'delegate-3',
        ]);
});

it('should sort number of voters in descending order', function () {
    $wallet1 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'username'    => 'delegate-1',
                'voteBalance' => 10000 * 1e8,
            ],
        ],
    ]);

    $wallet2 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'username'    => 'delegate-2',
                'voteBalance' => 4000 * 1e8,
            ],
        ],
    ]);

    $walletWithoutVoters = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'username'    => 'delegate-3',
                'voteBalance' => 4000 * 1e8,
            ],
        ],
    ]);

    ForgingStats::factory()->create([
        'public_key' => $wallet1->public_key,
    ]);

    ForgingStats::factory()->create([
        'public_key' => $wallet2->public_key,
    ]);

    ForgingStats::factory()->create([
        'public_key' => $walletWithoutVoters->public_key,
    ]);

    (new DelegateCache())->setAllVoterCounts([
        $wallet1->public_key => 30,
        $wallet2->public_key => 10,
    ]);

    Livewire::test(MissedBlocks::class)
        ->call('setIsReady')
        ->call('sortBy', 'no_of_voters')
        ->call('sortBy', 'no_of_voters')
        ->assertSet('sortDirection', SortDirection::DESC)
        ->assertSeeInOrder([
            'delegate-1',
            'delegate-2',
            'delegate-3',
            'delegate-1',
            'delegate-2',
            'delegate-3',
        ]);
});

it('should sort votes & percentage in ascending order', function (string $sortKey) {
    $wallet1 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'username'    => 'delegate-1',
                'voteBalance' => 10000 * 1e8,
            ],
        ],
    ]);

    $wallet2 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'username'    => 'delegate-2',
                'voteBalance' => 4000 * 1e8,
            ],
        ],
    ]);

    ForgingStats::factory()->create([
        'public_key' => $wallet1->public_key,
    ]);

    ForgingStats::factory()->create([
        'public_key' => $wallet2->public_key,
    ]);

    Livewire::test(MissedBlocks::class)
        ->call('setIsReady')
        ->call('sortBy', $sortKey)
        ->assertSet('sortDirection', SortDirection::ASC)
        ->assertSeeInOrder([
            'delegate-2',
            'delegate-1',
            'delegate-2',
            'delegate-1',
        ]);
})->with([
    'votes',
    'percentage_votes',
]);

it('should sort votes & percentage in descending order', function (string $sortKey) {
    $wallet1 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'username'    => 'delegate-1',
                'voteBalance' => 10000 * 1e8,
            ],
        ],
    ]);

    $wallet2 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'username'    => 'delegate-2',
                'voteBalance' => 4000 * 1e8,
            ],
        ],
    ]);

    ForgingStats::factory()->create([
        'public_key' => $wallet1->public_key,
    ]);

    ForgingStats::factory()->create([
        'public_key' => $wallet2->public_key,
    ]);

    Livewire::test(MissedBlocks::class)
        ->call('setIsReady')
        ->call('sortBy', $sortKey)
        ->set('sortDirection', SortDirection::DESC)
        ->assertSeeInOrder([
            'delegate-1',
            'delegate-2',
            'delegate-1',
            'delegate-2',
        ]);
})->with([
    'votes',
    'percentage_votes',
]);

it('should alternate sorting direction', function () {
    $wallet1 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'username'    => 'delegate-1',
                'voteBalance' => 10000 * 1e8,
            ],
        ],
    ]);

    ForgingStats::factory()->create([
        'public_key' => $wallet1->public_key,
    ]);

    (new DelegateCache())->setAllVoterCounts([
        $wallet1->public_key => 30,
    ]);

    $component = Livewire::test(MissedBlocks::class)
        ->call('setIsReady')
        ->assertSet('sortKey', 'age')
        ->assertSet('sortDirection', SortDirection::DESC)
        ->call('sortBy', 'age')
        ->assertSet('sortKey', 'age')
        ->assertSet('sortDirection', SortDirection::ASC);

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
    Livewire::test(MissedBlocks::class)
        ->call('setIsReady')
        ->assertSet('page', 1)
        ->assertSet('sortKey', 'age')
        ->assertSet('sortDirection', SortDirection::DESC)
        ->set('page', 12)
        ->call('sortBy', 'age')
        ->assertSet('page', 1)
        ->assertSet('sortKey', 'age')
        ->assertSet('sortDirection', SortDirection::ASC)
        ->set('page', 12)
        ->call('sortBy', 'age')
        ->assertSet('page', 1)
        ->assertSet('sortKey', 'age')
        ->assertSet('sortDirection', SortDirection::DESC);
});

it('should parse sorting direction from query string', function () {
    Route::get('/test-delegates', function () {
        return BladeCompiler::render('<livewire:delegates.missed-blocks :defer-loading="false" />');
    });

    $wallet2 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'username'    => 'delegate-2',
                'voteBalance' => 4000 * 1e8,
            ],
        ],
    ]);

    $wallet1 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'username'    => 'delegate-1',
                'voteBalance' => 10000 * 1e8,
            ],
        ],
    ]);

    ForgingStats::factory()->create([
        'public_key' => $wallet1->public_key,
        'timestamp'  => 100,
    ]);

    ForgingStats::factory()->create([
        'public_key' => $wallet2->public_key,
        'timestamp'  => 134,
    ]);

    $this->get('/test-delegates?sort=name&sort-direction=asc')
        ->assertSeeInOrder([
            $wallet1->address,
            $wallet2->address,
            $wallet1->address,
            $wallet2->address,
        ]);

    $this->get('/test-delegates?sort=name&sort-direction=desc')
        ->assertSeeInOrder([
            $wallet2->address,
            $wallet1->address,
            $wallet2->address,
            $wallet1->address,
        ]);
});

it('should force ascending if invalid query string value', function () {
    Route::get('/test-delegates', function () {
        return BladeCompiler::render('<livewire:delegates.missed-blocks :defer-loading="false" />');
    });

    $wallet2 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'username'    => 'delegate-2',
                'voteBalance' => 4000 * 1e8,
            ],
        ],
    ]);

    $wallet1 = Wallet::factory()->activeDelegate()->create([
        'attributes' => [
            'delegate' => [
                'username'    => 'delegate-1',
                'voteBalance' => 10000 * 1e8,
            ],
        ],
    ]);

    ForgingStats::factory()->create([
        'public_key' => $wallet1->public_key,
        'timestamp'  => 100,
    ]);

    ForgingStats::factory()->create([
        'public_key' => $wallet2->public_key,
        'timestamp'  => 134,
    ]);

    $this->get('/test-delegates?sort=name&sort-direction=desc')
        ->assertSeeInOrder([
            $wallet2->address,
            $wallet1->address,
            $wallet2->address,
            $wallet1->address,
        ]);

    $this->get('/test-delegates?sort=name&sort-direction=testing')
        ->assertSeeInOrder([
            $wallet1->address,
            $wallet2->address,
            $wallet1->address,
            $wallet2->address,
        ]);
});
