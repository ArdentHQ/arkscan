<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Facades\Settings;
use App\Http\Livewire\Home\Blocks;
use App\Models\Block;
use App\Models\Scopes\OrderByHeightScope;
use App\Services\NumberFormatter;
use App\ViewModels\ViewModelFactory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;

it('should list the first page of blocks', function () {
    Block::factory(30)->create();

    $component = Livewire::test(Blocks::class)
        ->call('setIsReady');

    foreach (ViewModelFactory::collection(Block::withScope(OrderByHeightScope::class)->take(15)->get()) as $block) {
        $component->assertSee($block->id());
        $component->assertSee($block->timestamp());
        $component->assertSee($block->username());
        $component->assertSee(NumberFormatter::number($block->height()));
        $component->assertSee(NumberFormatter::number($block->transactionCount()));
        $component->assertSeeInOrder([
            Network::currency(),
            number_format($block->amount()),
        ]);
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

it('should cache the total block count', function () {
    Block::factory(5)->create();

    expect(Cache::has('blocks_total_count'))->toBeFalse();

    Livewire::test(Blocks::class)->call('setIsReady');

    expect(Cache::get('blocks_total_count'))->toBe(5);
});

it('should use the cached total count instead of querying the database', function () {
    Block::factory(5)->create();

    Cache::put('blocks_total_count', 999, 60);

    $component = Livewire::test(Blocks::class)->call('setIsReady');

    expect($component->get('blocks')->total())->toBe(999);
});

it('should show stale total count when new blocks are added after caching', function () {
    Block::factory(5)->create();

    Livewire::test(Blocks::class)->call('setIsReady');

    expect(Cache::get('blocks_total_count'))->toBe(5);

    Block::factory(3)->create();

    $component = Livewire::test(Blocks::class)->call('setIsReady');

    expect($component->get('blocks')->total())->toBe(5);
});

it('should reload on new block websocket event', function () {
    $component = Livewire::test(Blocks::class)
        ->call('setIsReady');

    $block = Block::factory()->create([
        'height' => 12345,
    ]);

    $component->assertDontSee($block->id)
        ->dispatch('echo:blocks,NewBlock')
        ->assertSee($block->id);
});

it('should poll when broadcasting driver is not reverb', function () {
    Config::set('broadcasting.default', 'log');

    Livewire::test(Blocks::class)
        ->call('setIsReady')
        ->assertSee('wire:poll.10s', false);
});

it('should not poll when broadcasting driver is reverb', function () {
    Config::set('broadcasting.default', 'reverb');

    Livewire::test(Blocks::class)
        ->call('setIsReady')
        ->assertDontSee('wire:poll.10s', false);
});
