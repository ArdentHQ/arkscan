<?php

declare(strict_types=1);

use App\Enums\DelegateForgingStatus;
use App\Http\Livewire\DelegateDataBoxes;
use App\Models\Block;
use App\Models\Round;
use App\Models\Wallet;
use App\Services\Cache\WalletCache;
use App\ViewModels\WalletViewModel;
use Livewire\Livewire;
use function Tests\configureExplorerDatabase;

function createRoundWithDelegatesAndPerformances(array $performances = null): void
{
    Wallet::factory(51)->create()->each(function ($wallet) use ($performances) {
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

        if (is_null($performances)) {
            for ($i = 0; $i < 5; $i++) {
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

beforeEach(fn () => configureExplorerDatabase());

it('should render without errors', function () {
    createRoundWithDelegatesAndPerformances();

    $component = Livewire::test(DelegateDataBoxes::class);

    $component->call('pollStatistics');

    $component->assertHasNoErrors();
    $component->assertViewIs('livewire.delegate-data-boxes');
});

it('should get the performances of active delegates and parse it into a readable array', function () {
    createRoundWithDelegatesAndPerformances();

    $component = Livewire::test(DelegateDataBoxes::class);

    $component->call('pollStatistics');

    expect($component->instance()->getDelegatesPerformance())->toBeArray();
    expect($component->instance()->getDelegatesPerformance())->toHaveKeys(['forging', 'missed', 'missing']);
});

it('should determine if delegates are forging based on their round history', function () {
    createRoundWithDelegatesAndPerformances([true, true, true, true, true]);

    $component = Livewire::test(DelegateDataBoxes::class);

    $component->call('pollStatistics');

    $delegateWallet = Wallet::first();
    $delegate = new WalletViewModel($delegateWallet);

    expect($component->instance()->getDelegatePerformance($delegate->publicKey()))->toBeString();
    expect($component->instance()->getDelegatePerformance($delegate->publicKey()))->toBe(DelegateForgingStatus::forging);
});

it('should determine if delegates are not forging based on their round history', function () {
    createRoundWithDelegatesAndPerformances([false, false, false, false, false]);

    $component = Livewire::test(DelegateDataBoxes::class);

    $component->call('pollStatistics');

    $delegateWallet = Wallet::first();
    $delegate = new WalletViewModel($delegateWallet);

    expect($component->instance()->getDelegatePerformance($delegate->publicKey()))->toBeString();
    expect($component->instance()->getDelegatePerformance($delegate->publicKey()))->toBe(DelegateForgingStatus::missing);
});

it('should determine if delegates just missed based on their round history', function () {
    createRoundWithDelegatesAndPerformances([true, true, true, true, false]);

    $component = Livewire::test(DelegateDataBoxes::class);

    $component->call('pollStatistics');

    $delegateWallet = Wallet::first();
    $delegate = new WalletViewModel($delegateWallet);

    expect($component->instance()->getDelegatePerformance($delegate->publicKey()))->toBeString();
    expect($component->instance()->getDelegatePerformance($delegate->publicKey()))->toBe(DelegateForgingStatus::missed);
});

it('should determine if delegates are forging after missing 4 slots based on their round history', function () {
    createRoundWithDelegatesAndPerformances([false, false, false, false, true]);

    $component = Livewire::test(DelegateDataBoxes::class);

    $component->call('pollStatistics');

    $delegateWallet = Wallet::first();
    $delegate = new WalletViewModel($delegateWallet);

    expect($component->instance()->getDelegatePerformance($delegate->publicKey()))->toBeString();
    expect($component->instance()->getDelegatePerformance($delegate->publicKey()))->toBe(DelegateForgingStatus::forging);
});

it('should return the block count', function () {
    createRoundWithDelegatesAndPerformances();

    $component = Livewire::test(DelegateDataBoxes::class);

    expect($component->instance()->getBlockCount())->toBeString();
});

it('should return the next delegate', function () {
    createRoundWithDelegatesAndPerformances();

    $component = Livewire::test(DelegateDataBoxes::class);

    expect($component->instance()->getNextdelegate())->toBeInstanceOf(WalletViewModel::class);
});
