<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Http\Livewire\BlockTable;
use App\Models\Block;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Wallet;
use App\Services\Cache\WalletCache;
use App\Services\NumberFormatter;
use App\ViewModels\ViewModelFactory;
use Carbon\Carbon;
use Livewire\Livewire;

it('should list the first page of records', function () {
    $this->travelTo(Carbon::parse('2023-07-12 00:00:00'));

    $cache = new WalletCache();

    foreach (range(0, 40) as $index) {
        $this->travel(8)->seconds();

        $block = Block::factory()->create([
            'timestamp' => Carbon::now()->timestamp,
            'number'    => $index + 1,
        ]);

        $cache->setWalletNameByAddress($block->proposer, 'test-username-'.($index + 1));
    }

    $component = Livewire::test(BlockTable::class)
        ->call('setIsReady');

    foreach (ViewModelFactory::paginate(Block::withScope(OrderByTimestampScope::class)->paginate())->items() as $block) {
        $component->assertSee($block->hash());
        $component->assertSee($block->timestamp());
        $component->assertSee($block->username());
        $component->assertSee(NumberFormatter::number($block->height()));
        $component->assertSee(NumberFormatter::number($block->transactionCount()));
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

it('should list the last page of records', function () {
    $this->travelTo(Carbon::parse('2023-07-12 00:00:00'));

    $cache = new WalletCache();

    foreach (range(0, 40) as $index) {
        $this->travel(8)->seconds();

        $block = Block::factory()->create([
            'timestamp' => Carbon::now()->timestamp,
            'number'    => $index + 1,
        ]);

        $cache->setWalletNameByAddress($block->proposer, 'test-username-'.($index + 1));
    }

    $component = Livewire::test(BlockTable::class)
        ->call('setIsReady')
        ->call('setPage', 2);

    $blocks = Block::withScope(OrderByTimestampScope::class)
        ->paginate(25, ['*'], 'page', 2, Block::count());

    foreach (ViewModelFactory::paginate($blocks)->items() as $block) {
        $component->assertSee($block->hash());
        $component->assertSee($block->timestamp());
        $component->assertSee($block->username());
        $component->assertSee(NumberFormatter::number($block->height()));
        $component->assertSee(NumberFormatter::number($block->transactionCount()));
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

it('should handle a lot of blocks', function () {
    $this->travelTo(Carbon::parse('2023-07-12 00:00:00'));

    $wallet = Wallet::factory()->create();

    foreach (range(1, 4000) as $index) {
        $this->travel(8)->seconds();

        Block::factory()->create([
            'proposer'          => $wallet->address,
            'timestamp'         => Carbon::now()->timestamp,
            'number'            => $index,
        ]);
    }

    expect(Block::count())->toBe(4000);

    Livewire::test(BlockTable::class)
        ->call('setIsReady')
        ->assertSee(160) // 4000 / 25 per page
        ->call('gotoPage', 159)
        ->assertSee(160);
});

it('should reload on new block event', function () {
    $this->travelTo(Carbon::parse('2023-07-12 00:00:00'));

    foreach (range(1, 400) as $index) {
        $this->travel(8)->seconds();

        Block::factory()->create([
            'timestamp' => Carbon::parse('2023-07-12 00:00:00')->timestamp,
            'number'    => $index,
        ]);
    }

    $component = Livewire::test(BlockTable::class);
    $component->call('setIsReady');

    $this->travel(10)->minutes();

    $otherBlock = Block::factory()->create([
        'timestamp' => Carbon::parse('2023-07-13 00:00:00')->timestamp,
        'number'    => 401,
    ]);

    $component->assertDontSee($otherBlock->hash)
        ->dispatch('echo:blocks,NewBlock')
        ->assertSee($otherBlock->hash);
});

it('should handle snapshot of blocks', function () {
    $this->travelTo(Carbon::parse('2023-07-12 00:00:00'));

    $cache = new WalletCache();

    foreach (range(1001, 1511) as $index) {
        $this->travel(8)->seconds();

        $block = Block::factory()->create([
            'timestamp' => Carbon::now()->timestamp,
            'number'    => $index + 1,
        ]);

        $cache->setWalletNameByAddress($block->proposer, 'test-username-'.($index + 1));
    }

    $blockCount = Block::count();
    $pageCount  = ceil($blockCount / 25);

    Livewire::test(BlockTable::class)
        ->call('setIsReady')
        ->set('paginatorsPerPage.default', 25)
        ->assertSee('Page 1 of '.$pageCount);
});

it('should list the last page of a snapshot', function () {
    $this->travelTo(Carbon::parse('2023-07-12 00:00:00'));

    $cache = new WalletCache();

    foreach (range(1001, 1511) as $index) {
        $this->travel(8)->seconds();

        $block = Block::factory()->create([
            'timestamp' => Carbon::now()->timestamp,
            'number'    => $index + 1,
        ]);

        $cache->setWalletNameByAddress($block->proposer, 'test-username-'.($index + 1));
    }

    $blockCount = Block::count();
    $pageCount  = ceil($blockCount / 25);

    $component = Livewire::test(BlockTable::class)
        ->call('setIsReady')
        ->call('setPage', $pageCount);

    $blocks = Block::withScope(OrderByTimestampScope::class)
        ->paginate(25, ['*'], 'page', $pageCount, $blockCount);

    expect($blocks->items())->toHaveCount(11);

    foreach (ViewModelFactory::paginate($blocks)->items() as $block) {
        $component->assertSee($block->hash());
        $component->assertSee($block->timestamp());
        $component->assertSee($block->username());
        $component->assertSee(NumberFormatter::number($block->height()));
        $component->assertSee(NumberFormatter::number($block->transactionCount()));
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
