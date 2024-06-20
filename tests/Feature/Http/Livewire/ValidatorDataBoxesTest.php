<?php

declare(strict_types=1);

use App\Contracts\RoundRepository as ContractsRoundRepository;
use App\Enums\ValidatorForgingStatus;
use App\Facades\Network;
use App\Http\Livewire\ValidatorDataBoxes;
use App\Models\Block;
use App\Models\Wallet;
use App\Repositories\RoundRepository;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\WalletCache;
use App\ViewModels\WalletViewModel;
use Carbon\Carbon;
use Livewire\Livewire;
use function Tests\createPartialRound;
use function Tests\createRealisticRound;
use function Tests\createRoundEntry;
use function Tests\getRoundValidators;

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
            'height'               => 5944900,
            'timestamp'            => $timestamp,
            'generator_public_key' => $wallet->public_key,
        ]);

        // Start height for round 112168
        if ($addBlockForNextRound) {
            Block::factory()->create([
                'height'               => 5944904,
                'timestamp'            => $timestamp,
                'generator_public_key' => $wallet->public_key,
            ]);
        }

        (new WalletCache())->setValidator($wallet->public_key, $wallet);

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
    createRoundWithValidatorsAndPerformances();

    $component = Livewire::test(ValidatorDataBoxes::class)
        ->call('setIsReady');

    $component->call('pollStatistics');

    $component->assertHasNoErrors();
    $component->assertViewIs('livewire.validator-data-boxes');
});

it('should handle case no block yet', function () {
    createRoundWithValidatorsAndPerformances(null, false);

    $component = Livewire::test(ValidatorDataBoxes::class)
        ->call('setIsReady');

    $component->call('pollStatistics');

    $component->assertHasNoErrors();

    $component->assertViewIs('livewire.validator-data-boxes');
});

it('should get the performances of active validators and parse it into a readable array', function () {
    createRoundWithValidatorsAndPerformances();

    $component = Livewire::test(ValidatorDataBoxes::class)
        ->call('setIsReady');

    $component->call('pollStatistics');

    expect($component->instance()->getValidatorsPerformance())->toBeArray();
    expect($component->instance()->getValidatorsPerformance())->toHaveKeys(['forging', 'missed', 'missing']);
});

it('should determine if validators are forging based on their round history', function () {
    createRoundWithValidatorsAndPerformances([true, true]);

    $component = Livewire::test(ValidatorDataBoxes::class)
        ->call('setIsReady');

    $component->call('pollStatistics');

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

    $component = Livewire::test(ValidatorDataBoxes::class)
        ->call('setIsReady');

    $component->call('pollStatistics');

    $validatorWallet = $validators->get(4);
    $validator       = new WalletViewModel($validatorWallet);

    expect($validator->performance())->toBe([false, false]);

    expect($component->instance()->getValidatorPerformance($validator->publicKey()))->toBe(ValidatorForgingStatus::missing);
});

it('should determine if validators are forging after missing 4 slots based on their round history', function () {
    createRoundWithValidatorsAndPerformances([false, true]);

    $component = Livewire::test(ValidatorDataBoxes::class)
        ->call('setIsReady');

    $component->call('pollStatistics');

    $validatorWallet = Wallet::first();
    $validator       = new WalletViewModel($validatorWallet);

    expect($component->instance()->getValidatorPerformance($validator->publicKey()))->toBe(ValidatorForgingStatus::forging);
});

it('should return the block count', function () {
    createRoundWithValidatorsAndPerformances();

    $component = Livewire::test(ValidatorDataBoxes::class)
        ->call('setIsReady');

    expect($component->instance()->getBlockCount())->toBeString();
});

it('should return the next validator', function () {
    [$validators, $round, $height] = createRealisticRound([array_fill(0, 53, true)], $this);

    createPartialRound($round, $height, 12, $this);

    $component = Livewire::test(ValidatorDataBoxes::class)
        ->call('setIsReady');

    expect($component->instance()->getNextValidator())->toBeInstanceOf(WalletViewModel::class);
});

it('should not error if no cached validator data', function () {
    $wallets = Wallet::factory(Network::validatorCount())
        ->activeValidator()
        ->create();

    createRoundEntry(112168, 5944904, $wallets);

    $wallets->each(function ($wallet) {
        Block::factory()->create([
            'height'               => 5944900,
            'timestamp'            => 113620904,
            'generator_public_key' => $wallet->public_key,
        ]);

        Block::factory()->create([
            'height'               => 5944904,
            'timestamp'            => 113620904,
            'generator_public_key' => $wallet->public_key,
        ]);
    });

    foreach ($wallets as $wallet) {
        expect((new WalletCache())->getValidator($wallet->public_key))->toBeNull();
    }

    Livewire::test(ValidatorDataBoxes::class)
        ->call('setIsReady')
        ->assertSeeHtml('rounded-sm-md animate-pulse bg-theme-secondary-300 dark:bg-theme-dark-800 w-[70px] h-5')
        ->assertSet('statistics.nextValidator', null);
});

it('should defer loading', function () {
    createRealisticRound([array_fill(0, 53, true)], $this);

    Livewire::test(ValidatorDataBoxes::class)
        ->call('pollStatistics')
        ->assertViewHas('height', 53 * 2)
        ->assertViewHas('statistics', [])
        ->assertDontSee('106')
        ->call('setIsReady')
        ->assertDontSee('106')
        ->call('pollStatistics')
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

    Livewire::test(ValidatorDataBoxes::class)
        ->call('setIsReady')
        ->call('pollStatistics')
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

    Livewire::test(ValidatorDataBoxes::class)
        ->call('setIsReady')
        ->call('pollStatistics')
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

    Livewire::test(ValidatorDataBoxes::class)
        ->call('setIsReady')
        ->call('pollStatistics')
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

    Livewire::test(ValidatorDataBoxes::class)
        ->call('setIsReady')
        ->call('pollStatistics')
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

    Livewire::test(ValidatorDataBoxes::class)
        ->call('setIsReady')
        ->call('pollStatistics')
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

    Livewire::test(ValidatorDataBoxes::class)
        ->call('setIsReady')
        ->call('pollStatistics')
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
