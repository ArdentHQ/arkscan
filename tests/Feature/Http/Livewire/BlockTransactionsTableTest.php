<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Facades\Settings;
use App\Http\Livewire\BlockTransactionsTable;
use App\Models\Block;
use App\Models\Transaction;
use App\Services\Cache\CryptoDataCache;
use App\Services\NumberFormatter;
use App\ViewModels\ViewModelFactory;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;

it('should list the first transactions for the giving block id', function () {
    $block = Block::factory()->create();
    Transaction::factory(25)->transfer()->create(['block_id' => $block->id]);

    $component = Livewire::test(BlockTransactionsTable::class, ['blockId' => $block->id]);

    foreach (ViewModelFactory::paginate($block->transactions()->paginate(25))->items() as $transaction) {
        $component->assertSee($transaction->id());
        $component->assertSee($transaction->timestamp());
        $component->assertSee($transaction->sender()->address());
        $component->assertSee($transaction->recipient()->address());
        $component->assertSee(NumberFormatter::currency($transaction->amount(), Network::currency()));
        $component->assertSee(NumberFormatter::currency($transaction->fee(), Network::currency()));
    }
});

it('should update the records fiat tooltip when currency changed', function () {
    Config::set('explorer.networks.development.canBeExchanged', true);

    (new CryptoDataCache())->setPrices('USD.week', collect([
        '2020-10-19' => 24210,
    ]));

    (new CryptoDataCache())->setPrices('BTC.week', collect([
        '2020-10-19' => 0.1234567,
    ]));

    $block = Block::factory()->create();

    Transaction::factory()->create([
        'block_id'          => $block->id,
        'timestamp'         => 112982056,
        'amount'            => 499 * 1e8,
    ]);

    $component = Livewire::test(BlockTransactionsTable::class, ['blockId' => $block->id]);

    $expectedValue = NumberFormatter::currency(12080790, 'USD');

    $component->assertSeeHtml('data-tippy-content="'.$expectedValue.'"');
    $component->assertDontSeeHtml('data-tippy-content="61.6048933 BTC"');

    $settings             = Settings::all();
    $settings['currency'] = 'BTC';

    Settings::shouldReceive('all')->andReturn($settings);
    Settings::shouldReceive('currency')->andReturn('BTC');

    $component->emit('currencyChanged', 'BTC');

    $component->assertDontSeeHtml('data-tippy-content="'.$expectedValue.'"');
    $component->assertSeeHtml('data-tippy-content="61.6048933 BTC"');
});
