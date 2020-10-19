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
        $component->assertSee((string) $block->timestamp_carbon);
        $component->assertSee($block->delegate->username);
        $component->assertSee($block->height);
        $component->assertSee($block->number_of_transactions);
        $component->assertSee($block->formatted_total_amount);
        $component->assertSee($block->formatted_total_fee);
    }
});
