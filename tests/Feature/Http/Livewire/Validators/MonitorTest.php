<?php

declare(strict_types=1);

use App\Contracts\RoundRepository as ContractsRoundRepository;
use App\DTO\Slot;
use App\Enums\ValidatorForgingStatus;
use App\Facades\Network;
use App\Http\Livewire\Validators\Monitor;
use App\Models\Block;
use App\Models\Wallet;
use App\Repositories\RoundRepository;
use App\Services\Cache\WalletCache;
use App\ViewModels\WalletViewModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use function Tests\createBlock;
use function Tests\createPartialRound;
use function Tests\createRealisticRound;
use function Tests\createRoundEntry;
use function Tests\getRoundValidators;

describe('Monitor', function () {
    beforeEach(function () {
        $this->activeValidators = require dirname(dirname(dirname(dirname(__DIR__)))).'/fixtures/forgers.php';
    });

    function createRoundWithValidators(): void
    {
        $wallets = Wallet::factory(Network::validatorCount())->create();

        createRoundEntry(112168, 5944904, $wallets);

        $wallets->each(function ($wallet) {
            $block = Block::factory()->create([
                'height'            => 5944900,
                'timestamp'         => 113620904,
                'generator_address' => $wallet->address,
            ]);

            // Start height for round 112168
            Block::factory()->create([
                'height'            => 5944904,
                'timestamp'         => 113620904,
                'generator_address' => $wallet->address,
            ]);

            (new WalletCache())->setValidator($wallet->address, $wallet);

            (new WalletCache())->setLastBlock($wallet->address, [
                'id'     => $block->id,
                'height' => $block->height->toNumber(),
            ]);
        });
    }

    function forgeBlock(string $address, int $height): void
    {
        $block = createBlock($height, $address);

        (new WalletCache())->setLastBlock($address, [
            'id'     => $block->id,
            'height' => $block->height->toNumber(),
        ]);
    }

    it('should render without errors', function () {
        createRoundWithValidators();

        Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->assertSeeHtml('pollData');
    });

    it('should throw an exception after 3 tries', function () {
        createRoundWithValidators();

        $this->expectExceptionMessage('Something went wrong!');

        Cache::shouldReceive('remember')
            ->andThrow(new Exception('Something went wrong!'))
            ->shouldReceive('increment')
            ->andReturn(1, 2, 3);

        $instance          = Livewire::test(Monitor::class)->instance();
        $instance->isReady = true;
        $instance->pollValidators();
    });

    it('should not throw an exception if only fails 2 times', function () {
        createRoundWithValidators();

        $taggedCache = Cache::tags('tags');

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady');

        Cache::shouldReceive('remember')
            ->once()
            ->andThrow(new Exception('Something went wrong!'))
            ->shouldReceive('increment')
            ->andReturn(1, 2, 3)
            ->shouldReceive('remember')
            ->andReturnUsing(fn ($tag, $time, $closure) => $closure())
            ->shouldReceive('tags')
            ->andReturn($taggedCache)
            ->shouldReceive('driver')
            ->andReturn($taggedCache)
            ->shouldReceive('forget')
            ->andReturn(null);

        $component->call('pollValidators');
    });

    it('should get the last blocks from the last 2 rounds and beyond', function () {
        $wallets = Wallet::factory(Network::validatorCount())->create();

        createRoundEntry(1, 1, $wallets);

        $wallets->each(function ($wallet) {
            for ($i = 0; $i < 3; $i++) {
                Block::factory()->create([
                    'height'            => $i,
                    'generator_address' => $wallet->address,
                ]);
            }

            (new WalletCache())->setValidator($wallet->address, $wallet);
        });

        $wallets->first()->blocks()->delete();

        Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollValidators');

        expect((new WalletCache())->getLastBlock($wallets->first()->public_key))->toBe([]);

        foreach ($wallets->skip(1) as $wallet) {
            expect((new WalletCache())->getLastBlock($wallet->address))->not()->toBe([]);
        }
    });

    it('should do nothing if no rounds', function () {
        $wallets = Wallet::factory(Network::validatorCount())->create();

        createRoundEntry(1, 1, $wallets);

        // Mark component validator property as public & update monitor data
        $validatorProperty = new ReflectionProperty(Monitor::class, 'validators');
        $validatorProperty->setAccessible(true);

        $component = Livewire::test(Monitor::class);
        $component->call('setIsReady');

        expect($validatorProperty->getValue($component->instance()))->toBe([]);

        $component->call('pollValidators');

        expect($validatorProperty->getValue($component->instance()))->toBe([]);
    });

    it('should set it ready on event', function () {
        $wallets = Wallet::factory(Network::validatorCount())->create();

        createRoundEntry(1, 1, $wallets);

        Livewire::test(Monitor::class)
            ->assertSet('isReady', false)
            ->dispatch('monitorIsReady')
            ->assertSet('isReady', true);
    });

    it('should not poll if not ready', function () {
        $wallets = Wallet::factory(Network::validatorCount())->create();

        createRoundEntry(1, 1, $wallets);

        // Mark component validator property as public & update monitor data
        $validatorProperty = new ReflectionProperty(Monitor::class, 'validators');
        $validatorProperty->setAccessible(true);

        $component = Livewire::test(Monitor::class);

        expect($validatorProperty->getValue($component->instance()))->toBe([]);

        $component->instance()->pollValidators();

        expect($validatorProperty->getValue($component->instance()))->toBe([]);
    });

    it('should show warning icon for validators missing blocks - minutes', function () {
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

        $validator = (new WalletViewModel($validators->get(4)));

        expect($validator->performance())->toBe([false, false]);

        Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollValidators')
            ->assertSeeInOrder([
                $validator->walletName(),
                'Validator last forged 207 blocks ago (~ 28 min)',
            ]);
    });

    it('should show warning icon for validators missing blocks - hours', function () {
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

        $this->travelTo(Carbon::parse('2024-02-01 15:00:00Z'));

        $validator = (new WalletViewModel($validators->get(4)));

        expect($validator->performance())->toBe([false, false]);

        Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollValidators')
            ->assertSeeInOrder([
                $validator->walletName(),
                'Validator last forged 207 blocks ago (~ 1h 28 min)',
            ]);
    });

    it('should show warning icon for validators missing blocks - days', function () {
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

        $this->travelTo(Carbon::parse('2024-02-03 15:00:00Z'));

        $validator = (new WalletViewModel($validators->get(4)));

        expect($validator->performance())->toBe([false, false]);

        Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollValidators')
            ->assertSeeInOrder([
                $validator->walletName(),
                'Validator last forged 207 blocks ago (more than a day)',
            ]);
    });

    it('should cache last blocks', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        createRealisticRound([
            array_fill(0, 53, true),
        ], $this);

        expect(Cache::has('monitor:last-blocks'))->toBeFalse();

        Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollValidators');

        expect(Cache::has('monitor:last-blocks'))->toBeTrue();
    });

    it('should show no overflow validators if no missed blocks', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        createRealisticRound([
            array_fill(0, 53, true),
        ], $this);

        $this->travelTo(Carbon::parse('2024-02-03 15:00:00Z'));

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollValidators');

        $instance = $component->instance();

        $overflowValidators = $instance->getOverflowValidatorsProperty();

        expect($overflowValidators)->toHaveCount(0);
    });

    it('should show no overflow validators at the start of a round', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [$validators, $round, $height] = createRealisticRound([
            array_fill(0, 53, true),
        ], $this);

        createRoundEntry($round, $height, Wallet::all());

        $this->travelTo(Carbon::parse('2024-02-03 15:00:00Z'));

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollValidators');

        $instance = $component->instance();

        $overflowValidators = $instance->getOverflowValidatorsProperty();

        expect($overflowValidators)->toHaveCount(0);
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

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollValidators');

        $instance = $component->instance();

        $overflowValidators = $instance->getOverflowValidatorsProperty();

        expect($overflowValidators)->toHaveCount(5);
        expect(collect($overflowValidators)->map(fn ($validator) => $validator->status())->toArray())->toBe([
            'done',
            'done',
            'done',
            'done',
            'done',
        ]);
    });

    it('should show overflow validators at the end of all initial slots', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [$validators, $round, $height] = createRealisticRound([
            array_fill(0, 53, true),
        ], $this);

        createPartialRound($round, $height, null, $this, [
            $validators->get(4)->public_key,
            $validators->get(5)->public_key,
            $validators->get(6)->public_key,
            $validators->get(7)->public_key,
            $validators->get(8)->public_key,
        ], [
            $validators->get(4)->public_key,
            $validators->get(5)->public_key,
            $validators->get(6)->public_key,
            $validators->get(7)->public_key,
            $validators->get(8)->public_key,
        ], true, 53);

        expect($height)->toBe((3 * Network::validatorCount()) - 4);

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollValidators');

        $instance = $component->instance();

        $overflowValidators = $instance->getOverflowValidatorsProperty();

        expect($overflowValidators)->toHaveCount(5);
        expect(collect($overflowValidators)->map(fn ($validator) => $validator->status())->toArray())->toBe([
            'next',
            'pending',
            'pending',
            'pending',
            'pending',
        ]);
    });

    it('should show overflow validators for partial round', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [$validators, $round, $height] = createRealisticRound([
            array_fill(0, 53, true),
        ], $this);

        createPartialRound($round, $height, 52, $this, [
            $validators->get(4)->public_key,
            $validators->get(5)->public_key,
            $validators->get(6)->public_key,
            $validators->get(7)->public_key,
            $validators->get(8)->public_key,
        ], [
            $validators->get(4)->public_key,
            $validators->get(5)->public_key,
            $validators->get(6)->public_key,
            $validators->get(7)->public_key,
            $validators->get(8)->public_key,
        ]);

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollValidators');

        $instance = $component->instance();

        $overflowValidators = $instance->getOverflowValidatorsProperty();

        expect($overflowValidators)->toHaveCount(5);
        expect(collect($overflowValidators)->map(fn ($validator) => $validator->status())->toArray())->toBe([
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

        [$validators, $round, $height] = createRealisticRound([
            array_fill(0, 53, true),
        ], $this);

        [$validators, $round, $height] = createPartialRound($round, $height, null, $this, [
            $validators->get(4)->public_key,
            $validators->get(5)->public_key,
            $validators->get(6)->public_key,
            $validators->get(7)->public_key,
            $validators->get(8)->public_key,
        ], [
            $validators->get(4)->public_key,
            $validators->get(5)->public_key,
            $validators->get(6)->public_key,
            $validators->get(7)->public_key,
            $validators->get(8)->public_key,
        ], true, 53);

        $validators = getRoundValidators(false, $round - 1);

        createBlock($height, $validators->get(0)['publicKey'], $this);
        createBlock($height + 1, $validators->get(1)['publicKey'], $this);

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollValidators');

        $instance = $component->instance();

        $overflowValidators = $instance->getOverflowValidatorsProperty();

        expect($overflowValidators)->toHaveCount(5);
        expect(collect($overflowValidators)->map(fn ($validator) => $validator->status())->toArray())->toBe([
            'done',
            'done',
            'next',
            'pending',
            'pending',
        ]);
    });

    it('should handle when an overflow validator misses a block', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [$validators, $round, $height] = createRealisticRound([
            array_fill(0, 53, true),
        ], $this);

        [$validators, $round, $height, $totalMissedSeconds] = createPartialRound($round, $height, null, $this, [
            $validators->get(4)->public_key,
            $validators->get(5)->public_key,
            $validators->get(6)->public_key,
            $validators->get(7)->public_key,
            $validators->get(8)->public_key,
        ], [
            $validators->get(4)->public_key,
            $validators->get(5)->public_key,
            $validators->get(6)->public_key,
            $validators->get(7)->public_key,
            $validators->get(8)->public_key,
        ], true, 53);

        $validators = getRoundValidators(false, $round - 1);

        // Overflow slot 1
        createBlock($height, $validators->get(0)['publicKey'], $this);

        // Overflow slot 2
        $this->travel(Network::blockTime() + 2)->seconds();

        // Overflow slot 3
        createBlock($height + 1, $validators->get(2)['publicKey'], $this);

        $overflowForgeTime = Carbon::parse('2024-02-01 14:00:00Z')->addSeconds((Network::blockTime() * (Network::validatorCount() + 4)) + $totalMissedSeconds + 2);

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollValidators');

        $instance = $component->instance();

        $overflowValidators = $instance->getOverflowValidatorsProperty();

        expect($overflowValidators)->toHaveCount(6);
        expect(collect($overflowValidators)->map(fn ($validator) => $validator->status())->toArray())->toBe([
            'done',
            'done',
            'done',
            'next',
            'pending',
            'pending',
        ]);

        expect(collect($overflowValidators)->map(fn ($validator) => $validator->forgingAt()->format('Y-m-d H:i:s'))->toArray())->toBe([
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

        [$validators, $round, $height] = createRealisticRound([
            array_fill(0, 53, true),
        ], $this);

        [$validators, $round, $height] = createPartialRound($round, $height, null, $this, [
            $validators->get(44)->public_key,
        ], [
            $validators->get(44)->public_key,
        ], true, 49);

        expect($height)->toBe((3 * Network::validatorCount()) - 4);

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollValidators');

        $instance = $component->instance();

        $validatorsProperty = new ReflectionProperty($instance, 'validators');
        $validatorsProperty->setAccessible(true);

        $slots = collect($validatorsProperty->getValue($instance))->groupBy(fn ($validator) => $validator->status());

        expect($slots['done'])->toHaveCount(49);
        expect($slots['pending'])->toHaveCount(3);
        expect($slots['next'])->toHaveCount(1);

        /** @var Slot[] */
        $overflowValidators = $instance->getOverflowValidatorsProperty();

        $overflowForgeTime = Carbon::parse('2024-02-01 14:00:00Z')->addSeconds(Network::blockTime() * Network::validatorCount());

        expect($overflowValidators)->toHaveCount(1);
        expect($overflowValidators[0]->forgingAt()->format('Y-m-d H:i:s'))->toBe($overflowForgeTime->format('Y-m-d H:i:s'));
        expect(collect($overflowValidators)->map(fn ($validator) => $validator->status())->toArray())->toBe([
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

    function createRoundWithValidatorsAndPerformances(array $performances = null, bool $addBlockForNextRound = true, int $walletCount = 53, int $baseIndex = 0): void
    {
        $wallets = Wallet::factory($walletCount)
            ->activeValidator()
            ->create();

        createRoundEntry(112168, 5944904, $wallets);

        $wallets->each(function ($wallet, $index) use ($performances, $addBlockForNextRound, $baseIndex) {
            $timestamp = Carbon::now()->add(($baseIndex + $index) * 8, 'seconds')->timestamp;

            $block = Block::factory()->create([
                'height'            => 5944900,
                'timestamp'         => $timestamp,
                'generator_address' => $wallet->address,
            ]);

            // Start height for round 112168
            if ($addBlockForNextRound) {
                Block::factory()->create([
                    'height'            => 5944904,
                    'timestamp'         => $timestamp,
                    'generator_address' => $wallet->address,
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
                'id'     => $block->id,
                'height' => $block->height->toNumber(),
            ]);
        });
    }

    it('should get the performances of active validators and parse it into a readable array', function () {
        createRoundWithValidatorsAndPerformances();

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollData');

        expect($component->instance()->getValidatorsPerformance())->toBeArray();
        expect($component->instance()->getValidatorsPerformance())->toHaveKeys(['forging', 'missed', 'missing']);
    });

    it('should determine if validators are forging based on their round history', function () {
        createRoundWithValidatorsAndPerformances([true, true]);

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollData');

        $validatorWallet = Wallet::first();
        $validator       = new WalletViewModel($validatorWallet);

        expect($component->instance()->getValidatorPerformance($validator->publicKey()))->toBe(ValidatorForgingStatus::forging);
    });

    it('should determine if validators are not forging based on their round history', function () {
        [$validators] = createRealisticRound([
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

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollData');

        $validatorWallet = $validators->get(4);
        $validator       = new WalletViewModel($validatorWallet);

        expect($validator->performance())->toBe([false, false]);

        expect($component->instance()->getValidatorPerformance($validator->publicKey()))->toBe(ValidatorForgingStatus::missing);
    });

    it('should determine if validators are forging after missing 4 slots based on their round history', function () {
        createRoundWithValidatorsAndPerformances([false, true]);

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollData');

        $validatorWallet = Wallet::first();
        $validator       = new WalletViewModel($validatorWallet);

        expect($component->instance()->getValidatorPerformance($validator->publicKey()))->toBe(ValidatorForgingStatus::forging);
    });

    it('should return the block count', function () {
        createRoundWithValidatorsAndPerformances();

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollData');

        expect($component->instance()->getBlockCount())->toBeString();
    });

    it('should return the next validator', function () {
        [$validators, $round, $height] = createRealisticRound([array_fill(0, 53, true)], $this);

        createPartialRound($round, $height, 12, $this);

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollData');

        expect($component->instance()->getNextValidator())->toBeInstanceOf(WalletViewModel::class);
    });

    it('should return the next validator when in overflow', function () {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [$validators, $round, $height] = createRealisticRound([
            array_fill(0, 53, true),
        ], $this);

        [$validators, $round, $height] = createPartialRound($round, $height, null, $this, [
            $validators->get(4)->public_key,
            $validators->get(5)->public_key,
            $validators->get(6)->public_key,
            $validators->get(7)->public_key,
            $validators->get(8)->public_key,
        ], [
            $validators->get(4)->public_key,
            $validators->get(5)->public_key,
            $validators->get(6)->public_key,
            $validators->get(7)->public_key,
            $validators->get(8)->public_key,
        ], true, 53);

        $validators = getRoundValidators(false, $round - 1);

        // Overflow slot 1
        createBlock($height, $validators->get(0)['publicKey'], $this);

        // Overflow slot 2
        $this->travel(Network::blockTime() + 2)->seconds();

        // Overflow slot 3
        createBlock($height + 1, $validators->get(2)['publicKey'], $this);

        $component = Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollValidators');

        $instance = $component->instance();

        expect($instance->getNextValidator())->toBeInstanceOf(WalletViewModel::class);
        expect($instance->getNextValidator()->publicKey())->toBe($validators->get(4)['publicKey']);
    });

    it('should not error if no cached validator data', function () {
        $wallets = Wallet::factory(Network::validatorCount())
            ->activeValidator()
            ->create();

        createRoundEntry(112168, 5944904, $wallets);

        $wallets->each(function ($wallet) {
            Block::factory()->create([
                'height'            => 5944900,
                'timestamp'         => 113620904,
                'generator_address' => $wallet->address,
            ]);

            Block::factory()->create([
                'height'            => 5944904,
                'timestamp'         => 113620904,
                'generator_address' => $wallet->address,
            ]);
        });

        foreach ($wallets as $wallet) {
            expect((new WalletCache())->getValidator($wallet->address))->toBeNull();
        }

        Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->assertSeeHtml('rounded-sm-md animate-pulse bg-theme-secondary-300 dark:bg-theme-dark-800 w-[70px] h-5')
            ->assertSet('statistics.nextValidator', null);
    });

    it('should defer loading', function () {
        createRealisticRound([array_fill(0, 53, true)], $this);

        Livewire::test(Monitor::class)
            ->call('pollData')
            ->assertViewHas('height', 0)
            ->assertViewHas('statistics', [])
            ->assertDontSee('106')
            ->call('setIsReady')
            ->assertDontSee('106')
            ->call('pollData')
            ->assertViewHas('height', 53 * 2)
            ->assertSee('106');
    });

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
            $validatorIndex  = $validatorsOrder->search(fn ($validator) => $validator['publicKey'] === $validators->get(4)->public_key);
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

        createPartialRound($round, $height, 51, $this, [], [$validators->get(4)->public_key]);

        expect((new WalletViewModel($validators->get(4)))->performance())->toBe([false, true]);

        Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollData')
            ->assertSeeHtmlInOrder([
                'Forging',
                '<span>53</span>',
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

        Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollData')
            ->assertSeeHtmlInOrder([
                'Forging',
                '<span>53</span>',
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

        [$validators, $round, $height] = createRealisticRound([
            array_fill(0, 53, true),
            array_fill(0, 53, true),
            array_fill(0, 53, true),
        ], $this);

        for ($i = 0; $i < 3; $i++) {
            createRoundEntry($round, $height, $validators);
            $validatorsOrder = getRoundValidators(false, $round);
            $validatorIndex  = $validatorsOrder->search(fn ($validator) => $validator['publicKey'] === $validators->get(4)->public_key);
            if ($validatorIndex < 51) {
                break;
            }

            [$validators, $round, $height] = createRealisticRound([
                array_fill(0, 53, true),
            ], $this);
        }

        createPartialRound($round, $height, 51, $this, [$validators->get(4)->public_key], [$validators->get(4)->public_key]);

        expect((new WalletViewModel($validators->get(4)))->performance())->toBe([true, false]);

        Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollData')
            ->assertSeeHtmlInOrder([
                'Forging',
                '<span>52</span>',
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

        Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollData')
            ->assertSeeHtmlInOrder([
                'Forging',
                '<span>52</span>',
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
            $validatorIndex  = $validatorsOrder->search(fn ($validator) => $validator['publicKey'] === $validators->get(4)->public_key);
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

        createPartialRound($round, $height, 51, $this, [$validators->get(4)->public_key], [$validators->get(4)->public_key]);

        expect((new WalletViewModel($validators->get(4)))->performance())->toBe([false, false]);

        Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollData')
            ->assertSeeHtmlInOrder([
                'Forging',
                '<span>52</span>',
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

        Livewire::test(Monitor::class)
            ->call('setIsReady')
            ->call('pollData')
            ->assertSeeHtmlInOrder([
                'Forging',
                '<span>52</span>',
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
            ->assertDontSeeHtml('<span>52</span>')
            ->assertDontSeeHtml('<span>0</span>')
            ->assertDontSeeHtml('<span>1</span>')
            ->dispatch('echo:blocks,NewBlock')
            ->assertSeeHtmlInOrder([
                'Forging',
                '<span>52</span>',
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
            ->assertDontSeeHtml('<span>52</span>')
            ->assertDontSeeHtml('<span>0</span>')
            ->assertDontSeeHtml('<span>1</span>')
            ->dispatch('monitorIsReady')
            ->assertSeeHtmlInOrder([
                'Forging',
                '<span>52</span>',
                'Missed',
                '<span>0</span>',
                'Not Forging',
                '<span>1</span>',
                'Current Height',
            ]);
    });
});
