<?php

declare(strict_types=1);

use App\Contracts\RoundRepository as ContractsRoundRepository;
use App\DTO\Slot;
use App\Enums\ValidatorForgingStatus;
use App\Facades\Network;
use App\Models\Block;
// use App\Http\Livewire\Validators\Monitor;
use App\Models\Wallet;
use App\Repositories\RoundRepository;
use App\Services\Cache\WalletCache;
use App\ViewModels\WalletViewModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Inertia\Testing\AssertableInertia as Assert;
use Livewire\Livewire;
use function Tests\createBlock;
use function Tests\createPartialRound;
use function Tests\createRealisticRound;
use function Tests\createRoundEntry;
use function Tests\getRoundValidators;
use function Tests\mockTaggedCache;

beforeEach(function () {
    $this->withoutExceptionHandling();
});

function performRequest($context, $withReload = true, $pageCallback = null, $reloadCallback = null): mixed
{
    return $context->get(route('validator-monitor'))
        ->assertOk()
        ->assertInertia(function (Assert $page) use ($withReload, $pageCallback, $reloadCallback) {
            $page->where('rowCount', Network::validatorCount())
                ->missing('height')
                ->missing('validatorData')
                ->component('Validators/Monitor');

            if (is_callable($pageCallback)) {
                $pageCallback($page);
            }

            if (! $withReload) {
                return;
            }

            $page->reloadOnly('height,validatorData', function (Assert $reload) use ($reloadCallback) {
                $reload->has('validatorData', 3)
                    ->has('validatorData.validators')
                    ->has('validatorData.overflowValidators')
                    ->has('validatorData.statistics');

                if (is_callable($reloadCallback)) {
                    $reloadCallback($reload);
                }
            });
        });
}

describe('Monitor', function () {
    beforeEach(function () {
        $this->activeValidators = require dirname(dirname(dirname(__DIR__))).'/fixtures/forgers.php';
    });

    function createRoundWithValidators(): void
    {
        $wallets = Wallet::factory(Network::validatorCount())->create();

        createRoundEntry(112168, 5944904, $wallets);

        $wallets->each(function ($wallet) {
            $block = Block::factory()->create([
                'number'            => 5944900,
                'timestamp'         => 113620904,
                'proposer'          => $wallet->address,
            ]);

            // Start height for round 112168
            Block::factory()->create([
                'number'            => 5944904,
                'timestamp'         => 113620904,
                'proposer'          => $wallet->address,
            ]);

            (new WalletCache())->setValidator($wallet->address, $wallet);

            (new WalletCache())->setLastBlock($wallet->address, [
                'id'     => $block->hash,
                'number' => $block->number->toNumber(),
            ]);
        });
    }

    function forgeBlock(string $address, int $height): void
    {
        $block = createBlock($height, $address);

        (new WalletCache())->setLastBlock($address, [
            'id'     => $block->hash,
            'number' => $block->number->toNumber(),
        ]);
    }

    it('should render without errors', function () {
        createRoundWithValidators();

        performRequest($this);
    });

    it('should throw an exception after 3 tries', function () {
        createRoundWithValidators();

        $this->expectExceptionMessage('Something went wrong!');

        mockTaggedCache(withTags:true)->shouldReceive('remember')
            ->andThrow(new Exception('Something went wrong!'))
            ->shouldReceive('increment')
            ->andReturn(1, 2, 3);

        performRequest($this);
    });

    it('should not throw an exception if fails to poll 2 times', function () {
        createRoundWithValidators();

        mockTaggedCache(withTags:true)->shouldReceive('remember')
            ->once()
            ->andThrow(new Exception('Something went wrong!'))
            ->shouldReceive('increment')
            ->andReturn(1, 2, 3)
            ->shouldReceive('remember')
            ->andReturnUsing(fn ($tag, $time, $closure) => $closure())
            ->shouldReceive('forget')
            ->andReturn(null);

        performRequest($this);
    });

    it('should get the last blocks from the last 2 rounds and beyond', function () {
        $wallets = Wallet::factory(Network::validatorCount())->create();

        createRoundEntry(1, 1, $wallets);

        $wallets->each(function ($wallet) {
            for ($i = 0; $i < 3; $i++) {
                Block::factory()->create([
                    'number'            => $i,
                    'proposer'          => $wallet->address,
                ]);
            }

            (new WalletCache())->setValidator($wallet->address, $wallet);
        });

        $wallets->first()->blocks()->delete();

        performRequest($this);

        expect((new WalletCache())->getLastBlock($wallets->first()->address))->toBe([]);

        foreach ($wallets->skip(1) as $wallet) {
            expect((new WalletCache())->getLastBlock($wallet->address))->not()->toBe([]);
        }
    });

    it('should do nothing if no rounds', function () {
        $wallets = Wallet::factory(Network::validatorCount())->create();

        createRoundEntry(1, 1, $wallets);

        performRequest($this, reloadCallback: fn (Assert $reload) => $reload->where('validatorData.validators', []));
    });

    // it('should show warning icon for validators missing blocks - minutes', function () {
    //     $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

    //     $this->freezeTime();

    //     [0 => $validators] = createRealisticRound([
    //         [
    //             ...array_fill(0, 4, true),
    //             false,
    //             ...array_fill(0, 48, true),
    //         ],
    //         [
    //             ...array_fill(0, 4, true),
    //             false,
    //             ...array_fill(0, 48, true),
    //         ],
    //         [
    //             ...array_fill(0, 4, true),
    //             false,
    //             ...array_fill(0, 48, true),
    //         ],
    //     ], $this);

    //     $validator = (new WalletViewModel($validators->get(4)));

    //     expect($validator->performance())->toBe([false, false]);

    //     performRequest($this)->assertSeeInOrder([
    //         $validator->username(),
    //         'Validator last forged 207 blocks ago (~ 28 min)',
    //     ]);
    // });

    // it('should show warning icon for validators missing blocks - hours', function () {
    //     $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

    //     $this->freezeTime();

    //     [0 => $validators] = createRealisticRound([
    //         [
    //             ...array_fill(0, 4, true),
    //             false,
    //             ...array_fill(0, 48, true),
    //         ],
    //         [
    //             ...array_fill(0, 4, true),
    //             false,
    //             ...array_fill(0, 48, true),
    //         ],
    //         [
    //             ...array_fill(0, 4, true),
    //             false,
    //             ...array_fill(0, 48, true),
    //         ],
    //     ], $this);

    //     $this->travelTo(Carbon::parse('2024-02-01 15:00:00Z'));

    //     $validator = (new WalletViewModel($validators->get(4)));

    //     expect($validator->performance())->toBe([false, false]);

    //     performRequest($this)->assertSeeInOrder([
    //         $validator->username(),
    //         'Validator last forged 207 blocks ago (~ 1h 28 min)',
    //     ]);
    // });

    // it('should show warning icon for validators missing blocks - days', function () {
    //     $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

    //     $this->freezeTime();

    //     [0 => $validators] = createRealisticRound([
    //         [
    //             ...array_fill(0, 4, true),
    //             false,
    //             ...array_fill(0, 48, true),
    //         ],
    //         [
    //             ...array_fill(0, 4, true),
    //             false,
    //             ...array_fill(0, 48, true),
    //         ],
    //         [
    //             ...array_fill(0, 4, true),
    //             false,
    //             ...array_fill(0, 48, true),
    //         ],
    //     ], $this);

    //     $this->travelTo(Carbon::parse('2024-02-03 15:00:00Z'));

    //     $validator = (new WalletViewModel($validators->get(4)));

    //     expect($validator->performance())->toBe([false, false]);

    //     performRequest($this)->assertSeeInOrder([
    //         $validator->username(),
    //         'Validator last forged 207 blocks ago (more than a day)',
    //     ]);
    // });

    // it('should reload on new block event', function () {
    //     $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

    //     $this->freezeTime();

    //     [0 => $validators] = createRealisticRound([
    //         [
    //             ...array_fill(0, 4, true),
    //             false,
    //             ...array_fill(0, 48, true),
    //         ],
    //         [
    //             ...array_fill(0, 4, true),
    //             false,
    //             ...array_fill(0, 48, true),
    //         ],
    //         [
    //             ...array_fill(0, 4, true),
    //             false,
    //             ...array_fill(0, 48, true),
    //         ],
    //     ], $this);

    //     $this->travelTo(Carbon::parse('2024-02-03 15:00:00Z'));

    //     $validator = (new WalletViewModel($validators->get(4)));

    //     performRequest($this)->assertSeeInOrder([
    //         $validator->username(),
    //         'Validator last forged 207 blocks ago (more than a day)',
    //     ]);
    // });

    it('should cache last blocks', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        createRealisticRound([
            array_fill(0, 53, true),
        ], $this);

        expect(Cache::has('monitor:last-blocks'))->toBeFalse();

        performRequest($this);

        expect(Cache::has('monitor:last-blocks'))->toBeTrue();
    });

    it('should show no overflow validators if no missed blocks', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        createRealisticRound([
            array_fill(0, 53, true),
        ], $this);

        $this->travelTo(Carbon::parse('2024-02-03 15:00:00Z'));

        performRequest($this, reloadCallback: function (Assert $reload) {
            $reload->where('validatorData.overflowValidators', []);
        });
    });

    it('should show no overflow validators at the start of a round', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [$validators, $round, $height] = createRealisticRound([
            array_fill(0, 53, true),
        ], $this);

        createRoundEntry($round, $height, Wallet::all());

        $this->travelTo(Carbon::parse('2024-02-03 15:00:00Z'));

        performRequest($this, reloadCallback: function (Assert $reload) {
            $reload->where('validatorData.overflowValidators', []);
        });
    });

    it('should show overflow validators with a full round', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        createRealisticRound([
            [
                ...array_fill(0, 8, true),
                false,
                false,
                false,
                false,
                false,
                ...array_fill(0, 40, true),
            ],
        ], $this);

        expect(Carbon::now()->format('Y-m-d H:i:s'))->toBe('2024-02-01 14:01:00');

        performRequest($this, reloadCallback: function (Assert $reload) {
            $reload->has('validatorData.overflowValidators', 5)
                ->where('validatorData.overflowValidators', function ($overflowValidators) {
                    return collect($overflowValidators)->map(fn ($validator) => $validator['status'])->toArray() === [
                        'done',
                        'done',
                        'done',
                        'done',
                        'done',
                    ];
                });
        });
    });

    it('should show overflow validators at the end of all initial slots', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [$validators, $round, $height] = createRealisticRound([
            array_fill(0, 53, true),
        ], $this);

        createPartialRound($round, $height, null, $this, [
            $validators->get(4)->address,
            $validators->get(5)->address,
            $validators->get(6)->address,
            $validators->get(7)->address,
            $validators->get(8)->address,
        ], [
            $validators->get(4)->address,
            $validators->get(5)->address,
            $validators->get(6)->address,
            $validators->get(7)->address,
            $validators->get(8)->address,
        ], true, 53);

        expect($height)->toBe((3 * Network::validatorCount()) - 4);

        // $component = Livewire::test(Monitor::class)
        //     ->call('setIsReady')
        //     ->call('pollValidators');

        // $instance = $component->instance();

        // $overflowValidators = $instance->getOverflowValidatorsProperty();

        // expect($overflowValidators)->toHaveCount(5);
        // expect(collect($overflowValidators)->map(fn ($validator) => $validator->status())->toArray())->toBe([
        //     'next',
        //     'pending',
        //     'pending',
        //     'pending',
        //     'pending',
        // ]);

        performRequest($this, reloadCallback: function (Assert $reload) {
            $reload->has('validatorData.overflowValidators', 5)
                ->where('validatorData.overflowValidators', function ($overflowValidators) {
                    return collect($overflowValidators)->map(fn ($validator) => $validator['status'])->toArray() === [
                        'next',
                        'pending',
                        'pending',
                        'pending',
                        'pending',
                    ];
                });
        });
    });

    it('should show overflow validators for partial round', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [$validators, $round, $height] = createRealisticRound([
            array_fill(0, 53, true),
        ], $this);

        createPartialRound($round, $height, 52, $this, [
            $validators->get(4)->address,
            $validators->get(5)->address,
            $validators->get(6)->address,
            $validators->get(7)->address,
            $validators->get(8)->address,
        ], [
            $validators->get(4)->address,
            $validators->get(5)->address,
            $validators->get(6)->address,
            $validators->get(7)->address,
            $validators->get(8)->address,
        ]);

        performRequest($this, reloadCallback: function (Assert $reload) {
            $reload->has('validatorData.overflowValidators', 5)
                ->where('validatorData.overflowValidators', function ($overflowValidators) {
                    return collect($overflowValidators)->map(fn ($validator) => $validator['status'])->toArray() === [
                        'done',
                        'done',
                        'done',
                        'done',
                        'next',
                    ];
                });
        });
    });

    it('should track overflow slots correctly', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [$validators, $round, $height] = createRealisticRound([
            array_fill(0, 53, true),
        ], $this);

        [$validators, $round, $height] = createPartialRound($round, $height, null, $this, [
            $validators->get(4)->address,
            $validators->get(5)->address,
            $validators->get(6)->address,
            $validators->get(7)->address,
            $validators->get(8)->address,
        ], [
            $validators->get(4)->address,
            $validators->get(5)->address,
            $validators->get(6)->address,
            $validators->get(7)->address,
            $validators->get(8)->address,
        ], true, 53);

        $validators = getRoundValidators(false, $round - 1);

        createBlock($height, $validators->get(0)['address'], $this);
        createBlock($height + 1, $validators->get(1)['address'], $this);

        performRequest($this, reloadCallback: function (Assert $reload) {
            $reload->has('validatorData.overflowValidators', 5)
                ->where('validatorData.overflowValidators', function ($overflowValidators) {
                    return collect($overflowValidators)->map(fn ($validator) => $validator['status'])->toArray() === [
                        'done',
                        'done',
                        'next',
                        'pending',
                        'pending',
                    ];
                });
        });
    });

    it('should handle when an overflow validator misses a block', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [$validators, $round, $height] = createRealisticRound([
            array_fill(0, 53, true),
        ], $this);

        [$validators, $round, $height, $totalMissedSeconds] = createPartialRound($round, $height, null, $this, [
            $validators->get(4)->address,
            $validators->get(5)->address,
            $validators->get(6)->address,
            $validators->get(7)->address,
            $validators->get(8)->address,
        ], [
            $validators->get(4)->address,
            $validators->get(5)->address,
            $validators->get(6)->address,
            $validators->get(7)->address,
            $validators->get(8)->address,
        ], true, 53);

        $validators = getRoundValidators(false, $round - 1);

        // Overflow slot 1
        createBlock($height, $validators->get(0)['address'], $this);

        // Overflow slot 2
        $this->travel(Network::blockTime() + 2)->seconds();

        // Overflow slot 3
        createBlock($height + 1, $validators->get(2)['address'], $this);

        $overflowForgeTime = Carbon::parse('2024-02-01 14:00:00Z')->addSeconds((Network::blockTime() * (Network::validatorCount() + 4)) + $totalMissedSeconds + 2);

        performRequest($this, reloadCallback: function (Assert $reload) use ($overflowForgeTime) {
            $reload->has('validatorData.overflowValidators', 6)
                ->where('validatorData.overflowValidators', function ($overflowValidators) {
                    return collect($overflowValidators)->map(fn ($validator) => $validator['status'])->toArray() === [
                        'done',
                        'done',
                        'done',
                        'next',
                        'pending',
                        'pending',
                    ];
                })
                ->where('validatorData.overflowValidators', function ($overflowValidators) use ($overflowForgeTime) {
                    return collect($overflowValidators)->map(fn ($validator) => $validator['forgingAt'])->toArray() === [
                        $overflowForgeTime->toIso8601String(),
                        $overflowForgeTime->addSeconds(Network::blockTime())->toIso8601String(),
                        $overflowForgeTime->addSeconds(Network::blockTime() + 2)->toIso8601String(), // Missed overflow block
                        $overflowForgeTime->addSeconds(Network::blockTime())->toIso8601String(),
                        $overflowForgeTime->addSeconds(Network::blockTime())->toIso8601String(),
                        $overflowForgeTime->addSeconds(Network::blockTime())->toIso8601String(),
                    ];
                });
        });
    });

    it('should correctly show overflow if only a single block was missed', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [$validators, $round, $height] = createRealisticRound([
            array_fill(0, 53, true),
        ], $this);

        [$validators, $round, $height] = createPartialRound($round, $height, null, $this, [
            $validators->get(44)->address,
        ], [
            $validators->get(44)->address,
        ], true, 49);

        expect($height)->toBe((3 * Network::validatorCount()) - 4);

        $overflowForgeTime = Carbon::parse('2024-02-01 14:00:00Z')->addSeconds(Network::blockTime() * Network::validatorCount());

        performRequest($this, reloadCallback: function (Assert $reload) use ($overflowForgeTime) {
            $reload->where('validatorData.validators', function ($validators) {
                return collect($validators)->groupBy(fn ($validator) => $validator['status'])->map(fn ($group) => $group->count())->toArray() === [
                    'done'    => 49,
                    'next'    => 1,
                    'pending' => 3,
                ];
            })
            ->has('validatorData.overflowValidators', 1)
            ->where('validatorData.overflowValidators.0.forgingAt', function ($forgingAt) use ($overflowForgeTime) {
                return $forgingAt === $overflowForgeTime->toIso8601String();
            })
            ->where('validatorData.overflowValidators', function ($overflowValidators) {
                return collect($overflowValidators)->map(fn ($validator) => $validator['status'])->toArray() === [
                    'pending',
                ];
            });
        });
    });

    it('should skip if no last successful forger', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [$_, $round, $height] = createRealisticRound([
            array_fill(0, 53, true),
        ], $this);

        performRequest($this, reloadCallback: function (Assert $reload) use ($round, $height) {
            $this->travel(53)->seconds();

            createRoundEntry($round, $height, Wallet::all());

            $validators = getRoundValidators(false, $round);

            [$_, $round, $height] = createPartialRound(
                $round,
                $height,
                53,
                $this,
                [$validators->last()['address']],
                [],
                true
            );

            $reload->has('validatorData.overflowValidators', 0)
                ->where('validatorData.overflowValidators', []);
        });
    });

    it('should extend forge time when missed before overflow (testing Helper)', function (int $count, string $expected) {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        createRealisticRound([
            [
                ...array_fill(0, 4, true),
                ...array_fill(0, $count, false),
                ...array_fill(0, 49 - $count, true),
            ],
        ], $this);

        expect(Carbon::now()->format('Y-m-d H:i:s'))->toBe($expected);
    })->with([
        1 => [1, '2024-02-01 14:00:08'],
        2 => [2, '2024-02-01 14:00:18'],
        3 => [3, '2024-02-01 14:00:30'],
        4 => [4, '2024-02-01 14:00:44'],
        5 => [5, '2024-02-01 14:02:00'], // doubles up because we hit the batch of missing validators on the second passthrough
        6 => [6, '2024-02-01 14:02:36'],
    ]);
});

describe('Data Boxes', function () {
    beforeEach(function () {
        $this->app->bind(ContractsRoundRepository::class, function (): RoundRepository {
            return new RoundRepository();
        });

        $this->travelTo(Carbon::parse('2022-08-22 00:00'));
        $this->freezeTime();
    });

    function createRoundWithValidatorsAndPerformances(?array $performances = null, bool $addBlockForNextRound = true, int $walletCount = 53, int $baseIndex = 0): void
    {
        $wallets = Wallet::factory($walletCount)
            ->activeValidator()
            ->create();

        createRoundEntry(112168, 5944904, $wallets);

        $wallets->each(function ($wallet, $index) use ($performances, $addBlockForNextRound, $baseIndex) {
            $timestamp = Carbon::now()->add(($baseIndex + $index) * 8, 'seconds')->timestamp;

            $block = Block::factory()->create([
                'number'            => 5944900,
                'timestamp'         => $timestamp,
                'proposer'          => $wallet->address,
            ]);

            // Start height for round 112168
            if ($addBlockForNextRound) {
                Block::factory()->create([
                    'number'            => 5944904,
                    'timestamp'         => $timestamp,
                    'proposer'          => $wallet->address,
                ]);
            }

            (new WalletCache())->setValidator($wallet->address, $wallet);

            if (is_null($performances)) {
                for ($i = 0; $i < 2; $i++) {
                    $performances[] = (bool) mt_rand(0, 1);
                }
            }

            (new WalletCache())->setPerformance($wallet->address, $performances);

            (new WalletCache())->setLastBlock($wallet->address, [
                'id'     => $block->hash,
                'number' => $block->number->toNumber(),
            ]);
        });
    }

    it('should get the performances of active validators and parse it into a readable array', function () {
        createRoundWithValidatorsAndPerformances();

        performRequest($this, reloadCallback: function (Assert $reload) {
            $reload->where('validatorData.statistics.performances', function ($performances) {
                return collect($performances)->keys()->toArray() === [
                    'forging',
                    'missed',
                    'missing',
                ];
            });
        });
    });

    // it('should determine if validators are forging based on their round history', function () {
    //     createRoundWithValidatorsAndPerformances([true, true]);

    //     performRequest($this, reloadCallback: function (Assert $reload) {
    //         $validatorWallet = Wallet::first();
    //         $validator       = new WalletViewModel($validatorWallet);

    //         dd($reload);

    //         // $reload->where('validatorData.statistics.performances', function ($performances) {
    //         //     return collect($performances)->keys()->toArray() === [
    //         //         'forging',
    //         //         'missed',
    //         //         'missing',
    //         //     ];
    //         // });
    //     });

    //     // expect($component->instance()->getValidatorPerformance($validator->address()))->toBe(ValidatorForgingStatus::forging);
    // });

    // it('should determine if validators are not forging based on their round history', function () {
    //     [$validators] = createRealisticRound([
    //         [
    //             ...array_fill(0, 4, true),
    //             false,
    //             ...array_fill(0, 48, true),
    //         ],
    //         [
    //             ...array_fill(0, 4, true),
    //             false,
    //             ...array_fill(0, 48, true),
    //         ],
    //     ], $this);

    //     performRequest($this, reloadCallback: function (Assert $reload) {
    //         $reload->where('validatorData.statistics.performances', function ($performances) {
    //             return collect($performances)->keys()->toArray() === [
    //                 'forging',
    //                 'missed',
    //                 'missing',
    //             ];
    //         });
    //     });

    //     $component = Livewire::test(Monitor::class)
    //         ->call('setIsReady')
    //         ->call('pollData');

    //     $validatorWallet = $validators->get(4);
    //     $validator       = new WalletViewModel($validatorWallet);

    //     expect($validator->performance())->toBe([false, false]);

    //     expect($component->instance()->getValidatorPerformance($validator->address()))->toBe(ValidatorForgingStatus::missing);
    // });

    // it('should determine if validators are forging after missing 4 slots based on their round history', function () {
    //     createRoundWithValidatorsAndPerformances([false, true]);

    //     $component = Livewire::test(Monitor::class)
    //         ->call('setIsReady')
    //         ->call('pollData');

    //     $validatorWallet = Wallet::first();
    //     $validator       = new WalletViewModel($validatorWallet);

    //     expect($component->instance()->getValidatorPerformance($validator->address()))->toBe(ValidatorForgingStatus::forging);
    // });

    it('should return the block count', function () {
        createRoundWithValidatorsAndPerformances();

        performRequest($this, reloadCallback: function (Assert $reload) {
            $reload->where('validatorData.statistics.blockCount', function ($blockCount) {
                return $blockCount === trans('pages.validators.statistics.blocks_generated', [
                    'forged' => 1,
                    'total'  => Network::validatorCount(),
                ]);
            });
        });
    });

    it('should return the next validator', function () {
        [$validators, $round, $height] = createRealisticRound([array_fill(0, 53, true)], $this);

        createPartialRound($round, $height, 12, $this);

        performRequest($this, reloadCallback: function (Assert $reload) {
            $reload->has('validatorData.statistics.nextValidator.attributes')
                ->has('validatorData.statistics.nextValidator.address')
                ->has('validatorData.statistics.nextValidator.public_key')
                ->has('validatorData.statistics.nextValidator.balance')
                ->has('validatorData.statistics.nextValidator.nonce');
        });
    });

    it('should return the next validator when in overflow', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [$validators, $round, $height] = createRealisticRound([
            array_fill(0, 53, true),
        ], $this);

        [$validators, $round, $height] = createPartialRound($round, $height, null, $this, [
            $validators->get(4)->address,
            $validators->get(5)->address,
            $validators->get(6)->address,
            $validators->get(7)->address,
            $validators->get(8)->address,
        ], [
            $validators->get(4)->address,
            $validators->get(5)->address,
            $validators->get(6)->address,
            $validators->get(7)->address,
            $validators->get(8)->address,
        ], true, 53);

        $validators = getRoundValidators(false, $round - 1);

        // Overflow slot 1
        createBlock($height, $validators->get(0)['address'], $this);

        // Overflow slot 2
        $this->travel(Network::blockTime() + 2)->seconds();

        // Overflow slot 3
        createBlock($height + 1, $validators->get(2)['address'], $this);

        performRequest($this, reloadCallback: function (Assert $reload) use ($validators) {
            $reload->where('validatorData.statistics.nextValidator.address', $validators->get(4)['address']);
        });
    });

    it('should not error if no cached validator data', function () {
        $wallets = Wallet::factory(Network::validatorCount())
            ->activeValidator()
            ->create();

        createRoundEntry(112168, 5944904, $wallets);

        $wallets->each(function ($wallet) {
            Block::factory()->create([
                'number'            => 5944900,
                'timestamp'         => 113620904,
                'proposer'          => $wallet->address,
            ]);

            Block::factory()->create([
                'number'            => 5944904,
                'timestamp'         => 113620904,
                'proposer'          => $wallet->address,
            ]);
        });

        foreach ($wallets as $wallet) {
            expect((new WalletCache())->getValidator($wallet->address))->toBeNull();
        }

        performRequest($this, reloadCallback: function (Assert $reload) {
            $reload->where('validatorData.statistics.nextValidator', null);
        });
    });

    // it('should calculate forged correctly with current round', function () {
    //     $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

    //     $this->freezeTime();

    //     [$validators, $round, $height] = createRealisticRound([
    //         array_fill(0, 53, true),
    //         [
    //             ...array_fill(0, 4, true),
    //             false,
    //             ...array_fill(0, 48, true),
    //         ],
    //     ], $this);

    //     for ($i = 0; $i < 3; $i++) {
    //         createRoundEntry($round, $height, $validators);
    //         $validatorsOrder = getRoundValidators(false, $round);
    //         $validatorIndex  = $validatorsOrder->search(fn ($validator) => $validator['address'] === $validators->get(4)->address);
    //         if ($validatorIndex < 51) {
    //             break;
    //         }

    //         [$validators, $round, $height] = createRealisticRound([
    //             array_fill(0, 53, true),
    //             [
    //                 ...array_fill(0, 4, true),
    //                 false,
    //                 ...array_fill(0, 48, true),
    //             ],
    //         ], $this);
    //     }

    //     createPartialRound($round, $height, 51, $this, [], [$validators->get(4)->address]);

    //     expect((new WalletViewModel($validators->get(4)))->performance())->toBe([false, true]);

    //     // Livewire::test(Monitor::class)
    //     //     ->call('setIsReady')
    //     //     ->call('pollData')
    //     //     ->assertSeeHtmlInOrder([
    //     //         'Forging',
    //     //         '<span>53</span>',
    //     //         'Missed',
    //     //         '<span>0</span>',
    //     //         'Not Forging',
    //     //         '<span>0</span>',
    //     //         'Current Height',
    //     //     ]);

    //     performRequest($this)
    //         ->assertSeeHtmlInOrder([
    //             'Forging',
    //             '<span>53</span>',
    //             'Missed',
    //             '<span>0</span>',
    //             'Not Forging',
    //             '<span>1</span>',
    //             'Current Height',
    //         ]);
    // });

    // it('should calculate forged correctly for previous rounds', function () {
    //     $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

    //     $this->freezeTime();

    //     [0 => $validators] = createRealisticRound([
    //         [
    //             ...array_fill(0, 4, true),
    //             false,
    //             ...array_fill(0, 48, true),
    //         ],
    //         array_fill(0, 53, true),
    //         array_fill(0, 53, true),
    //     ], $this);

    //     expect((new WalletViewModel($validators->get(4)))->performance())->toBe([true, true]);

    //     // Livewire::test(Monitor::class)
    //     //     ->call('setIsReady')
    //     //     ->call('pollData')
    //     //     ->assertSeeHtmlInOrder([
    //     //         'Forging',
    //     //         '<span>53</span>',
    //     //         'Missed',
    //     //         '<span>0</span>',
    //     //         'Not Forging',
    //     //         '<span>0</span>',
    //     //         'Current Height',
    //     //     ]);

    //     performRequest($this)
    //         ->assertSeeHtmlInOrder([
    //             'Forging',
    //             '<span>53</span>',
    //             'Missed',
    //             '<span>0</span>',
    //             'Not Forging',
    //             '<span>0</span>',
    //             'Current Height',
    //         ]);
    // });

    // it('should calculate missed correctly with current round', function () {
    //     $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

    //     $this->freezeTime();

    //     [$validators, $round, $height] = createRealisticRound([
    //         array_fill(0, 53, true),
    //         array_fill(0, 53, true),
    //         array_fill(0, 53, true),
    //     ], $this);

    //     for ($i = 0; $i < 3; $i++) {
    //         createRoundEntry($round, $height, $validators);
    //         $validatorsOrder = getRoundValidators(false, $round);
    //         $validatorIndex  = $validatorsOrder->search(fn ($validator) => $validator['address'] === $validators->get(4)->address);
    //         if ($validatorIndex < 51) {
    //             break;
    //         }

    //         [$validators, $round, $height] = createRealisticRound([
    //             array_fill(0, 53, true),
    //         ], $this);
    //     }

    //     createPartialRound($round, $height, 51, $this, [$validators->get(4)->address], [$validators->get(4)->address]);

    //     expect((new WalletViewModel($validators->get(4)))->performance())->toBe([true, false]);

    //     // Livewire::test(Monitor::class)
    //     //     ->call('setIsReady')
    //     //     ->call('pollData')
    //     //     ->assertSeeHtmlInOrder([
    //     //         'Forging',
    //     //         '<span>52</span>',
    //     //         'Missed',
    //     //         '<span>1</span>',
    //     //         'Not Forging',
    //     //         '<span>0</span>',
    //     //         'Current Height',
    //     //     ]);

    //     performRequest($this)
    //         ->assertSeeHtmlInOrder([
    //             'Forging',
    //             '<span>52</span>',
    //             'Missed',
    //             '<span>1</span>',
    //             'Not Forging',
    //             '<span>0</span>',
    //             'Current Height',
    //         ]);
    // });

    // it('should calculate missed correctly for previous rounds', function () {
    //     $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

    //     $this->freezeTime();

    //     [0 => $validators] = createRealisticRound([
    //         array_fill(0, 53, true),
    //         array_fill(0, 53, true),
    //         [
    //             ...array_fill(0, 4, true),
    //             false,
    //             ...array_fill(0, 48, true),
    //         ],
    //     ], $this);

    //     expect((new WalletViewModel($validators->get(4)))->performance())->toBe([true, false]);

    //     // Livewire::test(Monitor::class)
    //     //     ->call('setIsReady')
    //     //     ->call('pollData')
    //     //     ->assertSeeHtmlInOrder([
    //     //         'Forging',
    //     //         '<span>52</span>',
    //     //         'Missed',
    //     //         '<span>1</span>',
    //     //         'Not Forging',
    //     //         '<span>0</span>',
    //     //         'Current Height',
    //     //     ]);

    //     performRequest($this)
    //         ->assertSeeHtmlInOrder([
    //             'Forging',
    //             '<span>52</span>',
    //             'Missed',
    //             '<span>1</span>',
    //             'Not Forging',
    //             '<span>0</span>',
    //             'Current Height',
    //         ]);
    // });

    // it('should calculate not forging correctly with current round', function () {
    //     $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

    //     $this->freezeTime();

    //     [$validators, $round, $height] = createRealisticRound([
    //         [
    //             ...array_fill(0, 4, true),
    //             false,
    //             ...array_fill(0, 48, true),
    //         ],
    //         [
    //             ...array_fill(0, 4, true),
    //             false,
    //             ...array_fill(0, 48, true),
    //         ],
    //         [
    //             ...array_fill(0, 4, true),
    //             false,
    //             ...array_fill(0, 48, true),
    //         ],
    //     ], $this);

    //     for ($i = 0; $i < 3; $i++) {
    //         createRoundEntry($round, $height, $validators);
    //         $validatorsOrder = getRoundValidators(false, $round);
    //         $validatorIndex  = $validatorsOrder->search(fn ($validator) => $validator['address'] === $validators->get(4)->address);
    //         if ($validatorIndex < 51) {
    //             break;
    //         }

    //         [$validators, $round, $height] = createRealisticRound([
    //             [
    //                 ...array_fill(0, 4, true),
    //                 false,
    //                 ...array_fill(0, 48, true),
    //             ],
    //         ], $this);
    //     }

    //     createPartialRound($round, $height, 51, $this, [$validators->get(4)->address], [$validators->get(4)->address]);

    //     expect((new WalletViewModel($validators->get(4)))->performance())->toBe([false, false]);

    //     // Livewire::test(Monitor::class)
    //     //     ->call('setIsReady')
    //     //     ->call('pollData')
    //     //     ->assertSeeHtmlInOrder([
    //     //         'Forging',
    //     //         '<span>52</span>',
    //     //         'Missed',
    //     //         '<span>0</span>',
    //     //         'Not Forging',
    //     //         '<span>1</span>',
    //     //         'Current Height',
    //     //     ]);

    //     performRequest($this)
    //         ->assertSeeHtmlInOrder([
    //             'Forging',
    //             '<span>52</span>',
    //             'Missed',
    //             '<span>0</span>',
    //             'Not Forging',
    //             '<span>1</span>',
    //             'Current Height',
    //         ]);
    // });

    // it('should calculate not forging correctly for previous rounds', function () {
    //     $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

    //     $this->freezeTime();

    //     [0 => $validators] = createRealisticRound([
    //         [
    //             ...array_fill(0, 4, true),
    //             false,
    //             ...array_fill(0, 48, true),
    //         ],
    //         [
    //             ...array_fill(0, 4, true),
    //             false,
    //             ...array_fill(0, 48, true),
    //         ],
    //         [
    //             ...array_fill(0, 4, true),
    //             false,
    //             ...array_fill(0, 48, true),
    //         ],
    //     ], $this);

    //     expect((new WalletViewModel($validators->get(4)))->performance())->toBe([false, false]);

    //     // Livewire::test(Monitor::class)
    //     //     ->call('setIsReady')
    //     //     ->call('pollData')
    //     //     ->assertSeeHtmlInOrder([
    //     //         'Forging',
    //     //         '<span>52</span>',
    //     //         'Missed',
    //     //         '<span>0</span>',
    //     //         'Not Forging',
    //     //         '<span>1</span>',
    //     //         'Current Height',
    //     //     ]);

    //     performRequest($this)
    //         ->assertSeeHtmlInOrder([
    //             'Forging',
    //             '<span>52</span>',
    //             'Missed',
    //             '<span>0</span>',
    //             'Not Forging',
    //             '<span>1</span>',
    //             'Current Height',
    //         ]);
    // });

    // it('should reload on new block event', function () {
    //     $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

    //     $this->freezeTime();

    //     createRealisticRound([
    //         [
    //             ...array_fill(0, 4, true),
    //             false,
    //             ...array_fill(0, 48, true),
    //         ],
    //         [
    //             ...array_fill(0, 4, true),
    //             false,
    //             ...array_fill(0, 48, true),
    //         ],
    //         [
    //             ...array_fill(0, 4, true),
    //             false,
    //             ...array_fill(0, 48, true),
    //         ],
    //     ], $this);

    //     // Livewire::test(Monitor::class)
    //     //     ->call('setIsReady')
    //     //     ->assertDontSeeHtml('<span>52</span>')
    //     //     ->assertDontSeeHtml('<span>0</span>')
    //     //     ->assertDontSeeHtml('<span>1</span>')
    //     //     ->dispatch('echo:blocks,NewBlock')
    //     //     ->assertSeeHtmlInOrder([
    //     //         'Forging',
    //     //         '<span>52</span>',
    //     //         'Missed',
    //     //         '<span>0</span>',
    //     //         'Not Forging',
    //     //         '<span>1</span>',
    //     //         'Current Height',
    //     //     ]);

    //     performRequest($this)
    //         ->assertSeeHtmlInOrder([
    //             'Forging',
    //             '<span>52</span>',
    //             'Missed',
    //             '<span>0</span>',
    //             'Not Forging',
    //             '<span>1</span>',
    //             'Current Height',
    //         ]);
    // });

    it('should should poll when component is ready', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        createRealisticRound([
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 48, true),
            ],
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 48, true),
            ],
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 48, true),
            ],
        ], $this);

        // Livewire::test(Monitor::class)
        //     ->assertDontSeeHtml('<span>52</span>')
        //     ->assertDontSeeHtml('<span>0</span>')
        //     ->assertDontSeeHtml('<span>1</span>')
        //     ->dispatch('monitorIsReady')
        //     ->assertSeeHtmlInOrder([
        //         'Forging',
        //         '<span>52</span>',
        //         'Missed',
        //         '<span>0</span>',
        //         'Not Forging',
        //         '<span>1</span>',
        //         'Current Height',
        //     ]);

        $request = performRequest($this)->assertDontSeeHtml('<span>52</span>')
                ->assertDontSeeHtml('<span>0</span>')
                ->assertDontSeeHtml('<span>1</span>');

        $request->assertInertia(function (Assert $inertia) {
            dd($this);
        });
        //         , pageCallback: function (Assert $page) {
        //     $page->assertDontSeeHtml('<span>52</span>')
        //         ->assertDontSeeHtml('<span>0</span>')
        //         ->assertDontSeeHtml('<span>1</span>');
        // }, reloadCallback: function (Assert $reload) {
        //     $reload->assertSeeHtmlInOrder([
        //         'Forging',
        //         '<span>52</span>',
        //         'Missed',
        //         '<span>0</span>',
        //         'Not Forging',
        //         '<span>1</span>',
        //         'Current Height',
        //     ]);
        // });
    });
});
