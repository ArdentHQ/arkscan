<?php

declare(strict_types=1);

use App\Http\Livewire\Tables\Blocks;
use App\Models\Block;
use App\ViewModels\ViewModelFactory;
use Livewire\Livewire;
use function Tests\configureExplorerDatabase;

beforeEach(fn () => configureExplorerDatabase());

it('should list the first page of records', function () {
    Block::factory(30)->create();

    $component = Livewire::test(Blocks::class, [
        'blocks' => Block::latestByHeight(),
    ]);

    foreach (ViewModelFactory::paginate(Block::latestByHeight()->paginate())->items() as $block) {
        $component->assertSee($block->id());
        $component->assertSee($block->timestamp());
        $component->assertSee($block->username());
        $component->assertSee($block->height());
        $component->assertSee($block->transactionCount());
        $component->assertSee($block->amount());
        $component->assertSee($block->fee());
    }
});
