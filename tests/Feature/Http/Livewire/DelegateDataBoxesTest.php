<?php

declare(strict_types=1);

use App\Console\Commands\CacheDelegatePerformance;
use App\Enums\DelegateForgingStatus;
use App\Facades\Rounds;
use App\Http\Livewire\DelegateDataBoxes;
use App\Models\Block;
use App\Models\Round;
use App\Models\Wallet;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\WalletCache;
use App\Services\Timestamp;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Livewire\Livewire;

use function Tests\createRealisticRound;

beforeEach(function () {
    $this->travelTo(Carbon::parse('2022-08-22 00:00'));
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

    $component = Livewire::test(DelegateDataBoxes::class)
        ->call('setIsReady');

    $component->call('pollStatistics');

    $component->assertHasNoErrors();
    $component->assertViewIs('livewire.delegate-data-boxes');
});

it('should handle case no block yet', function () {
    createRoundWithDelegatesAndPerformances(null, false);

    $component = Livewire::test(DelegateDataBoxes::class)
        ->call('setIsReady');

    $component->call('pollStatistics');

    $component->assertHasNoErrors();

    $component->assertViewIs('livewire.delegate-data-boxes');
});

it('should get the performances of active delegates and parse it into a readable array', function () {
    createRoundWithDelegatesAndPerformances();

    $component = Livewire::test(DelegateDataBoxes::class)
        ->call('setIsReady');

    $component->call('pollStatistics');

    expect($component->instance()->getDelegatesPerformance())->toBeArray();
    expect($component->instance()->getDelegatesPerformance())->toHaveKeys(['forging', 'missed', 'missing']);
});

it('should determine if delegates are forging based on their round history', function () {
    createRoundWithDelegatesAndPerformances([true, true]);

    $component = Livewire::test(DelegateDataBoxes::class)
        ->call('setIsReady');

    $component->call('pollStatistics');

    $delegateWallet = Wallet::first();
    $delegate       = new WalletViewModel($delegateWallet);

    expect($component->instance()->getDelegatePerformance($delegate->publicKey()))->toBeString();
    expect($component->instance()->getDelegatePerformance($delegate->publicKey()))->toBe(DelegateForgingStatus::forging);
});

it('should determine if delegates are not forging based on their round history', function () {
    createRoundWithDelegatesAndPerformances([false, false]);

    $component = Livewire::test(DelegateDataBoxes::class)
        ->call('setIsReady');

    $component->call('pollStatistics');

    $delegateWallet = Wallet::first();
    $delegate       = new WalletViewModel($delegateWallet);

    expect($component->instance()->getDelegatePerformance($delegate->publicKey()))->toBeString();
    expect($component->instance()->getDelegatePerformance($delegate->publicKey()))->toBe(DelegateForgingStatus::missing);
});

it('should determine if delegates just missed based on their round history', function () {
    createRoundWithDelegatesAndPerformances([true, false]);

    $component = Livewire::test(DelegateDataBoxes::class)
        ->call('setIsReady');

    $component->call('pollStatistics');

    $delegateWallet = Wallet::first();
    $delegate       = new WalletViewModel($delegateWallet);

    expect($component->instance()->getDelegatePerformance($delegate->publicKey()))->toBeString();
    expect($component->instance()->getDelegatePerformance($delegate->publicKey()))->toBe(DelegateForgingStatus::missed);
});

it('should determine if delegates are forging after missing 4 slots based on their round history', function () {
    createRoundWithDelegatesAndPerformances([false, true]);

    $component = Livewire::test(DelegateDataBoxes::class)
        ->call('setIsReady');

    $component->call('pollStatistics');

    $delegateWallet = Wallet::first();
    $delegate       = new WalletViewModel($delegateWallet);

    expect($component->instance()->getDelegatePerformance($delegate->publicKey()))->toBeString();
    expect($component->instance()->getDelegatePerformance($delegate->publicKey()))->toBe(DelegateForgingStatus::forging);
});

it('should return the block count', function () {
    createRoundWithDelegatesAndPerformances();

    $component = Livewire::test(DelegateDataBoxes::class)
        ->call('setIsReady');

    expect($component->instance()->getBlockCount())->toBeString();
});

it('should return the next delegate', function () {
    createRoundWithDelegatesAndPerformances();

    $component = Livewire::test(DelegateDataBoxes::class)
        ->call('setIsReady');

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

    Livewire::test(DelegateDataBoxes::class)
        ->call('setIsReady')
        ->assertSeeHtml('rounded-sm-md animate-pulse bg-theme-secondary-300 dark:bg-theme-dark-800 w-[70px] h-5')
        ->assertSet('statistics.nextDelegate', null);
});

it('should defer loading', function () {
    createRoundWithDelegatesAndPerformances();

    (new NetworkCache())->setHeight(fn (): int => 4234212);

    Livewire::test(DelegateDataBoxes::class)
        ->call('pollStatistics')
        ->assertViewHas('height', 4234212)
        ->assertViewHas('statistics', [])
        ->assertDontSee('4,234,212')
        ->call('setIsReady')
        ->assertDontSee('4,234,212')
        ->call('pollStatistics')
        ->assertSee('4,234,212');
});

// it('should calculate forging correctly', function ($performances, $addBlockForNextRound) {
//     createRoundWithDelegatesAndPerformances([true, true], true, 50);
//     createRoundWithDelegatesAndPerformances($performances, $addBlockForNextRound, 1);

//     (new NetworkCache())->setHeight(fn (): int => 4234212);

//     Livewire::test(DelegateDataBoxes::class)
//         ->call('setIsReady')
//         ->call('pollStatistics')
//         ->assertSeeInOrder([
//             'Forging',
//             51,
//             'Missed',
//             0,
//             'Not Forging',
//             0,
//             'Current Height',
//         ]);
// })->with([
//     'forged, forged, forged' => [
//         [true, true],
//         true,
//     ],
//     'missed, forged, forged' => [
//         [false, true],
//         true,
//     ],
//     'missed, missed, forged' => [
//         [false, false],
//         true,
//     ],
// ]);

it('should calculate forging correctly with current round', function () {
    $this->freezeTime();

    $delegates = createRealisticRound([
        array_fill(0, 51, true),
        [
            ...array_fill(0, 4, true),
            false,
            ...array_fill(0, 3, true),
        ]
    ], $this, array_fill(0, 20, true));

    expect((new WalletViewModel($delegates->get(4)))->hasForged())->toBeTrue();

    Livewire::test(DelegateDataBoxes::class)
        ->call('setIsReady')
        ->call('pollStatistics')
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

it('should calculate forging correctly for previous rounds', function () {
    $this->freezeTime();

    $delegates = createRealisticRound([
        [
            ...array_fill(0, 4, true),
            false,
            ...array_fill(0, 46, true),
        ],
        array_fill(0, 51, true),
        array_fill(0, 51, true),
    ], $this);

    expect((new WalletViewModel($delegates->get(4)))->hasForged())->toBeTrue();

    Livewire::test(DelegateDataBoxes::class)
        ->call('setIsReady')
        ->call('pollStatistics')
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
    $this->freezeTime();

    $delegates = createRealisticRound([
        array_fill(0, 51, true),
        array_fill(0, 51, true),
        array_fill(0, 51, true),
    ], $this, [
        ...array_fill(0, 4, true),
        false,
        ...array_fill(0, 3, true),
    ]);

    expect((new WalletViewModel($delegates->get(4)))->justMissed())->toBeTrue();

    Livewire::test(DelegateDataBoxes::class)
        ->call('setIsReady')
        ->call('pollStatistics')
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
    $this->freezeTime();

    $delegates = createRealisticRound([
        array_fill(0, 51, true),
        array_fill(0, 51, true),
        [
            ...array_fill(0, 4, true),
            false,
            ...array_fill(0, 46, true),
        ],
        array_fill(0, 51, true),
    ], $this);

    expect((new WalletViewModel($delegates->get(4)))->justMissed())->toBeTrue();

    Livewire::test(DelegateDataBoxes::class)
        ->call('setIsReady')
        ->call('pollStatistics')
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
    $this->freezeTime();

    $delegates = createRealisticRound([
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
    ], $this, [
        ...array_fill(0, 4, true),
        false,
        ...array_fill(0, 3, true),
    ]);

    expect((new WalletViewModel($delegates->get(4)))->keepsMissing())->toBeTrue();

    Livewire::test(DelegateDataBoxes::class)
        ->call('setIsReady')
        ->call('pollStatistics')
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
    $this->freezeTime();

    $delegates = createRealisticRound([
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

    expect((new WalletViewModel($delegates->get(4)))->keepsMissing())->toBeTrue();

    Livewire::test(DelegateDataBoxes::class)
        ->call('setIsReady')
        ->call('pollStatistics')
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
