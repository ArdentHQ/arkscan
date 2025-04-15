<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Facades\Settings;
use App\Http\Livewire\BlockTable;
use App\Models\Block;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\Cache\CryptoDataCache;
use App\Services\Cache\WalletCache;
use App\Services\NumberFormatter;
use App\ViewModels\TransactionViewModel;
use App\ViewModels\ViewModelFactory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
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

    $wallet = Wallet::factory()->create();

    $transactions = Transaction::factory(10)
        ->transfer()
        ->create([
            'block_hash'  => $block->hash,
            'timestamp'   => Carbon::parse('2020-10-19 00:00:00')->timestamp,
        ])
        ->concat(
            Transaction::factory(10)
                ->vote($wallet->address)
                ->create([
                    'block_hash'  => $block->hash,
                    'timestamp'   => Carbon::parse('2020-10-19 00:00:00')->timestamp,
                ])
        )
        ->concat(
            Transaction::factory(10)
                ->multiPayment([$wallet->address], [BigNumber::new(1e18)])
                ->create([
                    'block_hash'  => $block->hash,
                    'timestamp'   => Carbon::parse('2020-10-19 00:00:00')->timestamp,
                ])
        );

    $amount = 0;
    foreach ($transactions as $transaction) {
        $amount += (new TransactionViewModel($transaction))->amount();
    }

    $component = Livewire::test(BlockTable::class)
        ->call('setIsReady');

    $expectedUsd = NumberFormatter::currency($amount * $usdExchangeRate, 'USD');
    $expectedBtc = NumberFormatter::currency($amount * $btcExchangeRate, 'BTC');

    $component->assertSeeHtml('data-tippy-content="'.$expectedUsd.'"');
    $component->assertDontSeeHtml('data-tippy-content="'.$expectedBtc.'"');

    $settings             = Settings::all();
    $settings['currency'] = 'BTC';

    Settings::shouldReceive('all')->andReturn($settings);
    Settings::shouldReceive('currency')->andReturn('BTC');

    $component->dispatch('currencyChanged', 'BTC');

    $expectedUsd = NumberFormatter::currency($amount * $usdExchangeRate, 'USD');
    $expectedBtc = NumberFormatter::currency($amount * $btcExchangeRate, 'BTC');
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
