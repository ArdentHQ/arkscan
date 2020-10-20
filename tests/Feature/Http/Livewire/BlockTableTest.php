<?php

declare(strict_types=1);

use App\Http\Livewire\BlockTable;
use App\Models\Block;
use App\ViewModels\ViewModelFactory;
use Livewire\Livewire;
use function Tests\configureExplorerDatabase;

beforeEach(fn () => configureExplorerDatabase());

it('should list the first page of records', function () {
    Block::factory(60)->create();

    $component = Livewire::test(BlockTable::class);

    foreach (ViewModelFactory::paginate(Block::latestByHeight()->paginate())->items() as $block) {
        $component->assertSee($block->id());
        $component->assertSee($block->timestamp());
        $component->assertSee($block->delegate());
        $component->assertSee($block->height());
        $component->assertSee($block->transactionCount());
        $component->assertSee($block->amount());
        $component->assertSee($block->fee());
    }
});
