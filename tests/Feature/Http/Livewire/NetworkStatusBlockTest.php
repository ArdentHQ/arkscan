<?php

declare(strict_types=1);

use App\Http\Livewire\NetworkStatusBlock;
use App\Models\Block;
use App\Services\Cache\CryptoCompareCache;
use App\Services\Cache\NetworkCache;

use Livewire\Livewire;
use function Tests\configureExplorerDatabase;

it('should render with a height, name, supply and market cap', function () {
    configureExplorerDatabase();

    Block::factory()->create(['height' => 5651290]);

    (new NetworkCache())->setHeight(fn () => 5651290);
    (new NetworkCache())->setSupply(fn () => '13628098200000000');
    (new CryptoCompareCache())->setPrice('USD', 'USD', fn () => 0.2907);

    Livewire::test(NetworkStatusBlock::class)
        ->assertSee('5,651,290')
        ->assertSee('ARK Development Network')
        ->assertSee('136,280,982 DARK');

    // @TODO: add test for production which shows this
    // ->assertSee('Market Cap: 39,616,881.467 USD');
});
