<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Facades\Settings;
use App\Http\Livewire\BlockTable;
use App\Models\Block;
use App\Models\Scopes\OrderByHeightScope;
use App\Services\Cache\CryptoDataCache;
use App\Services\NumberFormatter;
use App\ViewModels\ViewModelFactory;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;

it('should list the first page of records', function () {
    Block::factory(30)->create();

    $component = Livewire::test(BlockTable::class);

    foreach (ViewModelFactory::paginate(Block::withScope(OrderByHeightScope::class)->paginate())->items() as $block) {
        $component->assertSee($block->id());
        $component->assertSee($block->timestamp());
        $component->assertSee($block->username());
        $component->assertSee(NumberFormatter::number($block->height()));
        $component->assertSee(NumberFormatter::number($block->transactionCount()));
        $component->assertSee(NumberFormatter::currency($block->amount(), Network::currency()));
        $component->assertSee(NumberFormatter::currency($block->fee(), Network::currency()));
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

    Block::factory()->create([
        'total_amount' => 499 * 1e8,
    ]);

    $component = Livewire::test(BlockTable::class);

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
