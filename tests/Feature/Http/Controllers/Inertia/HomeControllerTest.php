<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Facades\Settings;
use App\Models\Transaction;
use App\Services\Cache\CryptoDataCache;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\NetworkStatusBlockCache;
use App\Services\Cache\PriceChartCache;
use App\Services\MarketCap;
use App\Services\NumberFormatter;
use Illuminate\Support\Facades\Cache;
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
            ->missing('transactions'));
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
