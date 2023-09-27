<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Http\Livewire\Home\Blocks;
use App\Models\Block;
use App\Models\Scopes\OrderByHeightScope;
use App\Services\NumberFormatter;
use App\ViewModels\ViewModelFactory;
use Livewire\Livewire;

it('should list the first page of blocks', function () {
    Block::factory(30)->create();

    $component = Livewire::test(Blocks::class)
        ->set('state.selected', 'blocks')
        ->call('pollBlocks');

    foreach (ViewModelFactory::collection(Block::withScope(OrderByHeightScope::class)->take(15)->get()) as $block) {
        $component->assertSee($block->id());
        $component->assertSee($block->timestamp());
        $component->assertSee($block->username());
        $component->assertSee(NumberFormatter::number($block->height()));
        $component->assertSee(NumberFormatter::number($block->transactionCount()));
        $component->assertSee(NumberFormatter::currency($block->amount(), Network::currency()));
        $component->assertSee(NumberFormatter::currency($block->fee(), Network::currency()));
    }
});

it('should poll blocks when currency changed', function () {
    $block = Block::factory()->create();

    Livewire::test(Blocks::class)
        ->set('state.selected', 'blocks')
        ->assertDontSee($block->id)
        ->emit('currencyChanged')
        ->assertSee($block->id);
});
