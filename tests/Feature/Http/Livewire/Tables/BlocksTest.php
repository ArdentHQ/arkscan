<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Http\Livewire\Tables\Blocks;
use App\Models\Block;
use App\Models\Scopes\OrderByHeightScope;
use App\Services\NumberFormatter;
use App\ViewModels\ViewModelFactory;
use Livewire\Livewire;

it('should list the first page of records', function () {
    Block::factory(30)->create();

    $component = Livewire::test(Blocks::class, [
        'blocks' => Block::withScope(OrderByHeightScope::class),
    ]);

    foreach (ViewModelFactory::paginate(Block::withScope(OrderByHeightScope::class)->paginate())->items() as $block) {
        $component->assertSee($block->id());
        $component->assertSee($block->timestamp());
        $component->assertSee($block->username());
        $component->assertSee(NumberFormatter::number($block->height()));
        $component->assertSee(NumberFormatter::number($block->transactionCount()));
        $component->assertSee(NumberFormatter::currency($block->amount(), Network::currency()));
        $component->assertSee(NumberFormatter::currency($block->fee(), Network::currency()));
    }
});
