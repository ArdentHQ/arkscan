<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Http\Livewire\BlockTable;
use App\Models\Block;
use App\Models\Scopes\OrderByHeightScope;
use App\Services\Cache\CryptoCompareCache;
use App\Services\NumberFormatter;
use App\Services\Settings;
use App\ViewModels\ViewModelFactory;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;
use function Tests\configureExplorerDatabase;

beforeEach(fn () => configureExplorerDatabase());

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

    (new CryptoCompareCache())->setPrices('USD', collect([
        '2020-10-19' => 24210,
    ]));

    (new CryptoCompareCache())->setPrices('BTC', collect([
        '2020-10-19' => 0.1234567,
    ]));

    Block::factory()->create([
        'total_amount' => 499 * 1e8,
    ]);

    $component = Livewire::test(BlockTable::class);

    $expectedValue = NumberFormatter::currency(12080790, 'USD');

    $component->assertSeeHtml('data-tippy-content="'.$expectedValue.'"');
    $component->assertDontSeeHtml('data-tippy-content="61.6048933 BTC"');

    $settings = Settings::all();
    $settings['currency'] = 'BTC';
    Session::put('settings', json_encode($settings));

    $component->emit('currencyChanged', 'BTC');

    $component->assertDontSeeHtml('data-tippy-content="'.$expectedValue.'"');
    $component->assertSeeHtml('data-tippy-content="61.6048933 BTC"');
});
