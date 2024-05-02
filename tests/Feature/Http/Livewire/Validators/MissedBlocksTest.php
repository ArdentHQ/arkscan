<?php

declare(strict_types=1);

use App\Enums\SortDirection;
use App\Http\Livewire\Validators\MissedBlocks;
use App\Models\ForgingStats;
use App\Models\State;
use App\Models\Wallet;
use App\Services\Cache\ValidatorCache;
use App\Services\Timestamp;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\Compilers\BladeCompiler;
use Livewire\Livewire;
use function Tests\faker;

beforeEach(function () {
    State::factory()->create();

    if (Schema::hasTable('forging_stats')) {
        ForgingStats::truncate();
    }
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
    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-1',
            'validatorVoteBalance' => 4000 * 1e8,
        ],
    ]);

    $wallet2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-2',
            'validatorVoteBalance' => 4000 * 1e8,
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
            'validator-1',
            'validator-2',
            'validator-1',
            'validator-2',
        ]);
});

it('should sort height in descending order', function () {
    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-1',
            'validatorVoteBalance' => 4000 * 1e8,
        ],
    ]);

    $wallet2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-2',
            'validatorVoteBalance' => 4000 * 1e8,
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
            'validator-2',
            'validator-1',
            'validator-2',
            'validator-1',
        ]);
});

it('should sort by age by default', function () {
    $wallet2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-2',
            'validatorVoteBalance' => 4000 * 1e8,
        ],
    ]);

    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-1',
            'validatorVoteBalance' => 10000 * 1e8,
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
            'validator-2',
            'validator-1',
            'validator-2',
            'validator-1',
        ]);
});

it('should sort age in ascending order', function () {
    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-1',
            'validatorVoteBalance' => 10000 * 1e8,
        ],
    ]);

    $wallet2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-2',
            'validatorVoteBalance' => 4000 * 1e8,
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
            'validator-1',
            'validator-2',
            'validator-1',
            'validator-2',
        ]);
});

it('should sort name in ascending order', function () {
    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-1',
            'validatorVoteBalance' => 10000 * 1e8,
        ],
    ]);

    $wallet2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-2',
            'validatorVoteBalance' => 4000 * 1e8,
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
            'validator-1',
            'validator-2',
            'validator-1',
            'validator-2',
        ]);
});

it('should sort name in descending order', function () {
    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-1',
            'validatorVoteBalance' => 10000 * 1e8,
        ],
    ]);

    $wallet2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-2',
            'validatorVoteBalance' => 4000 * 1e8,
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
            'validator-2',
            'validator-1',
            'validator-2',
            'validator-1',
        ]);
});

it('should sort number of voters in ascending order', function () {
    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-1',
            'validatorVoteBalance' => 10000 * 1e8,
        ],
    ]);

    $wallet2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-2',
            'validatorVoteBalance' => 4000 * 1e8,
        ],
    ]);

    $walletWithoutVoters = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-3',
            'validatorVoteBalance' => 4000 * 1e8,
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

    (new ValidatorCache())->setAllVoterCounts([
        $wallet1->public_key => 30,
        $wallet2->public_key => 10,
    ]);

    Livewire::test(MissedBlocks::class)
        ->call('setIsReady')
        ->call('sortBy', 'no_of_voters')
        ->assertSet('sortDirection', SortDirection::ASC)
        ->assertSeeInOrder([
            'validator-2',
            'validator-1',
            'validator-3',
            'validator-2',
            'validator-1',
            'validator-3',
        ]);
});

it('should sort number of voters in descending order', function () {
    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-1',
            'validatorVoteBalance' => 10000 * 1e8,
        ],
    ]);

    $wallet2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-2',
            'validatorVoteBalance' => 4000 * 1e8,
        ],
    ]);

    $walletWithoutVoters = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-3',
            'validatorVoteBalance' => 4000 * 1e8,
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

    (new ValidatorCache())->setAllVoterCounts([
        $wallet1->public_key => 30,
        $wallet2->public_key => 10,
    ]);

    Livewire::test(MissedBlocks::class)
        ->call('setIsReady')
        ->call('sortBy', 'no_of_voters')
        ->call('sortBy', 'no_of_voters')
        ->assertSet('sortDirection', SortDirection::DESC)
        ->assertSeeInOrder([
            'validator-1',
            'validator-2',
            'validator-3',
            'validator-1',
            'validator-2',
            'validator-3',
        ]);
});

it('should handle no cached votes when sorting by number of voters', function () {
    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-1',
            'validatorVoteBalance' => 10000 * 1e8,
        ],
    ]);

    $wallet2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-2',
            'validatorVoteBalance' => 4000 * 1e8,
        ],
    ]);

    $walletWithoutVoters = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-3',
            'validatorVoteBalance' => 4000 * 1e8,
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

    Livewire::test(MissedBlocks::class)
        ->call('setIsReady')
        ->call('sortBy', 'no_of_voters')
        ->assertSet('sortDirection', SortDirection::ASC)
        ->assertSeeInOrder([
            'validator-1',
            'validator-2',
            'validator-3',
            'validator-1',
            'validator-2',
            'validator-3',
        ]);
});

it('should sort votes & percentage in ascending order', function (string $sortKey) {
    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-1',
            'validatorVoteBalance' => 10000 * 1e8,
        ],
    ]);

    $wallet2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-2',
            'validatorVoteBalance' => 4000 * 1e8,
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
            'validator-2',
            'validator-1',
            'validator-2',
            'validator-1',
        ]);
})->with([
    'votes',
    'percentage_votes',
]);

it('should sort votes & percentage in descending order', function (string $sortKey) {
    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-1',
            'validatorVoteBalance' => 10000 * 1e8,
        ],
    ]);

    $wallet2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-2',
            'validatorVoteBalance' => 4000 * 1e8,
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
            'validator-1',
            'validator-2',
            'validator-1',
            'validator-2',
        ]);
})->with([
    'votes',
    'percentage_votes',
]);

it('should alternate sorting direction', function () {
    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-1',
            'validatorVoteBalance' => 10000 * 1e8,
        ],
    ]);

    ForgingStats::factory()->create([
        'public_key' => $wallet1->public_key,
    ]);

    (new ValidatorCache())->setAllVoterCounts([
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

it('should handle empty table', function () {
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
    Route::get('/test-validators', function () {
        return BladeCompiler::render('<livewire:validators.missed-blocks :defer-loading="false" />');
    });

    $wallet2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-2',
            'validatorVoteBalance' => 4000 * 1e8,
        ],
    ]);

    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-1',
            'validatorVoteBalance' => 10000 * 1e8,
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

    $this->get('/test-validators?sort=name&sort-direction=asc')
        ->assertSeeInOrder([
            $wallet1->address,
            $wallet2->address,
            $wallet1->address,
            $wallet2->address,
        ]);

    $this->get('/test-validators?sort=name&sort-direction=desc')
        ->assertSeeInOrder([
            $wallet2->address,
            $wallet1->address,
            $wallet2->address,
            $wallet1->address,
        ]);
});

it('should force ascending if invalid query string value', function () {
    Route::get('/test-validators', function () {
        return BladeCompiler::render('<livewire:validators.missed-blocks :defer-loading="false" />');
    });

    $wallet2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-2',
            'validatorVoteBalance' => 4000 * 1e8,
        ],
    ]);

    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-1',
            'validatorVoteBalance' => 10000 * 1e8,
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

    $this->get('/test-validators?sort=name&sort-direction=desc')
        ->assertSeeInOrder([
            $wallet2->address,
            $wallet1->address,
            $wallet2->address,
            $wallet1->address,
        ]);

    $this->get('/test-validators?sort=name&sort-direction=testing')
        ->assertSeeInOrder([
            $wallet1->address,
            $wallet2->address,
            $wallet1->address,
            $wallet2->address,
        ]);
});

it('should handle sorting several pages without cached data', function ($columnSortBy, $modelSortBy) {
    $validatorData = [];
    foreach (range(1, 145) as $rank) {
        $wallet          = faker()->wallet;
        $validatorData[] = [
            'id'                => faker()->uuid,
            'balance'           => faker()->numberBetween(1, 1000) * 1e8,
            'nonce'             => faker()->numberBetween(1, 1000),
            'updated_at'        => faker()->numberBetween(1, 1000),

            'address'    => $wallet['address'],
            'public_key' => $wallet['publicKey'],
            'attributes' => json_encode([
                'validatorRank'           => $rank,
                'username'                => 'validator-'.$rank,
                'validatorVoteBalance'    => random_int(1000, 10000) * 1e8,
                'validatorProducedBlocks' => faker()->numberBetween(1, 1000),
                'validatorMissedBlocks'   => faker()->numberBetween(1, 1000),
            ]),
        ];
    }

    Wallet::insert($validatorData);

    $missedBlocks       = [];
    $missedBlockCounter = 0;

    $missedBlocksData = [];

    $validators = Wallet::all();

    foreach ($validators as $validator) {
        $missedBlockCount = random_int(2, 4);
        foreach (range(1, $missedBlockCount) as $_) {
            $missedBlocksData[] = [
                'timestamp'     => Timestamp::fromUnix(Carbon::now()->subHours($missedBlockCounter)->unix())->unix(),
                'public_key'    => $validator->public_key,
                'forged'        => faker()->boolean(),
                'missed_height' => faker()->numberBetween(1, 10000),
            ];

            $missedBlockCounter++;
        }

        $voterCounts[$validator->public_key] = random_int(10, 100);
    }

    ForgingStats::insert($missedBlocksData);

    $missedBlocks = ForgingStats::all();

    $missedBlocks = $missedBlocks->sort(function ($a, $b) use ($modelSortBy) {
        $aValue = Arr::get($a, $modelSortBy);
        $bValue = Arr::get($b, $modelSortBy);

        if (is_numeric($bValue) && is_numeric($aValue)) {
            return (int) $aValue - (int) $bValue;
        }

        return strcmp($aValue, $bValue);
    });

    $component = Livewire::test(MissedBlocks::class)
        ->call('setIsReady')
        ->call('sortBy', $columnSortBy)
        ->set('sortDirection', SortDirection::ASC);

    foreach (range(1, 4) as $page) {
        $pageData = $missedBlocks->chunk(25)->get($page - 1)->pluck('address');

        $component->call('gotoPage', $page)
            ->assertSeeInOrder([
                ...$pageData,
                ...$pageData,
            ]);
    }
})->with([
    'height'           => ['height', 'missed_height'],
    'age'              => ['age', 'timestamp'],
    'name'             => ['name', 'timestamp'],
    'no_of_voters'     => ['no_of_voters', 'timestamp'],
    'votes'            => ['votes', 'timestamp'],
    'percentage_votes' => ['percentage_votes', 'timestamp'],
]);

it('should handle sorting several pages with cached data', function ($columnSortBy, $modelSortBy) {
    $validatorData = [];
    foreach (range(1, 145) as $rank) {
        $wallet          = faker()->wallet;
        $validatorData[] = [
            'id'                => faker()->uuid,
            'balance'           => faker()->numberBetween(1, 1000) * 1e8,
            'nonce'             => faker()->numberBetween(1, 1000),
            'updated_at'        => faker()->numberBetween(1, 1000),

            'address'    => $wallet['address'],
            'public_key' => $wallet['publicKey'],
            'attributes' => json_encode([
                'validatorRank'           => $rank,
                'username'                => 'validator-'.$rank,
                'validatorVoteBalance'    => random_int(1000, 10000) * 1e8,
                'validatorProducedBlocks' => faker()->numberBetween(1, 1000),
                'validatorMissedBlocks'   => faker()->numberBetween(1, 1000),
            ]),
        ];
    }

    Wallet::insert($validatorData);

    $voterCounts        = [];
    $missedBlocks       = [];
    $missedBlockCounter = 0;

    $missedBlocksData = [];

    $validators = Wallet::all()->keyBy('public_key');

    foreach ($validators as $validator) {
        $missedBlockCount = random_int(2, 4);
        foreach (range(1, $missedBlockCount) as $_) {
            $missedBlocksData[] = [
                'timestamp'     => Timestamp::fromUnix(Carbon::now()->subHours($missedBlockCounter)->unix())->unix(),
                'public_key'    => $validator->public_key,
                'forged'        => faker()->boolean(),
                'missed_height' => faker()->numberBetween(1, 10000),
            ];

            $missedBlockCounter++;
        }

        $voterCounts[$validator->public_key] = random_int(10, 100);
    }

    ForgingStats::insert($missedBlocksData);

    $missedBlocks = ForgingStats::all();

    $validatorCache = new ValidatorCache();
    $validatorCache->setAllVoterCounts($voterCounts);

    $missedBlocks = $missedBlocks->sort(function ($a, $b) use ($modelSortBy, $voterCounts, $validators) {
        if ($modelSortBy === 'no_of_voters') {
            $aValue = $voterCounts[$a->public_key];
            $bValue = $voterCounts[$b->public_key];
        } elseif ($modelSortBy === 'votes' || $modelSortBy === 'percentage_votes') {
            $aValue = Arr::get($validators[$a->public_key], 'attributes.validatorVoteBalance');
            $bValue = Arr::get($validators[$b->public_key], 'attributes.validatorVoteBalance');
        } elseif ($modelSortBy === 'name') {
            $aValue = Arr::get($validators[$a->public_key], 'attributes.username');
            $bValue = Arr::get($validators[$b->public_key], 'attributes.username');
        } else {
            $aValue = Arr::get($a, $modelSortBy);
            $bValue = Arr::get($b, $modelSortBy);
        }

        if (is_numeric($bValue) && is_numeric($aValue)) {
            return (int) $aValue - (int) $bValue;
        }

        return strcmp($aValue, $bValue);
    });

    $component = Livewire::test(MissedBlocks::class)
        ->call('setIsReady')
        ->call('sortBy', $columnSortBy)
        ->set('sortDirection', SortDirection::ASC);

    foreach (range(1, 4) as $page) {
        $pageData = $missedBlocks->chunk(25)->get($page - 1)->pluck('address');

        $component->call('gotoPage', $page)
            ->assertSeeInOrder([
                ...$pageData,
                ...$pageData,
            ]);
    }
})->with([
    'height'           => ['height', 'missed_height'],
    'age'              => ['age', 'timestamp'],
    'name'             => ['name', 'name'],
    'no_of_voters'     => ['no_of_voters', 'no_of_voters'],
    'votes'            => ['votes', 'votes'],
    'percentage_votes' => ['percentage_votes', 'percentage_votes'],
]);

it('should not sort for sqlite databases', function ($sortBy) {
    Config::set('database.default', 'sqlite');
    Config::set('database.connections.sqlite.database', ':memory:');

    $this->refreshDatabase();

    $wallet2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-2',
            'validatorVoteBalance' => 4000 * 1e8,
        ],
    ]);

    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-1',
            'validatorVoteBalance' => 10000 * 1e8,
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

    // Not missed
    ForgingStats::factory()->create([
        'public_key'    => $wallet2->public_key,
        'timestamp'     => 151,
        'missed_height' => null,
    ]);

    Livewire::test(MissedBlocks::class)
        ->call('setIsReady')
        ->call('sortBy', $sortBy)
        ->assertSeeInOrder([
            'validator-2',
            'validator-1',
            'validator-2',
            'validator-1',
        ]);
})->with([
    'height',
    'age',
    'name',
    'no_of_voters',
    'votes',
    'percentage_votes',
]);
