<?php

declare(strict_types=1);

use App\Enums\SortDirection;
use App\Http\Livewire\Validators\MissedBlocks;
use App\Models\ForgingStats;
use App\Models\State;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\Cache\ValidatorCache;
use App\Services\Timestamp;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Compilers\BladeCompiler;
use Livewire\Livewire;
use function Tests\faker;

beforeEach(function () {
    State::factory()->create();
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
            'validatorVoteBalance' => (string) BigNumber::new(4000 * 1e18),
        ],
    ]);

    $wallet2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorVoteBalance' => (string) BigNumber::new(4000 * 1e18),
        ],
    ]);

    ForgingStats::factory()->create([
        'address'       => $wallet1->address,
        'missed_height' => 100,
    ]);

    ForgingStats::factory()->create([
        'address'       => $wallet2->address,
        'missed_height' => 134,
    ]);

    Livewire::test(MissedBlocks::class)
        ->call('setIsReady')
        ->call('sortBy', 'height')
        ->assertSet('sortDirection', SortDirection::ASC)
        ->assertSeeInOrder([
            $wallet1->address,
            $wallet2->address,
            $wallet1->address,
            $wallet2->address,
        ]);
});

it('should sort height in descending order', function () {
    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorVoteBalance' => (string) BigNumber::new(4000 * 1e18),
        ],
    ]);

    $wallet2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorVoteBalance' => (string) BigNumber::new(4000 * 1e18),
        ],
    ]);

    ForgingStats::factory()->create([
        'address'       => $wallet1->address,
        'missed_height' => 100,
    ]);

    ForgingStats::factory()->create([
        'address'       => $wallet2->address,
        'missed_height' => 134,
    ]);

    Livewire::test(MissedBlocks::class)
        ->call('setIsReady')
        ->call('sortBy', 'height')
        ->call('sortBy', 'height')
        ->assertSet('sortDirection', SortDirection::DESC)
        ->assertSeeInOrder([
            $wallet2->address,
            $wallet1->address,
            $wallet2->address,
            $wallet1->address,
        ]);
});

it('should sort by age by default', function () {
    $wallet2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorVoteBalance' => (string) BigNumber::new(4000 * 1e18),
        ],
    ]);

    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorVoteBalance' => (string) BigNumber::new(10000 * 1e18),
        ],
    ]);

    ForgingStats::factory()->create([
        'address'    => $wallet1->address,
        'timestamp'  => 100,
    ]);

    ForgingStats::factory()->create([
        'address'    => $wallet2->address,
        'timestamp'  => 134,
    ]);

    Livewire::test(MissedBlocks::class)
        ->call('setIsReady')
        ->assertSet('sortKey', 'age')
        ->assertSet('sortDirection', SortDirection::DESC)
        ->assertSeeInOrder([
            $wallet2->address,
            $wallet1->address,
            $wallet2->address,
            $wallet1->address,
        ]);
});

it('should sort age in ascending order', function () {
    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorVoteBalance' => (string) BigNumber::new(10000 * 1e18),
        ],
    ]);

    $wallet2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorVoteBalance' => (string) BigNumber::new(4000 * 1e18),
        ],
    ]);

    ForgingStats::factory()->create([
        'address'    => $wallet1->address,
        'timestamp'  => 100,
    ]);

    ForgingStats::factory()->create([
        'address'    => $wallet2->address,
        'timestamp'  => 134,
    ]);

    Livewire::test(MissedBlocks::class)
        ->call('setIsReady')
        ->assertSet('sortKey', 'age')
        ->call('sortBy', 'age')
        ->assertSet('sortDirection', SortDirection::ASC)
        ->assertSeeInOrder([
            $wallet1->address,
            $wallet2->address,
            $wallet1->address,
            $wallet2->address,
        ]);
});

it('should sort number of voters in ascending order', function () {
    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorVoteBalance' => (string) BigNumber::new(10000 * 1e18),
        ],
    ]);

    $wallet2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorVoteBalance' => (string) BigNumber::new(4000 * 1e18),
        ],
    ]);

    $walletWithoutVoters = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorVoteBalance' => (string) BigNumber::new(4000 * 1e18),
        ],
    ]);

    ForgingStats::factory()->create([
        'address'    => $wallet1->address,
    ]);

    ForgingStats::factory()->create([
        'address'    => $wallet2->address,
    ]);

    ForgingStats::factory()->create([
        'address'    => $walletWithoutVoters->address,
    ]);

    (new ValidatorCache())->setAllVoterCounts([
        $wallet1->address => 30,
        $wallet2->address => 10,
    ]);

    Livewire::test(MissedBlocks::class)
        ->call('setIsReady')
        ->call('sortBy', 'no_of_voters')
        ->assertSet('sortDirection', SortDirection::ASC)
        ->assertSeeInOrder([
            $wallet2->address,
            $wallet1->address,
            $walletWithoutVoters->address,
            $wallet2->address,
            $wallet1->address,
            $walletWithoutVoters->address,
        ]);
});

it('should sort number of voters in descending order', function () {
    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorVoteBalance' => (string) BigNumber::new(10000 * 1e18),
        ],
    ]);

    $wallet2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorVoteBalance' => (string) BigNumber::new(4000 * 1e18),
        ],
    ]);

    $walletWithoutVoters = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorVoteBalance' => (string) BigNumber::new(4000 * 1e18),
        ],
    ]);

    ForgingStats::factory()->create([
        'address'    => $wallet1->address,
    ]);

    ForgingStats::factory()->create([
        'address'    => $wallet2->address,
    ]);

    ForgingStats::factory()->create([
        'address'    => $walletWithoutVoters->address,
    ]);

    (new ValidatorCache())->setAllVoterCounts([
        $wallet1->address => 30,
        $wallet2->address => 10,
    ]);

    Livewire::test(MissedBlocks::class)
        ->call('setIsReady')
        ->call('sortBy', 'no_of_voters')
        ->call('sortBy', 'no_of_voters')
        ->assertSet('sortDirection', SortDirection::DESC)
        ->assertSeeInOrder([
            $wallet1->address,
            $wallet2->address,
            $walletWithoutVoters->address,
            $wallet1->address,
            $wallet2->address,
            $walletWithoutVoters->address,
        ]);
});

it('should handle no cached votes when sorting by number of voters', function () {
    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorVoteBalance' => (string) BigNumber::new(10000 * 1e18),
        ],
    ]);

    $wallet2 = Wallet::factory()->activeValidator()->create([

        'attributes' => [
            'validatorVoteBalance' => (string) BigNumber::new(4000 * 1e18),
        ],
    ]);

    $walletWithoutVoters = Wallet::factory()->activeValidator()->create([

        'attributes' => [
            'validatorVoteBalance' => (string) BigNumber::new(4000 * 1e18),
        ],
    ]);

    ForgingStats::factory()->create([
        'timestamp'  => 100,
        'address'    => $wallet1->address,
    ]);

    ForgingStats::factory()->create([
        'timestamp'  => 101,
        'address'    => $wallet2->address,
    ]);

    ForgingStats::factory()->create([
        'timestamp'  => 102,
        'address'    => $walletWithoutVoters->address,
    ]);

    Livewire::test(MissedBlocks::class)
        ->call('setIsReady')
        ->call('sortBy', 'no_of_voters')
        ->assertSet('sortDirection', SortDirection::ASC)
        ->assertSeeInOrder([
            $wallet1->address,
            $wallet2->address,
            $walletWithoutVoters->address,
            $wallet1->address,
            $wallet2->address,
            $walletWithoutVoters->address,
        ]);
});

it('should sort votes & percentage in ascending order', function (string $sortKey) {
    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorVoteBalance' => (string) BigNumber::new(10000 * 1e18),
        ],
    ]);

    $wallet2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorVoteBalance' => (string) BigNumber::new(4000 * 1e18),
        ],
    ]);

    ForgingStats::factory()->create([
        'address'    => $wallet1->address,
    ]);

    ForgingStats::factory()->create([
        'address'    => $wallet2->address,
    ]);

    Livewire::test(MissedBlocks::class)
        ->call('setIsReady')
        ->call('sortBy', $sortKey)
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
            'validatorVoteBalance' => (string) BigNumber::new(10000 * 1e18),
        ],
    ]);

    $wallet2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorVoteBalance' => (string) BigNumber::new(4000 * 1e18),
        ],
    ]);

    ForgingStats::factory()->create([
        'address'    => $wallet1->address,
    ]);

    ForgingStats::factory()->create([
        'address'    => $wallet2->address,
    ]);

    Livewire::test(MissedBlocks::class)
        ->call('setIsReady')
        ->call('sortBy', $sortKey)
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

it('should alternate sorting direction', function () {
    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorVoteBalance' => (string) BigNumber::new(10000 * 1e18),
        ],
    ]);

    ForgingStats::factory()->create([
        'address'    => $wallet1->address,
    ]);

    (new ValidatorCache())->setAllVoterCounts([
        $wallet1->address => 30,
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
        ->assertSet('paginators.page', 1)
        ->assertSet('sortKey', 'age')
        ->assertSet('sortDirection', SortDirection::DESC)
        ->call('gotoPage', 12)
        ->call('sortBy', 'age')
        ->assertSet('paginators.page', 1)
        ->assertSet('sortKey', 'age')
        ->assertSet('sortDirection', SortDirection::ASC)
        ->call('gotoPage', 12)
        ->call('sortBy', 'age')
        ->assertSet('paginators.page', 1)
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
            'validatorVoteBalance' => (string) BigNumber::new(4000 * 1e18),
        ],
    ]);

    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-1',
            'validatorVoteBalance' => (string) BigNumber::new(10000 * 1e18),
        ],
    ]);

    ForgingStats::factory()->create([
        'address'    => $wallet1->address,
        'timestamp'  => 100,
    ]);

    ForgingStats::factory()->create([
        'address'    => $wallet2->address,
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
            'validatorVoteBalance' => (string) BigNumber::new(4000 * 1e18),
        ],
    ]);

    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-1',
            'validatorVoteBalance' => (string) BigNumber::new(10000 * 1e18),
        ],
    ]);

    ForgingStats::factory()->create([
        'address'    => $wallet1->address,
        'timestamp'  => 100,
    ]);

    ForgingStats::factory()->create([
        'address'    => $wallet2->address,
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

    $sortByVotesData = [];

    foreach (range(1, 145) as $rank) {
        $wallet          = faker()->wallet;

        $sortByVotesData[$wallet['address']] = random_int(10, 100);

        $validatorData[] = [
            'balance'           => faker()->numberBetween(1, 1000) * 1e18,
            'nonce'             => faker()->numberBetween(1, 1000),
            'updated_at'        => faker()->numberBetween(1, 1000),
            'address'           => $wallet['address'],
            'public_key'        => $wallet['publicKey'],
            'attributes'        => json_encode([
                'validatorRank'           => $rank,
                'validatorVoteBalance'    => (string) BigNumber::new($sortByVotesData[$wallet['address']]),
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
                'address'       => $validator->address,
                'forged'        => faker()->boolean(),
                'missed_height' => faker()->numberBetween(1, 10000),
            ];

            $missedBlockCounter++;
        }
    }

    ForgingStats::insert($missedBlocksData);

    $missedBlocks = ForgingStats::all();

    $missedBlocks = $missedBlocks->sort(function ($a, $b) use ($modelSortBy, $sortByVotesData) {
        if ($modelSortBy === 'validatorVoteBalance') {
            $aValue = $sortByVotesData[$a->address];
            $bValue = $sortByVotesData[$b->address];
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
    'no_of_voters'     => ['no_of_voters', 'timestamp'],
    'votes'            => ['votes', 'validatorVoteBalance'],
    'percentage_votes' => ['percentage_votes', 'validatorVoteBalance'],
]);

it('should handle sorting several pages with cached data', function ($columnSortBy, $modelSortBy) {
    $validatorData = [];

    $sortByVotesData = [];

    foreach (range(1, 145) as $rank) {
        $wallet          = faker()->wallet;

        $sortByVotesData[$wallet['address']] = random_int(10, 100);

        $validatorData[] = [
            'balance'           => faker()->numberBetween(1, 1000) * 1e18,
            'nonce'             => faker()->numberBetween(1, 1000),
            'updated_at'        => faker()->numberBetween(1, 1000),
            'address'           => $wallet['address'],
            'public_key'        => $wallet['publicKey'],
            'attributes'        => json_encode([
                'validatorRank'           => $rank,
                'validatorVoteBalance'    => (string) BigNumber::new($sortByVotesData[$wallet['address']]),
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

    $validators = Wallet::all();

    foreach ($validators as $validator) {
        $missedBlockCount = random_int(2, 4);
        foreach (range(1, $missedBlockCount) as $_) {
            $missedBlocksData[] = [
                'timestamp'     => Timestamp::fromUnix(Carbon::now()->subHours($missedBlockCounter)->unix())->unix(),
                'address'       => $validator->address,
                'forged'        => faker()->boolean(),
                'missed_height' => faker()->numberBetween(1, 10000),
            ];

            $sortByVotesData[$validator->address] = $validator->attributes['validatorVoteBalance'];

            $missedBlockCounter++;
        }

        $voterCounts[$validator->address] = random_int(10, 100);
    }

    ForgingStats::insert($missedBlocksData);

    $missedBlocks = ForgingStats::all();

    $validatorCache = new ValidatorCache();
    $validatorCache->setAllVoterCounts($voterCounts);

    $missedBlocks = $missedBlocks->sort(function ($a, $b) use ($modelSortBy, $voterCounts, $sortByVotesData) {
        if ($modelSortBy === 'no_of_voters') {
            $aValue = $voterCounts[$a->address];
            $bValue = $voterCounts[$b->address];
        } elseif ($modelSortBy === 'votes' || $modelSortBy === 'percentage_votes') {
            $aValue = $sortByVotesData[$a->address];
            $bValue = $sortByVotesData[$b->address];
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
    'no_of_voters'     => ['no_of_voters', 'no_of_voters'],
    'votes'            => ['votes', 'votes'],
    'percentage_votes' => ['percentage_votes', 'percentage_votes'],
]);

it('should not sort for sqlite databases', function ($sortBy) {
    Config::set('database.default', 'sqlite');
    Config::set('database.connections.sqlite.database', ':memory:');

    $this->artisan('migrate:fresh');

    $wallet2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-2',
            'validatorVoteBalance' => (string) BigNumber::new(4000 * 1e18),
        ],
    ]);

    $wallet1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username'             => 'validator-1',
            'validatorVoteBalance' => (string) BigNumber::new(10000 * 1e18),
        ],
    ]);

    ForgingStats::factory()->create([
        'address'    => $wallet1->address,
        'timestamp'  => 100,
    ]);

    ForgingStats::factory()->create([
        'address'    => $wallet2->address,
        'timestamp'  => 134,
    ]);

    // Not missed
    ForgingStats::factory()->create([
        'address'       => $wallet2->address,
        'timestamp'     => 151,
        'missed_height' => null,
    ]);

    Livewire::test(MissedBlocks::class)
        ->call('setIsReady')
        ->call('sortBy', $sortBy)
        ->assertSeeInOrder([
            $wallet2->address,
            $wallet1->address,
            $wallet2->address,
            $wallet1->address,
        ]);
})->with([
    'height',
    'age',
    'no_of_voters',
    'votes',
    'percentage_votes',
]);
