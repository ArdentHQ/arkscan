<?php

declare(strict_types=1);

use App\Http\Livewire\BlockTable;
use App\Models\Block;
use Livewire\Livewire;

use function Tests\configureExplorerDatabase;

beforeEach(fn () => configureExplorerDatabase());

it('should list the first page of records', function () {
    Block::factory(100)->create();

    $component = Livewire::test(BlockTable::class);

    foreach (Block::latestByHeight()->paginate()->items() as $block) {
        $component->assertSee($block->id);
        $component->assertSee($block->timestamp);
        $component->assertSee($block->generator_public_key);
        $component->assertSee($block->height);
        $component->assertSee($block->number_of_transactions);
        $component->assertSee($block->total_amount);
        $component->assertSee($block->total_fee);
    }
});
