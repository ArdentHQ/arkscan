<?php

declare(strict_types=1);

use App\Http\Livewire\MonitorNetwork;
use App\Models\Block;
use App\Models\Round;
use App\Models\Wallet;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use function Tests\configureExplorerDatabase;

beforeEach(fn () => configureExplorerDatabase());

// @TODO: make assertions about data visibility
it('should render without errors', function () {
    Wallet::factory(51)->create()->each(function ($wallet) {
        Round::factory()->create([
            'round'      => '112168',
            'public_key' => $wallet->public_key,
        ]);

        Cache::tags(['delegates'])->put($wallet->public_key, $wallet);
        Cache::put('lastBlock:'.$wallet->public_key, []);
    });

    Block::factory()->create([
        'height'    => 5720529,
        'timestamp' => 113620904,
    ]);

    $component = Livewire::test(MonitorNetwork::class);
});
