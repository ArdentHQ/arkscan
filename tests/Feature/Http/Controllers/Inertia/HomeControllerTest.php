<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Facades\Settings;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\Cache\CryptoDataCache;
use App\Services\Cache\MainsailCache;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\NetworkStatusBlockCache;
use App\Services\Cache\PriceChartCache;
use App\Services\Cache\ValidatorCache;
use App\Services\MarketCap;
use App\Services\NumberFormatter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    Cache::tags('statistics')->flush();
    Cache::tags('price_chart')->flush();
    Cache::tags('crypto_data')->flush();
    Cache::tags('network_status_block')->flush();
    Cache::tags('network')->flush();
});

it('should render the page without any errors', function () {
    $this->withoutExceptionHandling();

    $this
        ->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Home/Index')
            ->where('baseUrl', route('home', absolute: false))
            ->has('statistics')
            ->missing('transactions')
            ->missing('blocks'));
});

it('should have statistics', function () {
    Config::set('arkscan.networks.development.canBeExchanged', false);

    Wallet::factory()->count(11)->create();

    $cache = new NetworkCache();

    $cache->setSupply(function (): float {
        return 12345.6789 * 1e18;
    });

    $cache->setVotesPercentage('123.45');
    $cache->setHeight(fn () => 123456);

    (new ValidatorCache())->setTotalBalanceVoted(4567.2345);
    (new MainsailCache())->setFees([
        'min' => '1500000000',
        'avg' => '2500000000',
        'max' => '3500000000',
    ]);

    $this
        ->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Home/Index')
            ->where('statistics.blockHeight', 123456)
            ->where('statistics.totalSupply', '12K')
            ->where('statistics.voting.percentage', '123.45%')
            ->where('statistics.voting.amount', '4K')
            ->where('statistics.gas.low.amount', '1.5')
            ->where('statistics.gas.low.value', null)
            ->where('statistics.gas.average.amount', '2.5')
            ->where('statistics.gas.average.value', null)
            ->where('statistics.gas.high.amount', '3.5')
            ->where('statistics.gas.high.value', null));
});

it('should calculate gas statistics with value', function () {
    Config::set('arkscan.networks.development.canBeExchanged', true);

    Wallet::factory()->count(11)->create();

    $cache = new NetworkCache();

    $cache->setSupply(function (): float {
        return 12345.6789 * 1e18;
    });

    $cache->setVotesPercentage('123.45');

    (new ValidatorCache())->setTotalBalanceVoted(4567.2345);
    (new MainsailCache())->setFees([
        'min' => (string) BigNumber::new(1.5 * 1e18),
        'avg' => (string) BigNumber::new(2.5 * 1e18),
        'max' => (string) BigNumber::new(3.5 * 1e18),
    ]);

    (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 2.0);

    $this
        ->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Home/Index')
            ->where('statistics.gas.low.amount', '1500000000')
            ->where('statistics.gas.low.value', '$3.00')
            ->where('statistics.gas.average.amount', '2500000000')
            ->where('statistics.gas.average.value', '$5.00')
            ->where('statistics.gas.high.amount', '3500000000')
            ->where('statistics.gas.high.value', '$7.00'));
});

it('should format small gas values for fiat currencies', function () {
    Config::set('arkscan.networks.development.canBeExchanged', true);

    Wallet::factory()->count(11)->create();

    $cache = new NetworkCache();

    $cache->setSupply(function (): float {
        return 12345.6789 * 1e18;
    });

    $cache->setVotesPercentage('123.45');

    (new ValidatorCache())->setTotalBalanceVoted(4567.2345);
    (new MainsailCache())->setFees([
        'min' => '1000000000000000',
        'avg' => '1000000000000000',
        'max' => '1000000000000000',
    ]);

    (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 1.0);

    $expectedValue = sprintf('< %s', NumberFormatter::currency(0.01, Settings::currency()));

    $this
        ->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Home/Index')
            ->where('statistics.gas.low.value', $expectedValue)
            ->where('statistics.gas.average.value', $expectedValue)
            ->where('statistics.gas.high.value', $expectedValue));
});

it('should show the no-results message when no transactions exist', function () {
    $this
        ->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Home/Index')
            ->reloadOnly('transactions', fn (Assert $reload) => $reload
                ->has('transactions.data', 0)
                ->has('transactions.meta')
                ->where('transactions.noResultsMessage', (string) trans('tables.transactions.no_results.no_results'))));
});

it('should show the no-results message when no blocks exist', function () {
    $this
        ->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Home/Index')
            ->reloadOnly('blocks', fn (Assert $reload) => $reload
                ->has('blocks.data', 0)
                ->has('blocks.meta')
                ->where('blocks.noResultsMessage', (string) trans('tables.blocks.no_results'))));
});

it('should return transactions without a no-results message', function () {
    $transaction = Transaction::factory()->create();

    $this
        ->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Home/Index')
            ->reloadOnly('transactions', fn (Assert $reload) => $reload
                ->has('transactions.data', 1)
                ->has('transactions.meta')
                ->where('transactions.data.0.hash', $transaction->hash)
                ->where('transactions.noResultsMessage', null)));
});

it('should return blocks without a no-results message', function () {
    $block = Block::factory()->create();

    $this
        ->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Home/Index')
            ->reloadOnly('blocks', fn (Assert $reload) => $reload
                ->has('blocks.data', 1)
                ->has('blocks.meta')
                ->where('blocks.data.0.hash', $block->hash)
                ->where('blocks.noResultsMessage', null)));
});

it('should include chart data with market stats', function () {
    $currency        = Settings::currency();
    $networkCurrency = Network::currency();

    (new NetworkStatusBlockCache())->setPrice($networkCurrency, $currency, 2.0);
    (new NetworkCache())->setSupply(fn () => 1_000_000.0);
    (new CryptoDataCache())->setVolume($currency, '2255149');

    (new PriceChartCache())->setHistoricalRaw($currency, 'day', collect([
        1_700_000_000 => 1.0,
        1_700_003_600 => 1.5,
        1_700_007_200 => 1.2,
    ]));

    $expectedVolume    = NumberFormatter::currencyForViews(2255149, $currency);
    $expectedMarketCap = MarketCap::getFormatted($networkCurrency, $currency);

    $this
        ->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Home/Index')
            ->has('chart')
            ->where('chart.period', 'day')
            ->where('chart.datasets.2', 2)
            ->where('chart.theme.name', 'green')
            ->where('chart.market.volume', $expectedVolume)
            ->where('chart.market.marketCap', $expectedMarketCap));
});

it('should fallback to day period when chartPeriod is invalid', function () {
    $currency        = Settings::currency();
    $networkCurrency = Network::currency();

    (new NetworkStatusBlockCache())->setPrice($networkCurrency, $currency, 1.0);
    (new NetworkCache())->setSupply(fn () => 1_000_000.0);
    (new PriceChartCache())->setHistoricalRaw($currency, 'day', collect([
        1_700_000_000 => 1.0,
        1_700_003_600 => 1.1,
    ]));

    $this
        ->get(route('home', ['chartPeriod' => 'invalid']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Home/Index')
            ->where('chart.period', 'day'));
});
