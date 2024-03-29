<?php

declare(strict_types=1);

use App\Enums\ValidatorForgingStatus;
use App\Facades\Network;
use App\Http\Livewire\ValidatorDataBoxes;
use App\Models\Block;
use App\Models\Round;
use App\Models\Wallet;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\WalletCache;
use App\ViewModels\WalletViewModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;
use function Tests\createPartialRound;
use function Tests\createRealisticRound;
use function Tests\createRoundEntry;

use Tests\Stubs\FullPartialRoundException;

beforeEach(function () {
    $this->travelTo(Carbon::parse('2022-08-22 00:00'));
});

function createRoundWithValidatorsAndPerformances(array $performances = null, bool $addBlockForNextRound = true, int $walletCount = 53, int $baseIndex = 0): void
{
    $wallets = Wallet::factory($walletCount)
        ->create();

    createRoundEntry(112168, 5720518, $wallets);

    $wallets->each(function ($wallet, $index) use ($performances, $addBlockForNextRound, $baseIndex) {
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

    expect($component->instance()->getValidatorPerformance($validator->publicKey()))->toBeString();
    expect($component->instance()->getValidatorPerformance($validator->publicKey()))->toBe(ValidatorForgingStatus::forging);
});

it('should determine if validators are not forging based on their round history', function () {
    createRoundWithValidatorsAndPerformances([false, false]);

    $component = Livewire::test(ValidatorDataBoxes::class)
        ->call('setIsReady');

    $component->call('pollStatistics');

    $validatorWallet = Wallet::first();
    $validator       = new WalletViewModel($validatorWallet);

    expect($component->instance()->getValidatorPerformance($validator->publicKey()))->toBeString();
    expect($component->instance()->getValidatorPerformance($validator->publicKey()))->toBe(ValidatorForgingStatus::missing);
});

it('should determine if validators just missed based on their round history', function () {
    createRoundWithValidatorsAndPerformances([true, false]);

    $component = Livewire::test(ValidatorDataBoxes::class)
        ->call('setIsReady');

    $component->call('pollStatistics');

    $validatorWallet = Wallet::first();
    $validator       = new WalletViewModel($validatorWallet);

    expect($component->instance()->getValidatorPerformance($validator->publicKey()))->toBeString();
    expect($component->instance()->getValidatorPerformance($validator->publicKey()))->toBe(ValidatorForgingStatus::missed);
});

it('should determine if validators are forging after missing 4 slots based on their round history', function () {
    createRoundWithValidatorsAndPerformances([false, true]);

    $component = Livewire::test(ValidatorDataBoxes::class)
        ->call('setIsReady');

    $component->call('pollStatistics');

    $validatorWallet = Wallet::first();
    $validator       = new WalletViewModel($validatorWallet);

    expect($component->instance()->getValidatorPerformance($validator->publicKey()))->toBeString();
    expect($component->instance()->getValidatorPerformance($validator->publicKey()))->toBe(ValidatorForgingStatus::forging);
});

it('should return the block count', function () {
    createRoundWithValidatorsAndPerformances();

    $component = Livewire::test(ValidatorDataBoxes::class)
        ->call('setIsReady');

    expect($component->instance()->getBlockCount())->toBeString();
});

it('should return the next validator', function () {
    createRoundWithValidatorsAndPerformances();

    $component = Livewire::test(ValidatorDataBoxes::class)
        ->call('setIsReady');

    expect($component->instance()->getNextvalidator())->toBeInstanceOf(WalletViewModel::class);
});

it('should not error if no cached validator data', function () {
    $wallets = Wallet::factory(Network::validatorCount())
        ->activeValidator()
        ->create();

    createRoundEntry(112168, 5720518, $wallets);

    $wallets->each(function ($wallet) {
        Block::factory()->create([
            'height'               => 5720529,
            'timestamp'            => 113620904,
            'generator_public_key' => $wallet->public_key,
        ]);

        Block::factory()->create([
            'height'               => 5720518,
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
    createRoundWithValidatorsAndPerformances();

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

    try {
        [$validators, $round, $height] = createRealisticRound([
            array_fill(0, 53, true),
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 48, true),
            ],
        ], $this);

        createPartialRound($round, $height, 49, $this, null, $validators->get(4)->public_key);
    } catch (FullPartialRoundException) {
        Artisan::call('cache:clear');

        [$validators, $round, $height] = createRealisticRound([
            array_fill(0, 53, true),
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 48, true),
            ],
        ], $this);

        createPartialRound($round, $height, 49, $this, null, $validators->get(4)->public_key);
    }

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

it('should calculate missed correctly with current round', function () {
    $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

    $this->freezeTime();

    try {
        [$validators, $round, $height] = createRealisticRound([
            array_fill(0, 53, true),
            array_fill(0, 53, true),
            array_fill(0, 53, true),
        ], $this);

        createPartialRound($round, $height, 49, $this, $validators->get(4)->public_key, $validators->get(4)->public_key);
    } catch (FullPartialRoundException) {
        Artisan::call('cache:clear');

        [$validators, $round, $height] = createRealisticRound([
            array_fill(0, 53, true),
            array_fill(0, 53, true),
            array_fill(0, 53, true),
        ], $this);

        createPartialRound($round, $height, 49, $this, $validators->get(4)->public_key, $validators->get(4)->public_key);
    }

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
        array_fill(0, 53, true),
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

    try {
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

        createPartialRound($round, $height, 49, $this, $validators->get(4)->public_key, $validators->get(4)->public_key);
    } catch (FullPartialRoundException) {
        Artisan::call('cache:clear');

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

        createPartialRound($round, $height, 49, $this, $validators->get(4)->public_key, $validators->get(4)->public_key);
    }

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
