<?php

declare(strict_types=1);

use App\Http\Livewire\LatestBlocksTable;
use App\Models\Block;
use App\ViewModels\ViewModelFactory;
use Livewire\Livewire;
use function Tests\configureExplorerDatabase;

beforeEach(fn () => configureExplorerDatabase());

it('should list the first page of records', function () {
    Block::factory(30)->create();

    $component = Livewire::test(LatestBlocksTable::class);

    foreach (ViewModelFactory::collection(Block::latestByHeight()->take(15)->get()) as $block) {
        $component->assertSee($block->id());
        $component->assertSee($block->timestamp());
        $component->assertSee($block->delegateUsername());
        $component->assertSee($block->height());
        $component->assertSee($block->transactionCount());
        $component->assertSee($block->amount());
        $component->assertSee($block->fee());
    }
});
