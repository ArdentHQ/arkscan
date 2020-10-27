<?php

declare(strict_types=1);

use App\Http\Livewire\WalletBlockTable;
use App\Models\Block;
use App\Models\Wallet;
use App\ViewModels\ViewModelFactory;
use Livewire\Livewire;
use function Tests\configureExplorerDatabase;
use function Tests\fakeCryptoCompare;

beforeEach(function () {
    fakeCryptoCompare();

    configureExplorerDatabase();

    $this->subject = Wallet::factory()->create();
});

it('should list all blocks for the given public key', function () {
    $blocks = Block::factory(10)->create([
        'generator_public_key' => $this->subject->public_key,
    ]);

    $component = Livewire::test(WalletBlockTable::class, [$this->subject->public_key]);

    foreach (ViewModelFactory::collection($blocks) as $block) {
        $component->assertSee($block->id());
        $component->assertSee($block->timestamp());
        $component->assertSee($block->height());
        $component->assertSee($block->transactionCount());
        $component->assertSee($block->fee());
        $component->assertSee($block->amount());
    }
});
