<?php

declare(strict_types=1);

use App\Http\Livewire\DelegateMonitor;
use App\Models\Block;
use App\Models\Round;
use App\Models\Wallet;
use App\Services\Cache\WalletCache;
use Livewire\Livewire;

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

// @TODO: make assertions about data visibility
it('should render without errors', function () {
    createRoundWithDelegates();

    $component = Livewire::test(DelegateMonitor::class);
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

    $component = Livewire::test(DelegateMonitor::class);
    $component->call('pollDelegates');

    expect((new WalletCache())->getLastBlock($wallets->first()->public_key))->toBe([]);

    foreach ($wallets->skip(1) as $wallet) {
        expect((new WalletCache())->getLastBlock($wallet->public_key))->not()->toBe([]);
    }
});
