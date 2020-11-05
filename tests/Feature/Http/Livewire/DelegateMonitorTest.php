<?php

declare(strict_types=1);

use App\Http\Livewire\DelegateMonitor;
use App\Models\Block;
use App\Models\Round;
use App\Models\Wallet;
use App\Services\Cache\WalletCache;
use Livewire\Livewire;
use function Tests\bip39;
use function Tests\configureExplorerDatabase;

function createRoundWithDelegates(): void
{
    $block = Block::factory()->create([
        'height'    => 5720529,
        'timestamp' => 113620904,
    ]);

    Wallet::factory(51)->create()->each(function ($wallet) use ($block) {
        $wallet->update(['public_key' => bip39()]);

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

beforeEach(fn () => configureExplorerDatabase());

// @TODO: make assertions about data visibility
it('should render without errors', function () {
    createRoundWithDelegates();

    $component = Livewire::test(DelegateMonitor::class);
    $component->call('pollDelegates');
});
