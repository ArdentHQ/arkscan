<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Facades\Rounds;
use App\Http\Livewire\Validators\Monitor;
use App\Models\Block;
use App\Models\Round;
use App\Models\Wallet;
use App\Services\Cache\WalletCache;
use App\Services\Monitor\ForgingInfoCalculator;
use App\Services\Monitor\Slots;
use App\Services\Monitor\ValidatorTracker;
use App\ViewModels\WalletViewModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use function Tests\createRealisticRound;
use function Tests\createRoundEntry;

beforeEach(function () {
    $this->activeValidators = require dirname(dirname(dirname(dirname(__DIR__)))).'/fixtures/forgers.php';
});

function createRoundWithValidators(): void
{
    $wallets = Wallet::factory(Network::validatorCount())->create();

    createRoundEntry(112168, 5720518, $wallets);

    $wallets->each(function ($wallet) {
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

        (new WalletCache())->setValidator($wallet->public_key, $wallet);

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

it('should correctly show the block is missed', function () {
    // Force round time
    $this->travelTo(new Carbon('2021-01-01 00:04:00'));

    createRoundEntry(1, 1, $this->activeValidators);

    // Create wallets for each validator
    $this->activeValidators->each(function ($validator) use (&$wallets) {
        $wallet = Wallet::factory()->create(['public_key' => $validator->public_key]);

        (new WalletCache())->setValidator($validator->public_key, $wallet);
    });

    // Store validator record for each Round object
    $wallets = Rounds::byRound(1)->validators;

    // Make methods public for fetching forging order
    $activeValidatorsMethod  = new ReflectionMethod(ValidatorTracker::class, 'getActiveValidators');
    $shuffleValidatorsMethod = new ReflectionMethod(ValidatorTracker::class, 'shuffleValidators');
    $orderValidatorsMethod   = new ReflectionMethod(ValidatorTracker::class, 'orderValidators');
    $activeValidatorsMethod->setAccessible(true);
    $shuffleValidatorsMethod->setAccessible(true);
    $orderValidatorsMethod->setAccessible(true);

    // Get validator order so we can forge in the correct order
    $originalOrder      = ForgingInfoCalculator::calculate(1, 1);
    $activeValidators   = $activeValidatorsMethod->invokeArgs(null, [$wallets]);
    $shuffledValidators = $shuffleValidatorsMethod->invokeArgs(null, [$activeValidators, 1]);
    $validatorsInOrder  = collect($orderValidatorsMethod->invokeArgs(null, [
        $shuffledValidators,
        $originalOrder['currentForger'],
        51,
    ]));

    // Forge blocks for first 5 validators
    $height = 1;
    $validatorsInOrder->take(5)->each(function ($publicKey) use (&$height) {
        forgeBlock($publicKey, $height);

        $this->travel(8)->seconds();
    });

    // Mark component validator property as public & update monitor data
    $validatorProperty = new ReflectionProperty(Monitor::class, 'validators');
    $validatorProperty->setAccessible(true);

    $component = Livewire::test(Monitor::class);

    expect($validatorProperty->getValue($component->instance()))->toBe([]);

    $component->call('setIsReady');

    $instance  = $component->instance();
    $instance->pollValidators();

    $validators = collect($validatorProperty->getValue($instance));

    expect($validators)->toHaveCount(51);

    // Split up validator slot data to check
    $forgedValidators  = $validators->splice(0, 5);
    $waitingValidators = $validators->splice(0, 1);
    $missedValidators  = $validators->splice(0, 5);

    $forgedValidators->each(fn ($validator) => expect($validator->hasForged())->toBeTrue());
    $waitingValidators->each(fn ($validator) => expect($validator->isNext())->toBeTrue());
    $missedValidators->each(fn ($validator) => expect($validator->isPending())->toBeTrue());

    // Progress time by 15 validator slots
    $this->travel(14 * 8)->seconds();

    // Forge block with 20th validator
    forgeBlock($validatorsInOrder->get(20), $height);
    $this->travel(8)->seconds();

    // Update validator data again
    $instance->pollValidators();

    $validators = collect($validatorProperty->getValue($instance));

    expect($validators)->toHaveCount(51);

    // Check validator data is correct after 15 missed blocks
    $forgedValidators  = $validators->splice(0, 5);
    $missedValidators  = $validators->splice(0, 15);
    $waitingValidators = $validators->splice(0, 1);

    $forgedValidators->each(fn ($validator) => expect($validator->isWaiting())->toBeFalse());
    $forgedValidators->each(fn ($validator) => expect($validator->hasForged())->toBeTrue());
    $missedValidators->each(fn ($validator) => expect($validator->isWaiting())->toBeFalse());
    $missedValidators->each(fn ($validator) => expect($validator->justMissed())->toBeTrue());
    $waitingValidators->each(fn ($validator) => expect($validator->isNext())->toBeTrue());

    $outputData = [];
    $forgedValidators->each(function ($validator) use (&$outputData) {
        $outputData[] = $validator->wallet()->username();
        $outputData[] = 'Completed';
    });
    $missedValidators->each(function ($validator) use (&$outputData) {
        $outputData[] = $validator->wallet()->username();
        $outputData[] = 'Missed';
    });
    $waitingValidators->each(function ($validator) use (&$outputData) {
        $outputData[] = $validator->wallet()->username();
        $outputData[] = 'Now';
    });

    $component
        ->call('pollValidators')
        ->assertSeeInOrder($outputData);
})->skip();

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
            'Validator last forged 207 blocks ago (~ 22 min)',
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
            'Validator last forged 207 blocks ago (~ 1h 29 min)',
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
