<?php

declare(strict_types=1);

use App\Models\ForgingStats;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\Cache\ValidatorCache;
use Illuminate\Support\Facades\Cache;
use Laravel\Dusk\Browser;

beforeEach(function () {
    $this->withoutExceptionHandling();
});

afterEach(function () {
    Cache::tags(['dusk'])->flush();
});

function browserNumberFormat(Browser $browser, int $value): string
{
    return $browser->script('return Intl.NumberFormat().format('.$value.');')[0];
}

function seedMissedBlocksSortingData(int $count, bool $withCachedVoters): array
{
    $timestamps  = range(100_000, 100_000 + $count - 1);
    $heights     = range(1, $count);
    $voteWeights = range(1, $count);
    $voterCounts = range(1, $count);

    shuffle($heights);
    shuffle($voteWeights);
    shuffle($voterCounts);

    $records     = [];
    $cacheCounts = [];

    for ($i = 0; $i < $count; $i++) {
        $wallet = Wallet::factory()->activeValidator()->create();

        $attributes                         = $wallet->attributes;
        $attributes['validatorVoteBalance'] = (string) BigNumber::new($voteWeights[$i])->multipliedBy('1000000000000000000');
        $wallet->attributes                 = $attributes;
        $wallet->save();

        ForgingStats::factory()->create([
            'address'       => $wallet->address,
            'timestamp'     => $timestamps[$i],
            'missed_height' => $heights[$i],
            'forged'        => false,
        ]);

        $records[] = [
            'address'       => $wallet->address,
            'timestamp'     => $timestamps[$i],
            'missed_height' => $heights[$i],
            'votes'         => $voteWeights[$i],
            'no_of_voters'  => $voterCounts[$i],
        ];

        $cacheCounts[$wallet->address] = $voterCounts[$i];
    }

    if ($withCachedVoters) {
        (new ValidatorCache())->setAllVoterCounts($cacheCounts);
    }

    return $records;
}

describe('Validators Tab', function () {
    it('should handle no data', function ($resolution) {
        $this->browse(function (Browser $browser) use ($resolution) {
            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('validators')
                ->waitForText(trans('tables.validators.no_results'));
        });
    })->with('resolutions');

    it('should display data', function ($resolution) {
        $wallets = Wallet::factory(4)->activeValidator()->create();

        $this->browse(function (Browser $browser) use ($wallets, $resolution) {
            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('validators')
                ->waitForText('4 results', ignoreCase: true);

            foreach ($wallets as $wallet) {
                $browser->assertSee(substr($wallet->address, 0, 5).'…'.substr($wallet->address, -5));
            }
        });
    })->with('resolutions');

    it('should go to page 2', function ($resolution) {
        Wallet::factory(53)->activeValidator()->create();
        $standbyWallets = Wallet::factory(10)->standbyValidator()->create();

        $this->browse(function (Browser $browser) use ($standbyWallets, $resolution) {
            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('validators')
                ->waitForText('63 results', ignoreCase: true)
                ->click('[data-testid="pagination:next-page"] button')
                ->waitForText('Page 2 of 2')
                ->assertQueryStringHas('page', '2');

            foreach ($standbyWallets as $wallet) {
                $browser->assertSee(substr($wallet->address, 0, 5).'…'.substr($wallet->address, -5));
            }
        });
    })->with('resolutions');

    it('should reset to page 1 on per-page change', function ($resolution) {
        $activeWallets = Wallet::factory(10)->activeValidator()->create();
        Wallet::factory(53)->standbyValidator()->create();

        $this->browse(function (Browser $browser) use ($activeWallets, $resolution) {
            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('validators', ['page' => 2])
                ->waitForText('63 results', ignoreCase: true)
                ->assertSee('Page 2 of 2')
                ->click('[data-testid="pagination:per-page-dropdown:button"]')
                ->waitForTextIn('[data-testid="pagination:per-page-dropdown:dropdown"]', '10')
                ->clickAtXPath('//div[@data-testid="pagination:per-page-dropdown:dropdown"]//span[.//text()="10"]')
                ->waitForText('Page 1 of 7');

            foreach ($activeWallets as $wallet) {
                $browser->assertSee(substr($wallet->address, 0, 5).'…'.substr($wallet->address, -5));
            }
        });
    })->with('resolutions');
});

describe('Missed Blocks Tab', function () {
    it('should handle no missed blocks', function ($resolution) {
        $this->browse(function (Browser $browser) use ($resolution) {
            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('validators')
                ->waitFor('button#tab-missed-blocks')
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
                ->waitFor('button#tab-missed-blocks')
                ->click('button#tab-missed-blocks')
                ->waitForText('2 results', ignoreCase: true);
        });
    })->with('resolutions');

    it('should go to page 2', function ($resolution) {
        for ($i = 14234; $i < 14234 + 30; $i++) {
            ForgingStats::factory()
                ->create([
                    'missed_height' => $i,
                    'timestamp'     => $i,
                ]);
        }

        $this->browse(function (Browser $browser) use ($resolution) {
            $sortedBlocks = ForgingStats::orderBy('timestamp', 'desc')
                ->whereNotNull('missed_height');

            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('validators')
                ->waitFor('button#tab-missed-blocks')
                ->click('button#tab-missed-blocks')
                ->waitForText('30 results', ignoreCase: true)
                ->click('[data-testid="pagination:next-page"] button')
                ->waitForText('Page 2 of 2')
                ->assertQueryStringHas('page', '2');

            foreach ($sortedBlocks->skip(25)->take(5)->get() as $block) {
                $browser->assertSee(browserNumberFormat($browser, $block->missed_height));
            }
        });
    })->with('resolutions');

    it('should reset to page 1 on per-page change', function ($resolution) {
        for ($i = 14234; $i < 14234 + 30; $i++) {
            ForgingStats::factory()
                ->create([
                    'missed_height' => $i,
                    'timestamp'     => $i,
                ]);
        }

        $this->browse(function (Browser $browser) use ($resolution) {
            $sortedBlocks = ForgingStats::orderBy('timestamp', 'desc')
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
                $browser->assertSee(browserNumberFormat($browser, $block->missed_height));
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

    it('should fall back to default if invalid query string value', function () {
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
                    'validator-2',
                    'validator-1',
                ], ignoreCase: true);
        });
    });

    it('should handle sorting several pages without cached data', function (string $sortKey) {
        $records = collect(seedMissedBlocksSortingData(100, false));

        $sorted = $records->sort(function (array $a, array $b) use ($sortKey): int {
            if ($sortKey === 'height') {
                $cmp = $a['missed_height'] <=> $b['missed_height'];

                return $cmp !== 0 ? $cmp : ($b['timestamp'] <=> $a['timestamp']);
            }

            if ($sortKey === 'age') {
                return $a['timestamp'] <=> $b['timestamp'];
            }

            if ($sortKey === 'no_of_voters') {
                return $a['timestamp'] <=> $b['timestamp'];
            }

            if ($sortKey === 'votes' || $sortKey === 'percentage_votes') {
                $cmp = $a['votes'] <=> $b['votes'];

                return $cmp !== 0 ? $cmp : ($b['timestamp'] <=> $a['timestamp']);
            }

            return 0;
        })->values();

        $this->browse(function (Browser $browser) use ($sorted, $sortKey) {
            $browser->resize(1280, 1024);

            foreach (range(1, 4) as $page) {
                $addresses = $sorted->slice(($page - 1) * 25, 25)->pluck('address')->values()->all();

                $browser->visitRoute('validators', [
                    'tab'            => 'missed-blocks',
                    'sort'           => $sortKey,
                    'sort-direction' => 'asc',
                    'page'           => $page,
                ])
                    ->waitForText('100 results', 20, ignoreCase: true)
                    ->waitForText('Page '.$page.' of 4', 20);

                $browser->waitForSeeInOrder($addresses, seconds: 20, ignoreCase: true);
            }
        });
    })->with([
        'height',
        'age',
        'no_of_voters',
        'votes',
        'percentage_votes',
    ]);

    it('should handle sorting several pages with cached data', function (string $sortKey) {
        $records = collect(seedMissedBlocksSortingData(100, true));

        $sorted = $records->sort(function (array $a, array $b) use ($sortKey): int {
            if ($sortKey === 'height') {
                $cmp = $a['missed_height'] <=> $b['missed_height'];

                return $cmp !== 0 ? $cmp : ($b['timestamp'] <=> $a['timestamp']);
            }

            if ($sortKey === 'age') {
                return $a['timestamp'] <=> $b['timestamp'];
            }

            if ($sortKey === 'no_of_voters') {
                $cmp = $a['no_of_voters'] <=> $b['no_of_voters'];

                return $cmp !== 0 ? $cmp : ($b['timestamp'] <=> $a['timestamp']);
            }

            if ($sortKey === 'votes' || $sortKey === 'percentage_votes') {
                $cmp = $a['votes'] <=> $b['votes'];

                return $cmp !== 0 ? $cmp : ($b['timestamp'] <=> $a['timestamp']);
            }

            return 0;
        })->values();

        $this->browse(function (Browser $browser) use ($sorted, $sortKey) {
            $browser->resize(1280, 1024);

            foreach (range(1, 4) as $page) {
                $addresses = $sorted->slice(($page - 1) * 25, 25)->pluck('address')->values()->all();

                $browser->visitRoute('validators', [
                    'tab'            => 'missed-blocks',
                    'sort'           => $sortKey,
                    'sort-direction' => 'asc',
                    'page'           => $page,
                ])
                    ->waitForText('100 results', 20, ignoreCase: true)
                    ->waitForText('Page '.$page.' of 4', 20);

                $browser->waitForSeeInOrder($addresses, seconds: 20, ignoreCase: true);
            }
        });
    })->with([
        'height',
        'age',
        'no_of_voters',
        'votes',
        'percentage_votes',
    ]);
});

dataset('resolutions', [
    'desktop' => [['width' => 1280, 'height' => 1024]],
    'lg'      => [['width' => 1024, 'height' => 768]],
    'md-lg'   => [['width' => 960, 'height' => 667]],
    'md'      => [['width' => 768, 'height' => 1024]],
    'sm'      => [['width' => 640, 'height' => 960]],
    'xs'      => [['width' => 370, 'height' => 844]],
]);
