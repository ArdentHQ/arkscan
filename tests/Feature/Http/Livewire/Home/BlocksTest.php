<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Facades\Settings;
use App\Http\Livewire\Home\Blocks;
use App\Models\Block;
use App\Models\Scopes\OrderByHeightScope;
use App\Services\Cache\WalletCache;
use App\Services\NumberFormatter;
use App\ViewModels\ViewModelFactory;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;

it('should list the first page of blocks', function () {
    $cache = new WalletCache();

    foreach (range(0, 30) as $index) {
        $this->travel(8)->seconds();

        $block = Block::factory()->create();

        $cache->setWalletNameByAddress($block->proposer, 'test-username-'.($index + 1));
    }

    $component = Livewire::test(Blocks::class)
        ->call('setIsReady');

    foreach (ViewModelFactory::collection(Block::withScope(OrderByHeightScope::class)->take(15)->get()) as $block) {
        $component->assertSee($block->hash());
        $component->assertSee($block->timestamp());
        $component->assertSee($block->username());
        $component->assertSee(NumberFormatter::number($block->height()));
        $component->assertSee(NumberFormatter::number($block->transactionCount()));
        $component->assertSeeInOrder([
            Network::currency(),
            number_format($block->totalReward()),
        ]);
        $component->assertSeeInOrder([
            Network::currency(),
            $block->totalRewardFiat(),
        ]);
    }
});

it('should refresh blocks when currency changed', function () {
    Config::set('arkscan.network', 'production');

    $component = Livewire::test(Blocks::class)
        ->call('setIsReady')
        ->assertSee('Value (USD)');

    Settings::shouldReceive('currency')
        ->andReturn('GBP');

    $component->dispatch('currencyChanged')
        ->assertSee('Value (GBP)');
});

it('should show message if no blocks', function () {
    Livewire::test(Blocks::class)
        ->call('setIsReady')
        ->assertSee(trans('tables.blocks.no_results'));
});
