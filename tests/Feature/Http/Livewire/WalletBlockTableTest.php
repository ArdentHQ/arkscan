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

    $component = Livewire::test(WalletBlockTable::class, [ViewModelFactory::make($this->subject)])
        ->call('setIsReady');

    foreach (ViewModelFactory::collection($blocks) as $block) {
        $component->assertSee($block->id());
        $component->assertSee($block->timestamp());
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

it('should show no data if not ready', function () {
    $block = Block::factory()->create([
        'generator_public_key' => $this->subject->public_key,
    ]);

    Livewire::test(WalletBlockTable::class, [ViewModelFactory::make($this->subject)])
        ->assertDontSee($block->id)
        ->call('setIsReady')
        ->assertSee($block->id);
});
