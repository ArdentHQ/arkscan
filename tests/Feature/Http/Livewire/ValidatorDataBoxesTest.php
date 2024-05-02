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

it('should return the block count', function () {
    [$validators, $round, $height] = createRealisticRound([
        array_fill(0, 53, true),
    ], $this);

    createPartialRound($round, $height, 2, $this);

    $component = Livewire::test(ValidatorDataBoxes::class)
        ->call('setIsReady');

    expect($component->instance()->getBlockCount())->toBe('2 / 53 Blocks');
});

it('should return no validators if current and previous round has no blocks', function () {
    $wallets = Wallet::factory(Network::validatorCount())
        ->activeValidator()
        ->create();

    createRoundEntry(112168, 112168 * Network::validatorCount(), $wallets);
    createRoundEntry(112169, 112169 * Network::validatorCount(), $wallets);

    (new NetworkCache())->setHeight(fn (): int => 112169 * Network::validatorCount());

    $component = Livewire::test(ValidatorDataBoxes::class)
        ->call('setIsReady');

    expect($component->instance()->getNextValidator())->toBeNull();
});

it('should return the next validator', function () {
    $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

    $this->freezeTime();

    [$validators, $round, $height] = createRealisticRound([
        array_fill(0, 53, true),
    ], $this);

    createPartialRound($round, $height, 2, $this);

    $component = Livewire::test(ValidatorDataBoxes::class)
        ->call('setIsReady');

    expect($component->instance()->getNextValidator()->address())->toBe($validators->get(3)->address);
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
    $wallets = Wallet::factory(Network::validatorCount())
        ->activeValidator()
        ->create();

    createRoundEntry(79890, 79890 * Network::validatorCount(), $wallets);

    (new NetworkCache())->setHeight(fn (): int => 4234212);

    Livewire::test(ValidatorDataBoxes::class)
        ->call('pollStatistics')
        ->assertViewHas('height', 4234212)
        ->assertViewHas('statistics', [])
        ->assertDontSee('4,234,212')
        ->call('setIsReady')
        ->assertDontSee('4,234,212')
        ->call('pollStatistics')
        ->assertSee('4,234,212');
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

    $validator = $validators->get(4);

    createPartialRound($round, $height, 51, $this, null, $validator->public_key);

    $component = Livewire::test(ValidatorDataBoxes::class)
        ->call('setIsReady');

    expect($component->instance()->getValidatorsPerformance())->toHaveKeys(['forging', 'missed', 'missing']);
    expect($component->instance()->getValidatorPerformance($validator->public_key))->toBe(ValidatorForgingStatus::forging);

    expect((new WalletViewModel($validator))->performance())->toBe([false, true]);

    $component->call('pollStatistics')
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

    $validator = $validators->get(4);

    $component = Livewire::test(ValidatorDataBoxes::class)
        ->call('setIsReady');

    expect($component->instance()->getValidatorsPerformance())->toHaveKeys(['forging', 'missed', 'missing']);
    expect($component->instance()->getValidatorPerformance($validator->public_key))->toBe(ValidatorForgingStatus::forging);

    expect((new WalletViewModel($validator))->performance())->toBe([true, true]);

    $component->call('pollStatistics')
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

    $validator = $validators->get(4);

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

    createPartialRound($round, $height, 51, $this, $validator->public_key, $validator->public_key);

    $component = Livewire::test(ValidatorDataBoxes::class)
        ->call('setIsReady');

    expect($component->instance()->getValidatorsPerformance())->toHaveKeys(['forging', 'missed', 'missing']);
    expect($component->instance()->getValidatorPerformance($validator->public_key))->toBe(ValidatorForgingStatus::missed);

    expect((new WalletViewModel($validator))->performance())->toBe([true, false]);

    $component->call('pollStatistics')
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

    $validator = $validators->get(4);

    $component = Livewire::test(ValidatorDataBoxes::class)
        ->call('setIsReady');

    expect($component->instance()->getValidatorsPerformance())->toHaveKeys(['forging', 'missed', 'missing']);
    expect($component->instance()->getValidatorPerformance($validator->public_key))->toBe(ValidatorForgingStatus::missed);

    expect((new WalletViewModel($validator))->performance())->toBe([true, false]);

    $component->call('pollStatistics')
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

    $validator = $validators->get(4);

    createPartialRound($round, $height, 51, $this, $validator->public_key, $validator->public_key);

    $component = Livewire::test(ValidatorDataBoxes::class)
        ->call('setIsReady');

    expect($component->instance()->getValidatorsPerformance())->toHaveKeys(['forging', 'missed', 'missing']);
    expect($component->instance()->getValidatorPerformance($validator->public_key))->toBe(ValidatorForgingStatus::missing);

    expect((new WalletViewModel($validator))->performance())->toBe([false, false]);

    $component->call('pollStatistics')
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

    $validator = $validators->get(4);

    $component = Livewire::test(ValidatorDataBoxes::class)
        ->call('setIsReady');

    expect($component->instance()->getValidatorsPerformance())->toHaveKeys(['forging', 'missed', 'missing']);
    expect($component->instance()->getValidatorPerformance($validator->public_key))->toBe(ValidatorForgingStatus::missing);

    expect((new WalletViewModel($validator))->performance())->toBe([false, false]);

    $component->call('pollStatistics')
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
