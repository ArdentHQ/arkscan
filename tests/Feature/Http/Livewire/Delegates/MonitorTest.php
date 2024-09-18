<?php

declare(strict_types=1);

use App\Contracts\RoundRepository as ContractsRoundRepository;
use App\Enums\DelegateForgingStatus;
use App\Facades\Rounds;
use App\Http\Livewire\Delegates\Monitor;
use App\Models\Block;
use App\Models\Round;
use App\Models\Wallet;
use App\Repositories\RoundRepository;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\WalletCache;
use App\Services\Monitor\DelegateTracker;
use App\Services\Monitor\ForgingInfoCalculator;
use App\Services\Monitor\Slots;
use App\ViewModels\WalletViewModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use function Tests\createPartialRound;
use function Tests\createRealisticRound;
use function Tests\getDelegateForgingPosition;

describe('Monitor', function () {
    beforeEach(function () {
        $this->activeDelegates = require dirname(dirname(dirname(dirname(__DIR__)))).'/fixtures/forgers.php';
    });

    function createRoundWithDelegates(): void
    {
        Wallet::factory(51)->create()->each(function ($wallet) {
            $block = Block::factory()->create([
                'height'               => 5720529,
                'timestamp'            => 113620904,
                'generator_public_key' => $wallet->public_key,
            ]);

            // Start height for round 112168
            Block::factory()->create([
                'height'               => 5720518,
                'timestamp'            => 113620904,
                'generator_public_key' => $wallet->public_key,
            ]);

            Round::factory()->create([
                'round'      => '112168',
                'public_key' => $wallet->public_key,
            ]);

            (new WalletCache())->setDelegate($wallet->public_key, $wallet);

            (new WalletCache())->setLastBlock($wallet->public_key, [
                'id'     => $block->id,
                'height' => $block->height->toNumber(),
            ]);
        });
    }

    function forgeBlock(string $publicKey, int &$height): void
    {
        $block = Block::factory()->create([
            'height'               => $height,
            'generator_public_key' => $publicKey,
            'timestamp'            => (new Slots())->getTime(),
        ]);

        (new WalletCache())->setLastBlock($publicKey, [
            'id'     => $block->id,
            'height' => $block->height->toNumber(),
        ]);

        $height++;
    }

    it('should render without errors', function () {
        createRoundWithDelegates();

        Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->assertSeeHtml('pollData');
    });

    it('should throw an exception after 3 tries', function () {
        createRoundWithDelegates();

        $this->expectExceptionMessage('Something went wrong!');

        Cache::shouldReceive('tags')
            ->andThrow(new Exception('Something went wrong!'))
            ->shouldReceive('increment')
            ->andReturn(1, 2, 3);

        Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollDelegates');
    });

    it('shouldnt throw an exception if only fails 2 times', function () {
        createRoundWithDelegates();

        $taggedCache = Cache::tags('tags');

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady');

        Cache::shouldReceive('tags')
            ->with('rounds')
            ->once()
            ->andThrow(new Exception('Something went wrong!'))
            ->shouldReceive('increment')
            ->andReturn(1, 2, 3)
            ->shouldReceive('remember')
            ->andReturnUsing(fn ($tag, $time, $closure) => $closure())
            ->shouldReceive('tags')
            ->andReturn($taggedCache)
            ->shouldReceive('forget')
            ->andReturn(null);

        $component->call('pollDelegates');
    });

    it('should get the last blocks from the last 2 rounds and beyond', function () {
        $wallets = Wallet::factory(51)->create()->each(function ($wallet) {
            Round::factory()->create([
                'round'      => '1',
                'public_key' => $wallet->public_key,
            ]);

            for ($i = 0; $i < 3; $i++) {
                Block::factory()->create([
                    'height'               => $i,
                    'generator_public_key' => $wallet->public_key,
                ]);
            }

            (new WalletCache())->setDelegate($wallet->public_key, $wallet);
        });

        $wallets->first()->blocks()->delete();

        Livewire::test(Monitor::class)
            ->call('setIsReady')->call('pollDelegates');

        expect((new WalletCache())->getLastBlock($wallets->first()->public_key))->toBe([]);

        foreach ($wallets->skip(1) as $wallet) {
            expect((new WalletCache())->getLastBlock($wallet->public_key))->not()->toBe([]);
        }
    });

    it('should do nothing if no rounds', function () {
        Wallet::factory(51)->create()->each(function ($wallet) {
            Round::factory()->create([
                'round'      => '1',
                'public_key' => $wallet->public_key,
            ]);
        });

        // Mark component delegate property as public & update monitor data
        $delegateProperty = new ReflectionProperty(Monitor::class, 'delegates');
        $delegateProperty->setAccessible(true);

        $component = Livewire::test(Monitor::class);
        $component->call('setIsReady');

        expect($delegateProperty->getValue($component->instance()))->toBe([]);

        $component->call('pollDelegates');

        expect($delegateProperty->getValue($component->instance()))->toBe([]);
    });

    it('should set it ready on event', function () {
        Wallet::factory(51)->create()->each(function ($wallet) {
            Round::factory()->create([
                'round'      => '1',
                'public_key' => $wallet->public_key,
            ]);
        });

        Livewire::test(Monitor::class)
            ->assertSet('isReady', false)
            ->dispatch('monitorIsReady')
            ->assertSet('isReady', true);
    });

    it('should not poll if not ready', function () {
        Wallet::factory(51)->create()->each(function ($wallet) {
            Round::factory()->create([
                'round'      => '1',
                'public_key' => $wallet->public_key,
            ]);
        });

        // Mark component delegate property as public & update monitor data
        $delegateProperty = new ReflectionProperty(Monitor::class, 'delegates');
        $delegateProperty->setAccessible(true);

        $component = Livewire::test(Monitor::class);

        expect($delegateProperty->getValue($component->instance()))->toBe([]);

        $component->instance()->pollDelegates();

        expect($delegateProperty->getValue($component->instance()))->toBe([]);
    });

    it('should correctly show the block is missed', function () {
        // Force round time
        $this->travelTo(new Carbon('2021-01-01 00:04:00'));

        // Create wallets for each delegate
        $this->activeDelegates->each(function ($delegate) use (&$wallets) {
            $wallet = Wallet::factory()->create(['public_key' => $delegate->public_key]);

            Round::factory()->create([
                'round'      => '1',
                'public_key' => $delegate->public_key,
                'balance'    => 0,
            ]);

            (new WalletCache())->setDelegate($delegate->public_key, $wallet);
        });

        // Store delegate record for each Round object
        $wallets = Rounds::allByRound(1)->map(fn ($round) => $round->delegate);

        // Make methods public for fetching forging order
        $activeDelegatesMethod  = new ReflectionMethod(DelegateTracker::class, 'getActiveDelegates');
        $shuffleDelegatesMethod = new ReflectionMethod(DelegateTracker::class, 'shuffleDelegates');
        $orderDelegatesMethod   = new ReflectionMethod(DelegateTracker::class, 'orderDelegates');
        $activeDelegatesMethod->setAccessible(true);
        $shuffleDelegatesMethod->setAccessible(true);
        $orderDelegatesMethod->setAccessible(true);

        // Get delegate order so we can forge in the correct order
        $originalOrder     = ForgingInfoCalculator::calculate((new Slots())->getTime(), 1);
        $activeDelegates   = $activeDelegatesMethod->invokeArgs(null, [$wallets]);
        $shuffledDelegates = $shuffleDelegatesMethod->invokeArgs(null, [$activeDelegates, 1]);
        $delegatesInOrder  = collect($orderDelegatesMethod->invokeArgs(null, [
            $shuffledDelegates,
            $originalOrder['currentForger'],
            51,
        ]));

        // Forge blocks for first 5 delegates
        $height = 1;
        $delegatesInOrder->take(5)->each(function ($publicKey) use (&$height) {
            forgeBlock($publicKey, $height);

            $this->travel(8)->seconds();
        });

        // Mark component delegate property as public & update monitor data
        $delegateProperty = new ReflectionProperty(Monitor::class, 'delegates');
        $delegateProperty->setAccessible(true);

        $component = Livewire::test(Monitor::class);

        expect($delegateProperty->getValue($component->instance()))->toBe([]);

        $component->call('setIsReady');

        $instance  = $component->instance();
        $instance->pollDelegates();

        $delegates = collect($delegateProperty->getValue($instance));

        expect($delegates)->toHaveCount(51);

        // Split up delegate slot data to check
        $forgedDelegates  = $delegates->splice(0, 5);
        $waitingDelegates = $delegates->splice(0, 1);
        $missedDelegates  = $delegates->splice(0, 5);

        $forgedDelegates->each(fn ($delegate) => expect($delegate->hasForged())->toBeTrue());
        $waitingDelegates->each(fn ($delegate) => expect($delegate->isNext())->toBeTrue());
        $missedDelegates->each(fn ($delegate) => expect($delegate->isPending())->toBeTrue());

        // Progress time by 15 delegate slots
        $this->travel(14 * 8)->seconds();

        // Forge block with 20th delegate
        forgeBlock($delegatesInOrder->get(20), $height);
        $this->travel(8)->seconds();

        // Update delegate data again
        $instance->pollDelegates();

        $delegates = collect($delegateProperty->getValue($instance));

        expect($delegates)->toHaveCount(51);

        // Check delegate data is correct after 15 missed blocks
        $forgedDelegates  = $delegates->splice(0, 5);
        $missedDelegates  = $delegates->splice(0, 15);
        $waitingDelegates = $delegates->splice(0, 1);

        $forgedDelegates->each(fn ($delegate) => expect($delegate->isWaiting())->toBeFalse());
        $forgedDelegates->each(fn ($delegate) => expect($delegate->hasForged())->toBeTrue());
        $missedDelegates->each(fn ($delegate) => expect($delegate->isWaiting())->toBeFalse());
        $missedDelegates->each(fn ($delegate) => expect($delegate->justMissed())->toBeTrue());
        $waitingDelegates->each(fn ($delegate) => expect($delegate->isNext())->toBeTrue());

        $outputData = [];
        $forgedDelegates->each(function ($delegate) use (&$outputData) {
            $outputData[] = $delegate->wallet()->username();
            $outputData[] = 'Completed';
        });
        $missedDelegates->each(function ($delegate) use (&$outputData) {
            $outputData[] = $delegate->wallet()->username();
            $outputData[] = 'Missed';
        });
        $waitingDelegates->each(function ($delegate) use (&$outputData) {
            $outputData[] = $delegate->wallet()->username();
            $outputData[] = 'Now';
        });

        $component
            ->call('pollDelegates')
            ->assertSeeInOrder($outputData);
    });

    it('should show warning icon for delegates missing blocks - minutes', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [0 => $delegates] = createRealisticRound([
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 46, true),
            ],
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 46, true),
            ],
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 46, true),
            ],
        ], $this);

        $delegate = (new WalletViewModel($delegates->get(4)));

        expect($delegate->performance())->toBe([false, false]);

        Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollDelegates')
            ->assertSeeInOrder([
                $delegate->username(),
                'Delegate last forged 199 blocks ago (~ 27 min)',
            ]);
    });

    it('should show warning icon for delegates missing blocks - hours', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [0 => $delegates] = createRealisticRound([
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 46, true),
            ],
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 46, true),
            ],
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 46, true),
            ],
        ], $this);

        $this->travelTo(Carbon::parse('2024-02-01 15:00:00Z'));

        $delegate = (new WalletViewModel($delegates->get(4)));

        expect($delegate->performance())->toBe([false, false]);

        Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollDelegates')
            ->assertSeeInOrder([
                $delegate->username(),
                'Delegate last forged 199 blocks ago (~ 1h 27 min)',
            ]);
    });

    it('should show warning icon for delegates missing blocks - days', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [0 => $delegates] = createRealisticRound([
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 46, true),
            ],
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 46, true),
            ],
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 46, true),
            ],
        ], $this);

        $this->travelTo(Carbon::parse('2024-02-03 15:00:00Z'));

        $delegate = (new WalletViewModel($delegates->get(4)));

        expect($delegate->performance())->toBe([false, false]);

        Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollDelegates')
            ->assertSeeInOrder([
                $delegate->username(),
                'Delegate last forged 199 blocks ago (more than a day)',
            ]);
    });

    it('should reload on new block event', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [0 => $delegates] = createRealisticRound([
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 46, true),
            ],
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 46, true),
            ],
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 46, true),
            ],
        ], $this);

        $this->travelTo(Carbon::parse('2024-02-03 15:00:00Z'));

        $delegate = (new WalletViewModel($delegates->get(4)));

        Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->dispatch('echo:blocks,NewBlock')
            ->assertSeeInOrder([
                $delegate->username(),
                'Delegate last forged 199 blocks ago (more than a day)',
            ]);
    });
});

describe('Data Boxes', function() {
    beforeEach(function () {
        $this->app->bind(ContractsRoundRepository::class, function (): RoundRepository {
            return new RoundRepository();
        });

        $this->travelTo(Carbon::parse('2022-08-22 00:00'));
        $this->freezeTime();
    });

    function createRoundWithDelegatesAndPerformances(array $performances = null, bool $addBlockForNextRound = true, int $wallets = 51, int $baseIndex = 0): void
    {
        Wallet::factory($wallets)->create()->each(function ($wallet, $index) use ($performances, $addBlockForNextRound, $baseIndex) {
            $timestamp = Carbon::now()->add(($baseIndex + $index) * 8, 'seconds')->timestamp;

            $block = Block::factory()->create([
                'height'               => 5720529,
                'timestamp'            => $timestamp,
                'generator_public_key' => $wallet->public_key,
            ]);

            // Start height for round 112168
            if ($addBlockForNextRound) {
                Block::factory()->create([
                    'height'               => 5720518,
                    'timestamp'            => $timestamp,
                    'generator_public_key' => $wallet->public_key,
                ]);
            }

            Round::factory()->create([
                'round'      => '112167',
                'public_key' => $wallet->public_key,
            ]);

            Round::factory()->create([
                'round'      => '112168',
                'public_key' => $wallet->public_key,
            ]);

            (new WalletCache())->setDelegate($wallet->public_key, $wallet);

            if (is_null($performances)) {
                for ($i = 0; $i < 2; $i++) {
                    $performances[] = (bool) mt_rand(0, 1);
                }
            }

            (new WalletCache())->setPerformance($wallet->public_key, $performances);

            (new WalletCache())->setLastBlock($wallet->public_key, [
                'id'     => $block->id,
                'height' => $block->height->toNumber(),
            ]);
        });
    }

    function createPartialTestRounds(
        int &$round,
        int &$height,
        string $requiredPublicKey,
        array $didForge,
        $context,
        string $missedPublicKey = null,
        int $blocks = 49,
        ?int $slots = null,
    ): void
    {
        $delegateForgingPosition = getDelegateForgingPosition($round, $requiredPublicKey);
        while ($delegateForgingPosition >= $blocks - 2 || $delegateForgingPosition === 0) {
            [1 => $round, 2 => $height] = createRealisticRound($didForge, $context);

            $delegateForgingPosition = getDelegateForgingPosition($round, $requiredPublicKey);
        }

        createPartialRound(
            $round,
            $height,
            $blocks,
            $context,
            [$requiredPublicKey],
            [$missedPublicKey],
            slots: $slots,
        );
    }

    it('should render without errors', function () {
        createRoundWithDelegatesAndPerformances();

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady');

        $component->call('pollData');

        $component->assertHasNoErrors();
        $component->assertViewIs('livewire.delegates.monitor');
    });

    it('should handle case no block yet', function () {
        createRoundWithDelegatesAndPerformances(null, false);

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady');

        $component->call('pollData');

        $component->assertHasNoErrors();

        $component->assertViewIs('livewire.delegates.monitor');
    });

    it('should get the performances of active delegates and parse it into a readable array', function () {
        createRoundWithDelegatesAndPerformances();

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady');

        $component->call('pollData');

        expect($component->instance()->getDelegatesPerformance())->toBeArray();
        expect($component->instance()->getDelegatesPerformance())->toHaveKeys(['forging', 'missed', 'missing']);
    });

    it('should determine if delegates are forging based on their round history', function () {
        createRoundWithDelegatesAndPerformances([true, true], false);

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady');

        $component->call('pollData');

        $delegateWallet = Wallet::first();
        $delegate       = new WalletViewModel($delegateWallet);

        expect($component->instance()->getDelegatePerformance($delegate->publicKey()))->toBe(DelegateForgingStatus::forging);
    });

    it('should determine if delegates are not forging based on their round history', function () {
        createRoundWithDelegatesAndPerformances([false, false], false);

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady');

        $component->call('pollData');

        $delegateWallet = Wallet::first();
        $delegate       = new WalletViewModel($delegateWallet);

        expect($component->instance()->getDelegatePerformance($delegate->publicKey()))->toBe(DelegateForgingStatus::missing);
    });

    it('should determine if delegates just missed based on their round history', function () {
        createRoundWithDelegatesAndPerformances([true, false], false);

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady');

        $component->call('pollData');

        $delegateWallet = Wallet::first();
        $delegate       = new WalletViewModel($delegateWallet);

        expect($component->instance()->getDelegatePerformance($delegate->publicKey()))->toBe(DelegateForgingStatus::missed);
    });

    it('should determine if delegates are forging after missing 4 slots based on their round history', function () {
        createRoundWithDelegatesAndPerformances([false, true], false);

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady');

        $component->call('pollData');

        $delegateWallet = Wallet::first();
        $delegate       = new WalletViewModel($delegateWallet);

        expect($component->instance()->getDelegatePerformance($delegate->publicKey()))->toBe(DelegateForgingStatus::forging);
    });

    it('should return the block count', function () {
        createRoundWithDelegatesAndPerformances();

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady');

        expect($component->instance()->getBlockCount())->toBeString();
    });

    it('should return the next delegate', function () {
        createRoundWithDelegatesAndPerformances();

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollData');

        expect($component->instance()->getNextdelegate())->toBeInstanceOf(WalletViewModel::class);
    });

    it('should not error if no cached delegate data', function () {
        $wallets = Wallet::factory(51)
            ->activeDelegate()
            ->create()
            ->each(function ($wallet) {
                $block = Block::factory()->create([
                    'height'               => 5720529,
                    'timestamp'            => 113620904,
                    'generator_public_key' => $wallet->public_key,
                ]);

                Block::factory()->create([
                    'height'               => 5720518,
                    'timestamp'            => 113620904,
                    'generator_public_key' => $wallet->public_key,
                ]);

                Round::factory()->create([
                    'round'      => '112168',
                    'public_key' => $wallet->public_key,
                ]);
            });

        foreach ($wallets as $wallet) {
            expect((new WalletCache())->getDelegate($wallet->public_key))->toBeNull();
        }

        Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->assertSeeHtml('rounded-sm-md animate-pulse bg-theme-secondary-300 dark:bg-theme-dark-800 w-[70px] h-5')
            ->assertSet('statistics.nextDelegate', null);
    });

    it('should defer loading', function () {
        createRoundWithDelegatesAndPerformances();

        (new NetworkCache())->setHeight(fn (): int => 4234212);

        Livewire::test(Monitor::class)
            ->call('pollData')
            ->assertViewHas('height', 0)
            ->assertViewHas('statistics', [])
            ->assertDontSee('4,234,212')
            ->call('setIsReady')
            ->assertViewHas('height', 4234212)
            ->assertDontSee('4,234,212')
            ->call('pollData')
            ->assertSee('4,234,212');
    });

    it('should calculate forged correctly with current round', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));
        $this->freezeTime();

        [$delegates, $round, $height] = createRealisticRound([
            array_fill(0, 51, true),
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 46, true),
            ],
            array_fill(0, 51, true),
        ], $this);

        $publicKey = $delegates->get(4)->public_key;
        createPartialTestRounds($round, $height, $publicKey, [
            array_fill(0, 51, true),
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 46, true),
            ],
            array_fill(0, 51, true),
        ], $this, null, 51);

        expect((new WalletViewModel($delegates->get(4)))->performance())->toBe([false, true]);

        Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollData')
            ->assertSeeHtmlInOrder([
                'Forging',
                '<span>51</span>',
                'Missed',
                '<span>0</span>',
                'Not Forging',
                '<span>0</span>',
                'Current Height',
            ]);
    });

    it('should calculate forged correctly for previous rounds', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [0 => $delegates] = createRealisticRound([
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 46, true),
            ],
            array_fill(0, 51, true),
        ], $this);

        expect((new WalletViewModel($delegates->get(4)))->performance())->toBe([false, true]);

        Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollData')
            ->assertSeeHtmlInOrder([
                'Forging',
                '<span>51</span>',
                'Missed',
                '<span>0</span>',
                'Not Forging',
                '<span>0</span>',
                'Current Height',
            ]);
    });

    it('should calculate missed correctly with current round', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [$delegates, $round, $height] = createRealisticRound([
            array_fill(0, 51, true),
            array_fill(0, 51, true),
            array_fill(0, 51, true),
        ], $this);

        $publicKey = $delegates->get(4)->public_key;

        createPartialTestRounds($round, $height, $publicKey, [
            array_fill(0, 51, true),
        ], $this, $publicKey, 50, 51);

        expect((new WalletViewModel($delegates->get(4)))->performance())->toBe([true, false]);

        Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollData')
            ->assertSeeHtmlInOrder([
                'Forging',
                '<span>50</span>',
                'Missed',
                '<span>1</span>',
                'Not Forging',
                '<span>0</span>',
                'Current Height',
            ]);
    });

    it('should calculate missed correctly for previous rounds', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [0 => $delegates] = createRealisticRound([
            array_fill(0, 51, true),
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 46, true),
            ],
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 46, true),
            ],
        ], $this);

        expect((new WalletViewModel($delegates->get(4)))->performance())->toBe([true, false]);

        Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollData')
            ->assertSeeHtmlInOrder([
                'Forging',
                '<span>50</span>',
                'Missed',
                '<span>1</span>',
                'Not Forging',
                '<span>0</span>',
                'Current Height',
            ]);
    });

    it('should calculate not forging correctly with current round', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [$delegates, $round, $height] = createRealisticRound([
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 46, true),
            ],
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 46, true),
            ],
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 46, true),
            ],
        ], $this);

        $publicKey = $delegates->get(4)->public_key;
        createPartialTestRounds($round, $height, $publicKey, [
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 46, true),
            ],
        ], $this, $publicKey, 51);

        expect((new WalletViewModel($delegates->get(4)))->performance())->toBe([false, false]);

        Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollData')
            ->assertSeeHtmlInOrder([
                'Forging',
                '<span>50</span>',
                'Missed',
                '<span>0</span>',
                'Not Forging',
                '<span>1</span>',
                'Current Height',
            ]);
    });

    it('should calculate not forging correctly for previous rounds', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [0 => $delegates] = createRealisticRound([
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 46, true),
            ],
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 46, true),
            ],
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 46, true),
            ],
        ], $this);

        expect((new WalletViewModel($delegates->get(4)))->performance())->toBe([false, false]);

        Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollData')
            ->assertSeeHtmlInOrder([
                'Forging',
                '<span>50</span>',
                'Missed',
                '<span>0</span>',
                'Not Forging',
                '<span>1</span>',
                'Current Height',
            ]);
    });

    it('should reload on new block event', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        createRealisticRound([
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 46, true),
            ],
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 46, true),
            ],
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 46, true),
            ],
        ], $this);

        Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->assertDontSeeHtml('<span>50</span>')
            ->assertDontSeeHtml('<span>0</span>')
            ->assertDontSeeHtml('<span>1</span>')
            ->dispatch('echo:blocks,NewBlock')
            ->assertSeeHtmlInOrder([
                'Forging',
                '<span>50</span>',
                'Missed',
                '<span>0</span>',
                'Not Forging',
                '<span>1</span>',
                'Current Height',
            ]);
    });

    it('should should poll when component is ready', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        createRealisticRound([
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 46, true),
            ],
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 46, true),
            ],
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 46, true),
            ],
        ], $this);

        Livewire::test(Monitor::class)
            ->assertDontSeeHtml('<span>50</span>')
            ->assertDontSeeHtml('<span>0</span>')
            ->assertDontSeeHtml('<span>1</span>')
            ->call('componentIsReady')
            ->assertSeeHtmlInOrder([
                'Forging',
                '<span>50</span>',
                'Missed',
                '<span>0</span>',
                'Not Forging',
                '<span>1</span>',
                'Current Height',
            ]);
    });
});
