<?php

declare(strict_types=1);

use App\Contracts\RoundRepository as ContractsRoundRepository;
use App\Enums\ValidatorForgingStatus;
use App\Facades\Network;
use App\Facades\Rounds;
use App\Models\Block;
use App\Models\Wallet;
use App\Repositories\RoundRepository;
use App\Services\Cache\WalletCache;
use App\ViewModels\WalletViewModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Dusk\Browser;
use Livewire\Livewire;
use function Tests\createBlock;
use function Tests\createPartialRound;
use function Tests\createRealisticRound;
use function Tests\createRoundEntry;
use function Tests\getRoundValidators;

beforeEach(function () {
    $this->withoutExceptionHandling();
});

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

    it('should show warning icon for validators missing blocks - minutes', function () {
        $this->freezeTime();

        [0 => $validators] = createRealisticRound([
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

        foreach ($validators as $validator) {
            $block = $validator->blocks()->orderBy('number', 'desc')->first();

            (new WalletCache())->setLastBlock($validator->address, [
                'hash'                   => $block['hash'],
                'number'                 => $block['number']->toNumber(),
                'timestamp'              => $block['timestamp'],
                'proposer'               => $validator->address,
            ]);
        }

        $validator = (new WalletViewModel($validators->get(4)));

        expect($validator->performance())->toBe([false, false]);

        $this->browse(function (Browser $browser) use ($validator) {
            $browser->visitRoute('validator-monitor')
                ->waitFor('[data-testid="missed-warning-'.$validator->address().'"]', 10)
                ->mouseOver('[data-testid="missed-warning-'.$validator->address().'"]')
                ->waitForText('Validator last forged 207 blocks ago (~ 28 min)', 10);
        });
    });

    it('should show warning icon for validators missing blocks - hours', function () {
        $this->travelTo(Carbon::now()->subHours(1));

        $this->freezeTime();

        [0 => $validators] = createRealisticRound([
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

        foreach ($validators as $validator) {
            $block = $validator->blocks()->orderBy('number', 'desc')->first();

            (new WalletCache())->setLastBlock($validator->address, [
                'hash'                   => $block['hash'],
                'number'                 => $block['number']->toNumber(),
                'timestamp'              => $block['timestamp'],
                'proposer'               => $validator->address,
            ]);
        }

        $validator = (new WalletViewModel($validators->get(4)));

        expect($validator->performance())->toBe([false, false]);

        $this->browse(function (Browser $browser) use ($validator) {
            $browser->visitRoute('validator-monitor')
                ->waitFor('[data-testid="missed-warning-'.$validator->address().'"]', 10)
                ->mouseOver('[data-testid="missed-warning-'.$validator->address().'"]')
                ->waitForText('Validator last forged 207 blocks ago (~ 1h 28 min)', 10);
        });
    });

    it('should show warning icon for validators missing blocks - days', function () {
        $this->travelTo(Carbon::now()->subDays(2));

        $this->freezeTime();

        [0 => $validators] = createRealisticRound([
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

        foreach ($validators as $validator) {
            $block = $validator->blocks()->orderBy('number', 'desc')->first();

            (new WalletCache())->setLastBlock($validator->address, [
                'hash'                   => $block['hash'],
                'number'                 => $block['number']->toNumber(),
                'timestamp'              => $block['timestamp'],
                'proposer'               => $validator->address,
            ]);
        }

        $validator = (new WalletViewModel($validators->get(4)));

        expect($validator->performance())->toBe([false, false]);

        $this->browse(function (Browser $browser) use ($validator) {
            $browser->visitRoute('validator-monitor')
                ->waitFor('[data-testid="missed-warning-'.$validator->address().'"]', 10)
                ->mouseOver('[data-testid="missed-warning-'.$validator->address().'"]')
                ->waitForText('Validator last forged 207 blocks ago (more than a day)', 10);
        });
    });
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

    it('should calculate forged correctly with current round', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [$validators, $round, $height] = createRealisticRound([
            array_fill(0, 53, true),
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 48, true),
            ],
        ], $this);

        for ($i = 0; $i < 3; $i++) {
            createRoundEntry($round, $height, $validators);
            $validatorsOrder = getRoundValidators(false, $round);
            $validatorIndex  = $validatorsOrder->search(fn ($validator) => $validator['address'] === $validators->get(4)->address);
            if ($validatorIndex < 51) {
                break;
            }

            [$validators, $round, $height] = createRealisticRound([
                array_fill(0, 53, true),
                [
                    ...array_fill(0, 4, true),
                    false,
                    ...array_fill(0, 48, true),
                ],
            ], $this);
        }

        createPartialRound($round, $height, 51, $this, [], [$validators->get(4)->address]);

        expect((new WalletViewModel($validators->get(4)))->performance())->toBe([false, true]);

        $this->browse(function (Browser $browser) {
            $browser->visitRoute('validator-monitor')
                ->waitForText(' / 53 Blocks', 10)
                ->assertEquals('[data-testid="forging-count"] span', '53')
                ->assertEquals('[data-testid="missed-count"] span', '0')
                ->assertEquals('[data-testid="not-forging-count"] span', '0');
        });
    });

    it('should calculate forged correctly for previous rounds', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [0 => $validators] = createRealisticRound([
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 48, true),
            ],
            array_fill(0, 53, true),
            array_fill(0, 53, true),
        ], $this);

        expect((new WalletViewModel($validators->get(4)))->performance())->toBe([true, true]);

        $this->browse(function (Browser $browser) {
            $browser->visitRoute('validator-monitor')
                ->waitForText(' / 53 Blocks', 10)
                ->assertEquals('[data-testid="forging-count"] span', '53')
                ->assertEquals('[data-testid="missed-count"] span', '0')
                ->assertEquals('[data-testid="not-forging-count"] span', '0');
        });
    });

    it('should calculate missed correctly with current round', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [$validators, $round, $height] = createRealisticRound([
            array_fill(0, 53, true),
            array_fill(0, 53, true),
            array_fill(0, 53, true),
        ], $this);

        for ($i = 0; $i < 3; $i++) {
            createRoundEntry($round, $height, $validators);
            $validatorsOrder = getRoundValidators(false, $round);
            $validatorIndex  = $validatorsOrder->search(fn ($validator) => $validator['address'] === $validators->get(4)->address);
            if ($validatorIndex < 51) {
                break;
            }

            [$validators, $round, $height] = createRealisticRound([
                array_fill(0, 53, true),
            ], $this);
        }

        createPartialRound($round, $height, 51, $this, [$validators->get(4)->address], [$validators->get(4)->address]);

        expect((new WalletViewModel($validators->get(4)))->performance())->toBe([true, false]);

        $this->browse(function (Browser $browser) {
            $browser->visitRoute('validator-monitor')
                ->waitForText(' / 53 Blocks', 10)
                ->assertEquals('[data-testid="forging-count"] span', '52')
                ->assertEquals('[data-testid="missed-count"] span', '1')
                ->assertEquals('[data-testid="not-forging-count"] span', '0');
        });
    });

    it('should calculate missed correctly for previous rounds', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [0 => $validators] = createRealisticRound([
            array_fill(0, 53, true),
            array_fill(0, 53, true),
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 48, true),
            ],
        ], $this);

        expect((new WalletViewModel($validators->get(4)))->performance())->toBe([true, false]);

        $this->browse(function (Browser $browser) {
            $browser->visitRoute('validator-monitor')
                ->waitForText(' / 53 Blocks', 10)
                ->assertEquals('[data-testid="forging-count"] span', '52')
                ->assertEquals('[data-testid="missed-count"] span', '1')
                ->assertEquals('[data-testid="not-forging-count"] span', '0');
        });
    });

    it('should calculate not forging correctly with current round', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [$validators, $round, $height] = createRealisticRound([
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

        for ($i = 0; $i < 3; $i++) {
            createRoundEntry($round, $height, $validators);
            $validatorsOrder = getRoundValidators(false, $round);
            $validatorIndex  = $validatorsOrder->search(fn ($validator) => $validator['address'] === $validators->get(4)->address);
            if ($validatorIndex < 51) {
                break;
            }

            [$validators, $round, $height] = createRealisticRound([
                [
                    ...array_fill(0, 4, true),
                    false,
                    ...array_fill(0, 48, true),
                ],
            ], $this);
        }

        createPartialRound($round, $height, 51, $this, [$validators->get(4)->address], [$validators->get(4)->address]);

        expect((new WalletViewModel($validators->get(4)))->performance())->toBe([false, false]);

        $this->browse(function (Browser $browser) {
            $browser->visitRoute('validator-monitor')
                ->waitForText(' / 53 Blocks', 10)
                ->assertEquals('[data-testid="forging-count"] span', '52')
                ->assertEquals('[data-testid="missed-count"] span', '0')
                ->assertEquals('[data-testid="not-forging-count"] span', '1');
        });
    });

    it('should calculate not forging correctly for previous rounds', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [0 => $validators] = createRealisticRound([
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

        expect((new WalletViewModel($validators->get(4)))->performance())->toBe([false, false]);

        $this->browse(function (Browser $browser) {
            $browser->visitRoute('validator-monitor')
                ->waitForText(' / 53 Blocks', 10)
                ->assertEquals('[data-testid="forging-count"] span', '52')
                ->assertEquals('[data-testid="missed-count"] span', '0')
                ->assertEquals('[data-testid="not-forging-count"] span', '1');
        });
    });
});
