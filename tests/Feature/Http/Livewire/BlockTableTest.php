<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Facades\Settings;
use App\Http\Livewire\BlockTable;
use App\Models\Block;
use App\Models\Scopes\OrderByHeightScope;
use App\Models\Transaction;
use App\Services\Cache\CryptoDataCache;
use App\Services\NumberFormatter;
use App\ViewModels\TransactionViewModel;
use App\ViewModels\ViewModelFactory;
use Carbon\Carbon;
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
    Config::set('arkscan.networks.development.canBeExchanged', true);

    $usdExchangeRate = 24210;
    $btcExchangeRate = 0.1234567;
    (new CryptoDataCache())->setPrices('USD.week', collect([
        '2020-10-19' => $usdExchangeRate,
    ]));

    (new CryptoDataCache())->setPrices('BTC.week', collect([
        '2020-10-19' => $btcExchangeRate,
    ]));

    $block = Block::factory()->create();

    $transactions = Transaction::factory(10)
        ->transfer()
        ->create([
            'block_id'  => $block->id,
            'timestamp' => Carbon::parse('2020-10-19 00:00:00')->timestamp,
        ])->concat(
            Transaction::factory(10)
                ->vote()
                ->create([
                    'block_id'  => $block->id,
                    'timestamp' => Carbon::parse('2020-10-19 00:00:00')->timestamp,
                ])
        )->concat(
            Transaction::factory(10)
                ->multiPayment()
                ->create([
                    'block_id'  => $block->id,
                    'timestamp' => Carbon::parse('2020-10-19 00:00:00')->timestamp,
                ])
        );

    $amount = 0;
    foreach ($transactions as $transaction) {
        $amount += (new TransactionViewModel($transaction))->amount();
    }

    $component = Livewire::test(BlockTable::class);

    $expectedUsd = NumberFormatter::currency($amount * $usdExchangeRate, 'USD');
    $expectedBtc = NumberFormatter::currency($amount * $btcExchangeRate, 'BTC');

    $component->assertSeeHtml('data-tippy-content="'.$expectedUsd.'"');
    $component->assertDontSeeHtml('data-tippy-content="'.$expectedBtc.'"');

    $settings             = Settings::all();
    $settings['currency'] = 'BTC';

    Settings::shouldReceive('all')->andReturn($settings);
    Settings::shouldReceive('currency')->andReturn('BTC');

    $component->emit('currencyChanged', 'BTC');

    $expectedUsd = NumberFormatter::currency($amount * $usdExchangeRate, 'USD');
    $expectedBtc = NumberFormatter::currency($amount * $btcExchangeRate, 'BTC');
});
