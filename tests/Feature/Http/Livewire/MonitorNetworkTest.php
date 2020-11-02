<?php

declare(strict_types=1);

use App\Http\Livewire\MonitorNetwork;
use App\Models\Block;
use App\Models\Round;
use App\Models\Wallet;
use App\Services\Cache\WalletCache;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use function Tests\configureExplorerDatabase;

beforeEach(fn () => configureExplorerDatabase());

// @TODO: make assertions about data visibility
it('should render without errors', function () {
    $block = Block::factory()->create([
        'height'    => 5720529,
        'timestamp' => 113620904,
    ]);

    Wallet::factory(51)->create()->each(function ($wallet) use ($block) {
        Round::factory()->create([
            'round'      => '112168',
            'public_key' => $wallet->public_key,
        ]);

        Cache::tags(['delegates'])->put($wallet->public_key, $wallet);

        (new WalletCache())->setLastBlock($wallet->public_key, fn () => [
            'id'     => $block->id,
            'height' => $block->height->toNumber(),
        ]);
    });

    $component = Livewire::test(MonitorNetwork::class);
});
