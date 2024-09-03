<?php

declare(strict_types=1);

use App\DTO\Slot;
use App\Facades\Network;
use App\Http\Livewire\Validators\Monitor;
use App\Models\Block;
use App\Models\Wallet;
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

beforeEach(function () {
    $this->activeValidators = require dirname(dirname(dirname(dirname(__DIR__)))).'/fixtures/forgers.php';
});

function createRoundWithValidators(): void
{
    $wallets = Wallet::factory(Network::validatorCount())->create();

    createRoundEntry(112168, 5944904, $wallets);

    $wallets->each(function ($wallet) {
        $block = Block::factory()->create([
            'height'               => 5944900,
            'timestamp'            => 113620904,
            'generator_public_key' => $wallet->public_key,
        ]);

        // Start height for round 112168
        Block::factory()->create([
            'height'               => 5944904,
            'timestamp'            => 113620904,
            'generator_public_key' => $wallet->public_key,
        ]);

        (new WalletCache())->setValidator($wallet->public_key, $wallet);

        (new WalletCache())->setLastBlock($wallet->public_key, [
            'id'     => $block->id,
            'height' => $block->height->toNumber(),
        ]);
    });
}

function forgeBlock(string $publicKey, int $height): void
{
    $block = createBlock($height, $publicKey);

    (new WalletCache())->setLastBlock($publicKey, [
        'id'     => $block->id,
        'height' => $block->height->toNumber(),
    ]);
}

it('should render without errors', function () {
    createRoundWithValidators();

    Livewire::test(Monitor::class)
        ->call('setIsReady')
        ->assertSeeHtml('pollValidators');
});

it('should throw an exception after 3 tries', function () {
    createRoundWithValidators();

    $this->expectExceptionMessage('Something went wrong!');

    Cache::shouldReceive('remember')
        ->andThrow(new Exception('Something went wrong!'))
        ->shouldReceive('increment')
        ->andReturn(1, 2, 3);

    Livewire::test(Monitor::class)
        ->call('setIsReady')
        ->call('pollValidators');
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
                'height'               => $i,
                'generator_public_key' => $wallet->public_key,
            ]);
        }

        (new WalletCache())->setValidator($wallet->public_key, $wallet);
    });

    $wallets->first()->blocks()->delete();

    Livewire::test(Monitor::class)
        ->call('setIsReady')
        ->call('pollValidators');

    expect((new WalletCache())->getLastBlock($wallets->first()->public_key))->toBe([]);

    foreach ($wallets->skip(1) as $wallet) {
        expect((new WalletCache())->getLastBlock($wallet->public_key))->not()->toBe([]);
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
        ->emit('monitorIsReady')
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
            $validator->username(),
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
            $validator->username(),
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
            $validator->username(),
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
