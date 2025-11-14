<?php

declare(strict_types=1);

use App\Enums\DelegateForgingStatus;
use App\Facades\Network;
use App\Facades\Rounds;
use App\Http\Livewire\Delegates\Monitor;
use App\Models\Block;
use App\Models\Round;
use App\Models\Wallet;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\WalletCache;
use App\Services\Monitor\DelegateTracker;
use App\Services\Monitor\ForgingInfoCalculator;
use App\Services\Monitor\Slots;
use App\ViewModels\WalletViewModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use function Tests\createBlock;
use function Tests\createPartialRound;
use function Tests\createRealisticRound;
use function Tests\createRoundEntry;
use function Tests\getRoundDelegates;

function createPartialTestRounds(
    int &$round,
    int &$height,
    string $requiredPublicKey,
    array $didForge,
    $context,
    ?string $missedPublicKey = null,
    ?int $blocks = 49,
    ?int $slots = null,
): void {
    getRoundDelegates(false, $round - 1);

    $missedPublicKeys = [];
    if ($missedPublicKey !== null) {
        $missedPublicKeys = [$missedPublicKey];
    }

    createPartialRound($round, $height, $blocks, $context, $missedPublicKeys, [$requiredPublicKey], true, $slots);
}

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
        $originalOrder     = ForgingInfoCalculator::calculateOriginalOrder((new Slots())->getTime(), 1);
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
        expect($delegate->keepsMissing())->toBe(true);

        Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollDelegates')
            ->assertSeeInOrder([
                $delegate->username(),
                'Delegate last forged 199 blocks ago (~ 27 min)',
            ]);

        expect($delegate->blocksSinceLastForged())->toBe(199);
        expect($delegate->durationSinceLastForged())->toBe('~ 27 min');
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

        expect($delegate->blocksSinceLastForged())->toBe(199);
        expect($delegate->durationSinceLastForged())->toBe('~ 1h 27 min');
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

        expect($delegate->blocksSinceLastForged())->toBe(199);
        expect($delegate->durationSinceLastForged())->toBe('more than a day');
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

    it('should show no overflow delegates if no missed blocks', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        createRealisticRound([
            array_fill(0, 51, true),
        ], $this);

        $this->travelTo(Carbon::parse('2024-02-03 15:00:00Z'));

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollDelegates');

        $instance = $component->instance();

        $overflowDelegates = $instance->getOverflowDelegatesProperty();

        expect($overflowDelegates)->toHaveCount(0);
    });

    it('should show no overflow delegates at the start of a round', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [$delegates, $round] = createRealisticRound([
            array_fill(0, 51, true),
        ], $this);

        foreach ($delegates as $delegate) {
            createRoundEntry($round, $delegate->public_key);
        }

        $this->travelTo(Carbon::parse('2024-02-03 15:00:00Z'));

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollDelegates');

        $instance = $component->instance();

        $overflowDelegates = $instance->getOverflowDelegatesProperty();

        expect($overflowDelegates)->toHaveCount(0);
    });

    it('should show overflow delegates with a full round', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        createRealisticRound([
            // array_fill(0, 51, true),
            [
                ...array_fill(0, 8, true),
                false,
                false,
                false,
                false,
                false,
                ...array_fill(0, 38, true),
            ],
        ], $this);

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollDelegates');

        $instance = $component->instance();

        $overflowDelegates = $instance->getOverflowDelegatesProperty();

        expect(collect($overflowDelegates)->map(fn ($delegate) => $delegate->status())->toArray())->toBe([
            'done',
            'done',
            'done',
            'done',
            'done',
        ]);
    });

    it('should show overflow delegates at the end of all initial slots', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [$delegates, $round, $height] = createRealisticRound([
            array_fill(0, 51, true),
        ], $this);

        $requiredPublicKeys = [
            $delegates->get(4)->public_key,
            $delegates->get(5)->public_key,
            $delegates->get(6)->public_key,
            $delegates->get(7)->public_key,
            $delegates->get(8)->public_key,
        ];

        createPartialRound($round, $height, null, $this, $requiredPublicKeys, $requiredPublicKeys, true, 51);

        // dump($round, $height);

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollDelegates');

        $instance = $component->instance();

        $overflowDelegates = $instance->getOverflowDelegatesProperty();

        // expect($overflowDelegates)->toHaveCount(5);
        expect(collect($overflowDelegates)->map(fn ($delegate) => $delegate->status())->toArray())->toBe([
            'next',
            'pending',
            'pending',
            'pending',
            'pending',
        ]);
    });

    it('should show overflow delegates for partial round', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [$delegates, $round, $height] = createRealisticRound([
            array_fill(0, Network::delegateCount(), true),
        ], $this);

        createPartialRound($round, $height, Network::delegateCount() - 1, $this, [
            $delegates->get(4)->public_key,
            $delegates->get(5)->public_key,
            $delegates->get(6)->public_key,
            $delegates->get(7)->public_key,
            $delegates->get(8)->public_key,
        ], [
            $delegates->get(4)->public_key,
            $delegates->get(5)->public_key,
            $delegates->get(6)->public_key,
            $delegates->get(7)->public_key,
            $delegates->get(8)->public_key,
        ]);

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollData');

        $instance = $component->instance();

        $overflowDelegates = $instance->getOverflowDelegatesProperty();

        // expect($overflowDelegates)->toHaveCount(5);
        expect(collect($overflowDelegates)->map(fn ($delegate) => $delegate->status())->toArray())->toBe([
            'done',
            'done',
            'done',
            'done',
            'next',
        ]);
    });

    it('should track overflow slots correctly', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [$delegates, $round, $height] = createRealisticRound([
            array_fill(0, Network::delegateCount(), true),
        ], $this);

        [$delegates, $round, $height] = createPartialRound($round, $height, null, $this, [
            $delegates->get(4)->public_key,
            $delegates->get(5)->public_key,
            $delegates->get(6)->public_key,
            $delegates->get(7)->public_key,
            $delegates->get(8)->public_key,
        ], [
            $delegates->get(4)->public_key,
            $delegates->get(5)->public_key,
            $delegates->get(6)->public_key,
            $delegates->get(7)->public_key,
            $delegates->get(8)->public_key,
        ], true, Network::delegateCount());

        $delegates = getRoundDelegates(false, $round - 1);

        createBlock($height, $delegates->get(0)['publicKey'], $this);
        createBlock($height + 1, $delegates->get(1)['publicKey'], $this);

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollData');

        $instance = $component->instance();

        $overflowDelegates = $instance->getOverflowDelegatesProperty();

        // expect($overflowDelegates)->toHaveCount(5);
        expect(collect($overflowDelegates)->map(fn ($delegate) => $delegate->status())->toArray())->toBe([
            'done',
            'done',
            'next',
            'pending',
            'pending',
        ]);
    });

    it('should handle when an overflow delegate misses a block', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [$delegates, $round, $height] = createRealisticRound([
            array_fill(0, Network::delegateCount(), true),
        ], $this);

        [$delegates, $round, $height] = createPartialRound($round, $height, null, $this, [
            $delegates->get(4)->public_key,
            $delegates->get(5)->public_key,
            $delegates->get(6)->public_key,
            $delegates->get(7)->public_key,
            $delegates->get(8)->public_key,
        ], [
            $delegates->get(4)->public_key,
            $delegates->get(5)->public_key,
            $delegates->get(6)->public_key,
            $delegates->get(7)->public_key,
            $delegates->get(8)->public_key,
        ], true, Network::delegateCount());

        $delegates = getRoundDelegates(false, $round - 1);

        // Overflow slot 1
        createBlock($height, $delegates->get(0)['publicKey'], $this);

        // Overflow slot 2
        $this->travel(Network::blockTime() + 2)->seconds();

        // Overflow slot 3
        createBlock($height + 1, $delegates->get(2)['publicKey'], $this);

        $overflowForgeTime = Carbon::parse('2024-02-01 14:00:00Z')->addSeconds((Network::blockTime() * (Network::delegateCount() + 4)));

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollData');

        $instance = $component->instance();

        $overflowDelegates = $instance->getOverflowDelegatesProperty();

        // expect($overflowDelegates)->toHaveCount(6);
        expect(collect($overflowDelegates)->map(fn ($delegate) => $delegate->status())->toArray())->toBe([
            'done',
            'done',
            'done',
            'next',
            'pending',
            'pending',
        ]);

        expect(collect($overflowDelegates)->map(fn ($delegate) => $delegate->forgingAt()->format('Y-m-d H:i:s'))->toArray())->toBe([
            $overflowForgeTime->format('Y-m-d H:i:s'),
            $overflowForgeTime->addSeconds(Network::blockTime())->format('Y-m-d H:i:s'),
            $overflowForgeTime->addSeconds(Network::blockTime() + 2)->format('Y-m-d H:i:s'), // Missed overflow block
            $overflowForgeTime->addSeconds(Network::blockTime())->format('Y-m-d H:i:s'),
            $overflowForgeTime->addSeconds(Network::blockTime())->format('Y-m-d H:i:s'),
            $overflowForgeTime->addSeconds(Network::blockTime())->format('Y-m-d H:i:s'),
        ]);
    });

    it('should correctly show overflow if only a single block was missed', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [$delegates, $round, $height] = createRealisticRound([
            array_fill(0, 51, true),
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 46, true),
            ]
        ], $this);

        [$delegates, $round, $height] = createPartialRound($round, $height, null, $this, [
            $delegates->get(4)->public_key,
        ], [
            $delegates->get(4)->public_key,
        ], true, Network::delegateCount() - 4);

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollData');

        $instance = $component->instance();

        /** @var Slot[] */
        $overflowDelegates = $instance->getOverflowDelegatesProperty();

        $delegatesProperty = new ReflectionProperty($instance, 'delegates');
        $delegatesProperty->setAccessible(true);

        $slots = collect($delegatesProperty->getValue($instance))->groupBy(fn ($delegate) => $delegate->status());

        expect($slots['done'])->toHaveCount(47);
        expect($slots['pending'])->toHaveCount(3);
        expect($slots['next'])->toHaveCount(1);

        expect($overflowDelegates)->toHaveCount(1);
        expect(collect($overflowDelegates)->map(fn ($delegate) => $delegate->status())->toArray())->toBe([
            'pending',
        ]);
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
        2 => [2, '2024-02-01 14:00:16'],
        3 => [3, '2024-02-01 14:00:24'],
        4 => [4, '2024-02-01 14:00:32'],
        5 => [5, '2024-02-01 14:01:20'],
        6 => [6, '2024-02-01 14:01:36'],
    ]);
});

describe('Data Boxes', function () {
    function createRoundWithDelegatesAndPerformances(?array $performances = null, bool $addBlockForNextRound = true, int $wallets = 51, int $baseIndex = 0): void
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
        [$delegates, $round, $height] = createRealisticRound([
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
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 46, true),
            ],
        ], $this);

        Artisan::call('explorer:cache-delegate-wallets');

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollData');

        expect((new WalletViewModel($delegates->get(4)))->performance())->toBe([false, false]);
        expect($component->instance()->getDelegatePerformance($delegates->get(4)->public_key))->toBe(DelegateForgingStatus::missing);
    });

    it('should determine if delegates just missed based on their round history', function () {
        [$delegates, $round, $height] = createRealisticRound([
            array_fill(0, 51, true),
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 46, true),
            ],
        ], $this);

        Artisan::call('explorer:cache-delegate-wallets');

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollData');

        expect((new WalletViewModel($delegates->get(4)))->performance())->toBe([true, false]);
        expect($component->instance()->getDelegatePerformance($delegates->get(4)->public_key))->toBe(DelegateForgingStatus::missed);
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
        ], $this);

        createPartialRound($round, $height, 45, $this, [], [$delegates->get(4)->public_key]);

        expect((new WalletViewModel($delegates->get(4)))->performance())->toBe([false, true]);
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
