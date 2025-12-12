<?php

declare(strict_types=1);

use App\Models\ForgingStats;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\Cache\ValidatorCache;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Laravel\Dusk\Browser;

beforeEach(function () {
    $this->withoutExceptionHandling();
});

afterEach(function () {
    Cache::tags(['dusk'])->flush();
});

describe('Missed Blocks Tab', function () {
    it('should handle no missed blocks', function ($resolution) {
        $this->browse(function (Browser $browser) use ($resolution) {
            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('validators')
                ->click('button#tab-missed-blocks')
                ->waitForText(trans('tables.missed-blocks.no_results'));
        });
    })->with('resolutions');

    it('should display missed blocks', function ($resolution) {
        ForgingStats::factory()->create([
            'missed_height' => 100,
        ]);

        ForgingStats::factory()->create([
            'missed_height' => 134,
        ]);

        $this->browse(function (Browser $browser) use ($resolution) {
            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('validators')
                ->click('button#tab-missed-blocks')
                ->waitForText('2 results', ignoreCase: true);
        });
    })->with('resolutions');

    it('should go to page 2', function ($resolution) {
        for ($i = 14234; $i < 14234 + 30; $i++) {
            ForgingStats::factory()
                ->create([
                    'missed_height' => $i,
                ]);
        }

        $this->browse(function (Browser $browser) use ($resolution) {
            $sortedBlocks = ForgingStats::orderBy('missed_height', 'desc')
                ->whereNotNull('missed_height');

            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('validators')
                ->click('button#tab-missed-blocks')
                ->waitForText('30 results', ignoreCase: true)
                ->click('[data-testid="pagination:next-page"] button')
                ->waitForText('Page 2 of 2')
                ->assertQueryStringHas('page', '2');

            foreach ($sortedBlocks->skip(25)->take(5)->get() as $block) {
                $browser->assertSee(number_format($block->missed_height));
            }
        });
    })->with('resolutions');

    it('should reset to page 1 on per-page change', function ($resolution) {
        for ($i = 14234; $i < 14234 + 30; $i++) {
            ForgingStats::factory()
                ->create([
                    'missed_height' => $i,
                ]);
        }

        $this->browse(function (Browser $browser) use ($resolution) {
            $sortedBlocks = ForgingStats::orderBy('missed_height', 'desc')
                ->whereNotNull('missed_height');

            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('validators', ['tab' => 'missed-blocks', 'page' => 2])
                ->waitForText('30 results', ignoreCase: true)
                ->assertSee('Page 2 of 2')
                ->click('[data-testid="pagination:per-page-dropdown:button"]')
                ->waitForTextIn('[data-testid="pagination:per-page-dropdown:dropdown"]', '10')
                ->clickAtXPath('//div[@data-testid="pagination:per-page-dropdown:dropdown"]//span[.//text()="10"]')
                ->waitForText('Page 1 of 3');

            foreach ($sortedBlocks->take(10)->get() as $block) {
                $browser->assertSee(number_format($block->missed_height));
            }
        });
    })->with('resolutions');

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

        $this->browse(function (Browser $browser) use ($wallet1, $wallet2) {
            $browser->resize(1280, 1024);

            $browser->visitRoute('validators', ['tab' => 'missed-blocks'])
                ->waitForText('2 results', ignoreCase: true)
                ->click('[data-testid="table:header:sortable:height"]')
                ->waitForSeeInOrder([
                    $wallet1->address,
                    $wallet2->address,
                ], ignoreCase: true);
        });
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

        $this->browse(function (Browser $browser) use ($wallet1, $wallet2) {
            $browser->resize(1280, 1024);

            $browser->visitRoute('validators', ['tab' => 'missed-blocks'])
                ->waitForText('2 results', ignoreCase: true)
                ->click('[data-testid="table:header:sortable:height"]')
                ->click('[data-testid="table:header:sortable:height"]')
                ->waitForSeeInOrder([
                    $wallet2->address,
                    $wallet1->address,
                ], ignoreCase: true);
        });
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
            'address'   => $wallet1->address,
            'timestamp' => 100,
        ]);

        ForgingStats::factory()->create([
            'address'   => $wallet2->address,
            'timestamp' => 134,
        ]);

        $this->browse(function (Browser $browser) use ($wallet1, $wallet2) {
            $browser->resize(1280, 1024);

            $browser->visitRoute('validators', ['tab' => 'missed-blocks'])
                ->waitForText('2 results', ignoreCase: true)
                ->waitForSeeInOrder([
                    $wallet2->address,
                    $wallet1->address,
                ], ignoreCase: true);
        });
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
            'address'   => $wallet1->address,
            'timestamp' => 100,
        ]);

        ForgingStats::factory()->create([
            'address'   => $wallet2->address,
            'timestamp' => 134,
        ]);

        $this->browse(function (Browser $browser) use ($wallet1, $wallet2) {
            $browser->resize(1280, 1024);

            $browser->visitRoute('validators', ['tab' => 'missed-blocks'])
                ->waitForText('2 results', ignoreCase: true)
                ->click('[data-testid="table:header:sortable:age"]')
                ->waitForSeeInOrder([
                    $wallet1->address,
                    $wallet2->address,
                ], ignoreCase: true);
        });
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
            'address' => $wallet1->address,
        ]);

        ForgingStats::factory()->create([
            'address' => $wallet2->address,
        ]);

        ForgingStats::factory()->create([
            'address' => $walletWithoutVoters->address,
        ]);

        (new ValidatorCache())->setAllVoterCounts([
            $wallet1->address => 30,
            $wallet2->address => 10,
        ]);

        $this->browse(function (Browser $browser) use ($wallet1, $wallet2, $walletWithoutVoters) {
            $browser->resize(1280, 1024);

            $browser->visitRoute('validators', ['tab' => 'missed-blocks'])
                ->waitForText('3 results', ignoreCase: true)
                ->click('[data-testid="table:header:sortable:no_of_voters"]')
                ->waitForSeeInOrder([
                    $wallet2->address,
                    $wallet1->address,
                    $walletWithoutVoters->address,
                ], ignoreCase: true);
        });
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
            'address' => $wallet1->address,
        ]);

        ForgingStats::factory()->create([
            'address' => $wallet2->address,
        ]);

        ForgingStats::factory()->create([
            'address' => $walletWithoutVoters->address,
        ]);

        (new ValidatorCache())->setAllVoterCounts([
            $wallet1->address => 30,
            $wallet2->address => 10,
        ]);

        $this->browse(function (Browser $browser) use ($wallet1, $wallet2, $walletWithoutVoters) {
            $browser->resize(1280, 1024);

            $browser->visitRoute('validators', ['tab' => 'missed-blocks'])
                ->waitForText('3 results', ignoreCase: true)
                ->click('[data-testid="table:header:sortable:no_of_voters"]')
                ->click('[data-testid="table:header:sortable:no_of_voters"]')
                ->waitForSeeInOrder([
                    $wallet1->address,
                    $wallet2->address,
                    $walletWithoutVoters->address,
                ], ignoreCase: true);
        });
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
            'timestamp' => 100,
            'address'   => $wallet1->address,
        ]);

        ForgingStats::factory()->create([
            'timestamp' => 101,
            'address'   => $wallet2->address,
        ]);

        ForgingStats::factory()->create([
            'timestamp' => 102,
            'address'   => $walletWithoutVoters->address,
        ]);

        $this->browse(function (Browser $browser) use ($wallet1, $wallet2, $walletWithoutVoters) {
            $browser->resize(1280, 1024);

            $browser->visitRoute('validators', ['tab' => 'missed-blocks'])
                ->waitForText('3 results', ignoreCase: true)
                ->click('[data-testid="table:header:sortable:no_of_voters"]')
                ->waitForSeeInOrder([
                    $wallet1->address,
                    $wallet2->address,
                    $walletWithoutVoters->address,
                ], ignoreCase: true);
        });
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
            'address' => $wallet1->address,
        ]);

        ForgingStats::factory()->create([
            'address' => $wallet2->address,
        ]);

        $this->browse(function (Browser $browser) use ($wallet1, $wallet2, $sortKey) {
            $browser->resize(1280, 1024);

            $browser->visitRoute('validators', ['tab' => 'missed-blocks'])
                ->waitForText('2 results', ignoreCase: true)
                ->click('[data-testid="table:header:sortable:'.$sortKey.'"]')
                ->waitForSeeInOrder([
                    $wallet2->address,
                    $wallet1->address,
                ], ignoreCase: true);
        });
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
            'address' => $wallet1->address,
        ]);

        ForgingStats::factory()->create([
            'address' => $wallet2->address,
        ]);

        // Livewire::test(Tabs::class)
        //     ->set('view', 'missed-blocks')
        //     ->call('setMissedBlocksReady')
        //     ->call('sortBy', $sortKey)
        //     ->set('sortDirections.missed-blocks', SortDirection::DESC)
        //     ->assertSeeInOrder([
        //         $wallet1->address,
        //         $wallet2->address,
        //         $wallet1->address,
        //         $wallet2->address,
        //     ]);

        $this->browse(function (Browser $browser) use ($wallet1, $wallet2, $sortKey) {
            $browser->resize(1280, 1024);

            $browser->visitRoute('validators', ['tab' => 'missed-blocks'])
                ->waitForText('2 results', ignoreCase: true)
                ->click('[data-testid="table:header:sortable:'.$sortKey.'"]')
                ->click('[data-testid="table:header:sortable:'.$sortKey.'"]')
                ->waitForSeeInOrder([
                    $wallet1->address,
                    $wallet2->address,
                ], ignoreCase: true);
        });
    })->with([
        'votes',
        'percentage_votes',
    ]);

    // it('should alternate sorting direction', function () {
    //     $wallet1 = Wallet::factory()->activeValidator()->create([
    //         'attributes' => [
    //             'validatorVoteBalance' => (string) BigNumber::new(10000 * 1e18),
    //         ],
    //     ]);

    //     ForgingStats::factory()->create([
    //         'address'    => $wallet1->address,
    //     ]);

    //     (new ValidatorCache())->setAllVoterCounts([
    //         $wallet1->address => 30,
    //     ]);

    //     $component = Livewire::test(Tabs::class)
    //         ->set('view', 'missed-blocks')
    //         ->call('setMissedBlocksReady')
    //         ->assertSet('sortKeys.missed-blocks', 'age')
    //         ->assertSet('sortDirections.missed-blocks', SortDirection::DESC)
    //         ->call('sortBy', 'age')
    //         ->assertSet('sortKeys.missed-blocks', 'age')
    //         ->assertSet('sortDirections.missed-blocks', SortDirection::ASC);

    //     foreach (['name', 'no_of_voters', 'votes', 'percentage_votes', 'missed_blocks'] as $column) {
    //         $component->call('sortBy', $column)
    //             ->assertSet('sortKeys.missed-blocks', $column)
    //             ->assertSet('sortDirections.missed-blocks', SortDirection::ASC)
    //             ->call('sortBy', $column)
    //             ->assertSet('sortKeys.missed-blocks', $column)
    //             ->assertSet('sortDirections.missed-blocks', SortDirection::DESC);
    //     }
    // });

    // it('should handle empty table', function () {
    //     $component = Livewire::test(Tabs::class)
    //         ->set('view', 'missed-blocks')
    //         ->call('setMissedBlocksReady')
    //         ->assertSet('sortKeys.missed-blocks', 'age')
    //         ->assertSet('sortDirections.missed-blocks', SortDirection::DESC)
    //         ->call('sortBy', 'age')
    //         ->assertSet('sortKeys.missed-blocks', 'age')
    //         ->assertSet('sortDirections.missed-blocks', SortDirection::ASC);

    //     foreach (['name', 'no_of_voters', 'votes', 'percentage_votes', 'missed_blocks'] as $column) {
    //         $component->call('sortBy', $column)
    //             ->assertSet('sortKeys.missed-blocks', $column)
    //             ->assertSet('sortDirections.missed-blocks', SortDirection::ASC)
    //             ->call('sortBy', $column)
    //             ->assertSet('sortKeys.missed-blocks', $column)
    //             ->assertSet('sortDirections.missed-blocks', SortDirection::DESC);
    //     }
    // });

    it('should reset page on sorting change', function () {
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

        ForgingStats::factory(20)->create([
            'address' => $wallet1->address,
        ]);

        ForgingStats::factory(20)->create([
            'address' => $wallet2->address,
        ]);

        $this->browse(function (Browser $browser) {
            $browser->resize(1280, 1024);

            $browser->visitRoute('validators', ['tab' => 'missed-blocks'])
                ->waitForText('40 results', ignoreCase: true)
                ->click('[data-testid="pagination:next-page"] button')
                ->pause(400)
                ->waitForText('Page 2 of 2')
                ->click('[data-testid="table:header:sortable:age"]')
                ->waitForText('Page 1 of 2')
                ->click('[data-testid="pagination:next-page"] button')
                ->pause(400)
                ->waitForText('Page 2 of 2')
                ->click('[data-testid="table:header:sortable:age"]')
                ->waitForText('Page 1 of 2');
        });
    });

    it('should parse sorting direction from query string', function () {
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

        $this->browse(function (Browser $browser) {
            $browser->resize(1280, 1024);

            $browser->visitRoute('validators', ['tab' => 'missed-blocks', 'sort' => 'name', 'sort-direction' => 'asc'])
                ->waitForText('2 results', ignoreCase: true)
                ->waitForSeeInOrder([
                    'validator-1',
                    'validator-2',
                ], ignoreCase: true)
                ->visitRoute('validators', ['tab' => 'missed-blocks', 'sort' => 'name', 'sort-direction' => 'desc'])
                ->waitForText('2 results', ignoreCase: true)
                ->waitForSeeInOrder([
                    'validator-2',
                    'validator-1',
                ], ignoreCase: true);
        });
    });

    it('should force ascending if invalid query string value', function () {
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

        $this->browse(function (Browser $browser) {
            $browser->resize(1280, 1024);

            $browser->visitRoute('validators', ['tab' => 'missed-blocks', 'sort' => 'name', 'sort-direction' => 'desc'])
                ->waitForText('2 results', ignoreCase: true)
                ->waitForSeeInOrder([
                    'validator-2',
                    'validator-1',
                ], ignoreCase: true)
                ->visitRoute('validators', ['tab' => 'missed-blocks', 'sort' => 'name', 'sort-direction' => 'testing'])
                ->waitForText('2 results', ignoreCase: true)
                ->waitForSeeInOrder([
                    'validator-1',
                    'validator-2',
                ], ignoreCase: true);
        });
    });

    // it('should handle sorting several pages without cached data', function ($columnSortBy, $modelSortBy) {
    //     $validatorData = [];

    //     $sortByVotesData = [];

    //     foreach (range(1, 145) as $rank) {
    //         $wallet          = faker()->wallet;

    //         $sortByVotesData[$wallet['address']] = random_int(10, 100);

    //         $validatorData[] = [
    //             'balance'           => faker()->numberBetween(1, 1000) * 1e18,
    //             'nonce'             => faker()->numberBetween(1, 1000),
    //             'updated_at'        => faker()->numberBetween(1, 1000),
    //             'address'           => $wallet['address'],
    //             'public_key'        => $wallet['publicKey'],
    //             'attributes'        => json_encode([
    //                 'validatorRank'           => $rank,
    //                 'validatorVoteBalance'    => (string) BigNumber::new($sortByVotesData[$wallet['address']]),
    //                 'validatorProducedBlocks' => faker()->numberBetween(1, 1000),
    //                 'validatorMissedBlocks'   => faker()->numberBetween(1, 1000),
    //             ]),
    //         ];
    //     }

    //     Wallet::insert($validatorData);

    //     $missedBlocks       = [];
    //     $missedBlockCounter = 0;

    //     $missedBlocksData = [];

    //     $validators = Wallet::all();

    //     foreach ($validators as $validator) {
    //         $missedBlockCount = random_int(2, 4);
    //         foreach (range(1, $missedBlockCount) as $_) {
    //             $missedBlocksData[] = [
    //                 'timestamp'     => Timestamp::fromUnix(Carbon::now()->subHours($missedBlockCounter)->unix())->unix(),
    //                 'address'       => $validator->address,
    //                 'forged'        => faker()->boolean(),
    //                 'missed_height' => faker()->numberBetween(1, 10000),
    //             ];

    //             $missedBlockCounter++;
    //         }
    //     }

    //     ForgingStats::insert($missedBlocksData);

    //     $missedBlocks = ForgingStats::all();

    //     $missedBlocks = $missedBlocks->sort(function ($a, $b) use ($modelSortBy, $sortByVotesData) {
    //         if ($modelSortBy === 'validatorVoteBalance') {
    //             $aValue = $sortByVotesData[$a->address];
    //             $bValue = $sortByVotesData[$b->address];
    //         } else {
    //             $aValue = Arr::get($a, $modelSortBy);
    //             $bValue = Arr::get($b, $modelSortBy);
    //         }

    //         if (is_numeric($bValue) && is_numeric($aValue)) {
    //             if ((int) $aValue === (int) $bValue) {
    //                 return $b->timestamp - $a->timestamp;
    //             }

    //             return (int) $aValue - (int) $bValue;
    //         }

    //         $value = strcmp($aValue, $bValue);
    //         if ($value === 0) {
    //             return $b->timestamp - $a->timestamp;
    //         }

    //         return $value;
    //     });

    //     $component = Livewire::test(Tabs::class)
    //         ->set('view', 'missed-blocks')
    //         ->call('setMissedBlocksReady')
    //         ->call('sortBy', $columnSortBy)
    //         ->set('sortDirections.missed-blocks', SortDirection::ASC);

    //     foreach (range(1, 4) as $page) {
    //         $pageData = $missedBlocks->chunk(25)->get($page - 1)->pluck('address');

    //         $component->call('gotoPage', $page)
    //             ->assertSeeInOrder([
    //                 ...$pageData,
    //                 ...$pageData,
    //             ]);
    //     }
    // })->with([
    //     'height'           => ['height', 'missed_height'],
    //     'age'              => ['age', 'timestamp'],
    //     'no_of_voters'     => ['no_of_voters', 'timestamp'],
    //     'votes'            => ['votes', 'validatorVoteBalance'],
    //     'percentage_votes' => ['percentage_votes', 'validatorVoteBalance'],
    // ]);

    // it('should handle sorting several pages with cached data', function ($columnSortBy, $modelSortBy) {
    //     $this->freezeTime();
    //     $this->travelTo('2025-09-04 13:44:12');

    //     $validatorData = [];

    //     $sortByVotesData = [];

    //     foreach (range(1, 145) as $rank) {
    //         $wallet          = faker()->wallet;

    //         $sortByVotesData[$wallet['address']] = random_int(10, 100);

    //         $validatorData[] = [
    //             'balance'           => faker()->numberBetween(1, 1000) * 1e18,
    //             'nonce'             => faker()->numberBetween(1, 1000),
    //             'updated_at'        => faker()->numberBetween(1, 1000),
    //             'address'           => $wallet['address'],
    //             'public_key'        => $wallet['publicKey'],
    //             'attributes'        => json_encode([
    //                 'validatorRank'           => $rank,
    //                 'validatorVoteBalance'    => (string) BigNumber::new($sortByVotesData[$wallet['address']]),
    //                 'validatorProducedBlocks' => faker()->numberBetween(1, 1000),
    //                 'validatorMissedBlocks'   => faker()->numberBetween(1, 1000),
    //             ]),
    //         ];
    //     }

    //     Wallet::insert($validatorData);

    //     $voterCounts        = [];
    //     $missedBlocks       = [];
    //     $missedBlockCounter = 0;

    //     $missedBlocksData = [];

    //     $validators = Wallet::all();

    //     foreach ($validators as $validator) {
    //         $missedBlockCount = random_int(2, 4);
    //         foreach (range(1, $missedBlockCount) as $_) {
    //             $missedBlocksData[] = [
    //                 'timestamp'     => Timestamp::fromUnix(Carbon::now()->subHours($missedBlockCounter)->unix())->unix(),
    //                 'address'       => $validator->address,
    //                 'forged'        => faker()->boolean(),
    //                 'missed_height' => faker()->numberBetween(1, 10000),
    //             ];

    //             $sortByVotesData[$validator->address] = $validator->attributes['validatorVoteBalance'];

    //             $missedBlockCounter++;
    //         }

    //         $voterCounts[$validator->address] = random_int(10, 100);
    //     }

    //     ForgingStats::insert($missedBlocksData);

    //     $missedBlocks = ForgingStats::all();

    //     $validatorCache = new ValidatorCache();
    //     $validatorCache->setAllVoterCounts($voterCounts);

    //     $missedBlocks = $missedBlocks->sort(function ($a, $b) use ($modelSortBy, $voterCounts, $sortByVotesData) {
    //         if ($modelSortBy === 'no_of_voters') {
    //             $aValue = $voterCounts[$a->address];
    //             $bValue = $voterCounts[$b->address];
    //         } elseif ($modelSortBy === 'votes' || $modelSortBy === 'percentage_votes') {
    //             $aValue = $sortByVotesData[$a->address];
    //             $bValue = $sortByVotesData[$b->address];
    //         } else {
    //             $aValue = Arr::get($a, $modelSortBy);
    //             $bValue = Arr::get($b, $modelSortBy);
    //         }

    //         if (is_numeric($bValue) && is_numeric($aValue)) {
    //             if ((int) $aValue === (int) $bValue) {
    //                 return $b->timestamp - $a->timestamp;
    //             }

    //             return (int) $aValue - (int) $bValue;
    //         }

    //         $value = strcmp($aValue, $bValue);
    //         if ($value === 0) {
    //             return $b->timestamp - $a->timestamp;
    //         }

    //         return $value;
    //     });

    //     $component = Livewire::test(Tabs::class)
    //         ->set('view', 'missed-blocks')
    //         ->call('setMissedBlocksReady')
    //         ->call('sortBy', $columnSortBy)
    //         ->set('sortDirections.missed-blocks', SortDirection::ASC);

    //     foreach (range(1, 4) as $page) {
    //         $pageData = $missedBlocks->chunk(25)->get($page - 1)->pluck('address');

    //         $component->call('gotoPage', $page)
    //             ->assertSeeInOrder([
    //                 ...$pageData,
    //                 ...$pageData,
    //             ]);
    //     }
    // })->with([
    //     'height'           => ['height', 'missed_height'],
    //     'age'              => ['age', 'timestamp'],
    //     'no_of_voters'     => ['no_of_voters', 'no_of_voters'],
    //     'votes'            => ['votes', 'votes'],
    //     'percentage_votes' => ['percentage_votes', 'percentage_votes'],
    // ]);
});

dataset('resolutions', [
    'desktop' => [['width' => 1280, 'height' => 1024]],
    'lg'      => [['width' => 1024, 'height' => 768]],
    'md-lg'   => [['width' => 960, 'height' => 667]],
    'md'      => [['width' => 768, 'height' => 1024]],
    'sm'      => [['width' => 640, 'height' => 960]],
    'xs'      => [['width' => 370, 'height' => 844]],
]);
