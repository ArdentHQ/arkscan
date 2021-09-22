<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Http\Livewire\WalletBlockTable;
use App\Models\Block;
use App\Models\Wallet;
use App\Services\NumberFormatter;
use App\ViewModels\ViewModelFactory;
use Livewire\Livewire;
use function Tests\fakeCryptoCompare;

beforeEach(function () {
    fakeCryptoCompare();

    $this->subject = Wallet::factory()->create();
});

it('should list all blocks for the given public key', function () {
    $blocks = Block::factory(10)->create([
        'generator_public_key' => $this->subject->public_key,
    ]);

    $component = Livewire::test(WalletBlockTable::class, [$this->subject->public_key, 'username']);

    foreach (ViewModelFactory::collection($blocks) as $block) {
        $component->assertSee($block->id());
        $component->assertSee($block->timestamp());
        $component->assertSee(NumberFormatter::number($block->height()));
        $component->assertSee(NumberFormatter::number($block->transactionCount()));
        $component->assertSee(NumberFormatter::currency($block->amount(), Network::currency()));
        $component->assertSee(NumberFormatter::currency($block->fee(), Network::currency()));
    }
});
