<?php

declare(strict_types=1);

use App\Facades\Settings;
use App\Http\Livewire\Stats\Chart;
use App\Services\Cache\NetworkCache;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;
use function Tests\fakeCryptoCompare;

beforeEach(function (): void {
    Carbon::setTestNow('2020-01-01 00:00:00');
});

it('should render the component with fiat value', function () {
    fakeCryptoCompare();

    Config::set('explorer.networks.development.canBeExchanged', true);
    Config::set('explorer.networks.development.currency', 'ARK');

    Artisan::call('explorer:cache-currencies-data');
    Artisan::call('explorer:cache-currencies-history --no-delay');
    Artisan::call('explorer:cache-prices');

    (new NetworkCache())->setSupply(fn () => 456748578.342 * 1e8);

    Livewire::test(Chart::class)
        ->set('period', 'day')
        ->assertSee(trans('pages.statistics.chart.price'))
        ->assertSee('0.00003363 BTC')
        ->assertSee('$1.22')
        ->assertSee('14.99%')
        ->assertSee(trans('pages.statistics.chart.market-cap'))
        ->assertSee('$558,145,930')
        ->assertSee(trans('pages.statistics.chart.min-price'))
        ->assertSee('1.275 BTC')
        ->assertSee(trans('pages.statistics.chart.max-price'))
        ->assertSee('2.469 BTC')
        ->assertSee('[1.898,1.904,1.967,1.941,2.013,2.213,2.414,2.369,2.469,2.374,2.228,2.211,2.266,2.364,2.341,2.269,1.981,1.889,1.275,1.471,1.498,1.518,1.61,1.638]');
});

it('should render the component with non fiat value', function () {
    Settings::shouldReceive('all')->andReturn(Settings::all());
    Settings::shouldReceive('theme')->andReturn('light');
    Settings::shouldReceive('currency')->andReturn('BTC');

    fakeCryptoCompare(false, 'BTC');

    Config::set('explorer.networks.development.canBeExchanged', true);
    Config::set('explorer.networks.development.currency', 'ARK');

    Artisan::call('explorer:cache-currencies-data');
    Artisan::call('explorer:cache-currencies-history --no-delay');
    Artisan::call('explorer:cache-prices');

    (new NetworkCache())->setSupply(fn () => 456748578.342 * 1e8);

    Livewire::test(Chart::class)
        ->set('period', 'day')
        ->assertSee(trans('pages.statistics.chart.price'))
        ->assertSee('0.00003363 BTC')
        ->assertSee('1.88%')
        ->assertSee(trans('pages.statistics.chart.market-cap'))
        ->assertSee('15,360.45468964 BTC')
        ->assertSee(trans('pages.statistics.chart.min-price'))
        ->assertSee('1.275 BTC')
        ->assertSee(trans('pages.statistics.chart.max-price'))
        ->assertSee('2.469 BTC')
        ->assertSee('[1.898,1.904,1.967,1.941,2.013,2.213,2.414,2.369,2.469,2.374,2.228,2.211,2.266,2.364,2.341,2.269,1.981,1.889,1.275,1.471,1.498,1.518,1.61,1.638]');
});

it('should not render the component', function () {
    fakeCryptoCompare();

    Config::set('explorer.networks.development.canBeExchanged', false);
    Config::set('explorer.networks.development.currency', 'ARK');

    Artisan::call('explorer:cache-currencies-data');
    Artisan::call('explorer:cache-currencies-history --no-delay');
    Artisan::call('explorer:cache-prices');

    (new NetworkCache())->setSupply(fn () => 456748578.342 * 1e8);

    Livewire::test(Chart::class)
        ->set('period', 'day')
        ->assertDontSee(trans('pages.statistics.chart.price'))
        ->assertDontSee('0.00003363 BTC')
        ->assertDontSee('$1.22')
        ->assertDontSee('14.99%')
        ->assertDontSee(trans('pages.statistics.chart.market-cap'))
        ->assertDontSee('$558,145,930')
        ->assertDontSee(trans('pages.statistics.chart.min-price'))
        ->assertDontSee('1.275 BTC')
        ->assertDontSee(trans('pages.statistics.chart.max-price'))
        ->assertDontSee('2.469 BTC')
        ->assertDontSee('[1.898]');
});

it('should filter by year', function () {
    fakeCryptoCompare();

    Config::set('explorer.networks.development.canBeExchanged', true);
    Config::set('explorer.networks.development.currency', 'ARK');

    Artisan::call('explorer:cache-currencies-data');
    Artisan::call('explorer:cache-currencies-history --no-delay');
    Artisan::call('explorer:cache-prices');

    (new NetworkCache())->setSupply(fn () => 456748578.342 * 1e8);

    Livewire::test(Chart::class)
        ->set('period', 'year')
        ->assertSee(trans('pages.statistics.chart.price'))
        ->assertSee('0.00003363 BTC')
        ->assertSee('$1.22')
        ->assertSee('14.99%')
        ->assertSee(trans('pages.statistics.chart.market-cap'))
        ->assertSee('$558,145,930')
        ->assertSee(trans('pages.statistics.chart.min-price'))
        ->assertSee('1.275 BTC')
        ->assertSee(trans('pages.statistics.chart.max-price'))
        ->assertSee('2.469 BTC')
        ->assertSee('[0.1802,0.1793,0.1961,0.189,0.1976,0.2091,0.2059,0.195,0.1954,0.204,0.2017,0.2171,0.2111,0.2184,0.2239,0.2193,0.2119,0.2214,0.2169,0.2129,0.2191,0.2203,0.2132,0.2054,0.2077,0.2086,0.1976,0.1895,0.1925,0.1767,0.1623,0.1681,0.1525,0.1625,0.1668,0.1784,0.1842,0.1897,0.187,0.1857,0.1803,0.1898,0.1803,0.1759,0.1867,0.1839,0.1898,0.1844,0.1671,0.1683,0.1665,0.1727,0.1669,0.1703,0.1605,0.1435,0.1494,0.1537,0.1552,0.1504,0.1531,0.1536,0.1482,0.1451,0.1442,0.1466,0.1485,0.1493,0.1435,0.1431,0.1398,0.1399,0.1456,0.1455,0.1457,0.1524,0.1462,0.1429,0.1381,0.1438,0.1407,0.14,0.1392,0.142,0.1486,0.1452,0.1556,0.16,0.1557,0.1523,0.1578,0.1646,0.1545,0.1635,0.1577,0.1562,0.1571,0.1628,0.1576,0.16,0.16,0.1669,0.1743,0.1764,0.1975,0.1967,0.2087,0.2147,0.2004,0.2188,0.2956,0.2755,0.2724,0.2732,0.284,0.2732,0.2425,0.2351,0.2484,0.2248,0.2293,0.2263,0.2265,0.2449,0.2258,0.2031,0.1907,0.1966,0.1947,0.1852,0.1936,0.2092,0.2173,0.216,0.2282,0.2328,0.2158,0.1819,0.1881,0.1881,0.1846,0.0916,0.1062,0.1109,0.1149,0.1065,0.125,0.1328,0.168,0.1595,0.1624,0.1451,0.1543,0.1625,0.1595,0.1629,0.1513,0.1446,0.1366,0.1482,0.1584,0.1558,0.1567,0.1604,0.1621,0.1598,0.1691,0.1637,0.1694,0.167,0.1538,0.1528,0.1529,0.1526,0.1989,0.182,0.1804,0.1735,0.1791,0.1737,0.1665,0.1604,0.1652,0.1672,0.173,0.1878,0.1865,0.184,0.1816,0.1868,0.1897,0.195,0.1996,0.1949,0.1922,0.1933,0.1894,0.1981,0.2066,0.2096,0.1889,0.1836,0.188,0.1905,0.1957,0.1796,0.1959,0.2084,0.2074,0.2069,0.2131,0.2056,0.2247,0.2123,0.2144,0.2274,0.2225,0.2161,0.2161,0.2158,0.2214,0.2182,0.2378,0.2333,0.2338,0.2333,0.2324,0.2389,0.2345,0.2377,0.2361,0.2405,0.2349,0.2507,0.251,0.2449,0.262,0.2763,0.285,0.3004,0.2834,0.2792,0.2823,0.3029,0.2994,0.32,0.3245,0.2969,0.33,0.2822,0.2808,0.2882,0.2601,0.2684,0.2559,0.2597,0.254,0.2809,0.2757,0.2882,0.2874,0.2975,0.3347,0.3294,0.2954,0.33,0.3183,0.3566,0.3368,0.3942,0.4054,0.4529,0.4412,0.4583,0.4607,0.4433,0.4236,0.4231,0.3735,0.403,0.4181,0.4522,0.4282,0.4249,0.4995,0.5938,0.5503,0.5226,0.5128,0.5081,0.5246,0.511,0.5047,0.4796,0.4858,0.4868,0.5191,0.5057,0.5284,0.5467,0.5224,0.479,0.5048,0.4779,0.5056,0.4978,0.533,0.4788,0.4914,0.4467,0.4554,0.4689,0.4725,0.4545,0.4434,0.4074,0.3245,0.3406,0.3135,0.32,0.3147,0.311,0.3452,0.3542,0.3511,0.3994,0.4082,0.3941,0.3547,0.3426,0.3566,0.3504,0.3617,0.3443,0.306,0.2998,0.2775,0.2969,0.3011,0.3025,0.3031,0.2952,0.3014,0.3045,0.2871,0.2735,0.2771,0.2784,0.2806,0.2686,0.2663,0.2789,0.3255,0.2987,0.3266,0.3154,0.3105,0.3014,0.2956,0.2867,0.2991,0.2946,0.2927,0.2704,0.2781]');
});

it('should render min max price and percentage', function () {
    fakeCryptoCompare();

    Config::set('explorer.networks.development.canBeExchanged', true);
    Config::set('explorer.networks.development.currency', 'ARK');

    Artisan::call('explorer:cache-currencies-data');
    Artisan::call('explorer:cache-currencies-history --no-delay');
    Artisan::call('explorer:cache-prices');

    (new NetworkCache())->setSupply(fn () => 456748578.342 * 1e8);

    Livewire::test(Chart::class)
        ->set('period', 'year')
        ->assertSee(trans('pages.statistics.chart.price'))
        ->assertSee('0.00003363 BTC')
        ->assertSee('$1.22')
        ->assertSee('14.99%')
        ->assertSee(trans('pages.statistics.chart.market-cap'))
        ->assertSee('$558,145,930')
        ->assertSee(trans('pages.statistics.chart.min-price'))
        ->assertSee('1.275 BTC')
        ->assertSee(trans('pages.statistics.chart.max-price'))
        ->assertSee('2.469 BTC')
        ->assertSee('[0.1802,0.1793,0.1961,0.189,0.1976,0.2091,0.2059,0.195,0.1954,0.204,0.2017,0.2171,0.2111,0.2184,0.2239,0.2193,0.2119,0.2214,0.2169,0.2129,0.2191,0.2203,0.2132,0.2054,0.2077,0.2086,0.1976,0.1895,0.1925,0.1767,0.1623,0.1681,0.1525,0.1625,0.1668,0.1784,0.1842,0.1897,0.187,0.1857,0.1803,0.1898,0.1803,0.1759,0.1867,0.1839,0.1898,0.1844,0.1671,0.1683,0.1665,0.1727,0.1669,0.1703,0.1605,0.1435,0.1494,0.1537,0.1552,0.1504,0.1531,0.1536,0.1482,0.1451,0.1442,0.1466,0.1485,0.1493,0.1435,0.1431,0.1398,0.1399,0.1456,0.1455,0.1457,0.1524,0.1462,0.1429,0.1381,0.1438,0.1407,0.14,0.1392,0.142,0.1486,0.1452,0.1556,0.16,0.1557,0.1523,0.1578,0.1646,0.1545,0.1635,0.1577,0.1562,0.1571,0.1628,0.1576,0.16,0.16,0.1669,0.1743,0.1764,0.1975,0.1967,0.2087,0.2147,0.2004,0.2188,0.2956,0.2755,0.2724,0.2732,0.284,0.2732,0.2425,0.2351,0.2484,0.2248,0.2293,0.2263,0.2265,0.2449,0.2258,0.2031,0.1907,0.1966,0.1947,0.1852,0.1936,0.2092,0.2173,0.216,0.2282,0.2328,0.2158,0.1819,0.1881,0.1881,0.1846,0.0916,0.1062,0.1109,0.1149,0.1065,0.125,0.1328,0.168,0.1595,0.1624,0.1451,0.1543,0.1625,0.1595,0.1629,0.1513,0.1446,0.1366,0.1482,0.1584,0.1558,0.1567,0.1604,0.1621,0.1598,0.1691,0.1637,0.1694,0.167,0.1538,0.1528,0.1529,0.1526,0.1989,0.182,0.1804,0.1735,0.1791,0.1737,0.1665,0.1604,0.1652,0.1672,0.173,0.1878,0.1865,0.184,0.1816,0.1868,0.1897,0.195,0.1996,0.1949,0.1922,0.1933,0.1894,0.1981,0.2066,0.2096,0.1889,0.1836,0.188,0.1905,0.1957,0.1796,0.1959,0.2084,0.2074,0.2069,0.2131,0.2056,0.2247,0.2123,0.2144,0.2274,0.2225,0.2161,0.2161,0.2158,0.2214,0.2182,0.2378,0.2333,0.2338,0.2333,0.2324,0.2389,0.2345,0.2377,0.2361,0.2405,0.2349,0.2507,0.251,0.2449,0.262,0.2763,0.285,0.3004,0.2834,0.2792,0.2823,0.3029,0.2994,0.32,0.3245,0.2969,0.33,0.2822,0.2808,0.2882,0.2601,0.2684,0.2559,0.2597,0.254,0.2809,0.2757,0.2882,0.2874,0.2975,0.3347,0.3294,0.2954,0.33,0.3183,0.3566,0.3368,0.3942,0.4054,0.4529,0.4412,0.4583,0.4607,0.4433,0.4236,0.4231,0.3735,0.403,0.4181,0.4522,0.4282,0.4249,0.4995,0.5938,0.5503,0.5226,0.5128,0.5081,0.5246,0.511,0.5047,0.4796,0.4858,0.4868,0.5191,0.5057,0.5284,0.5467,0.5224,0.479,0.5048,0.4779,0.5056,0.4978,0.533,0.4788,0.4914,0.4467,0.4554,0.4689,0.4725,0.4545,0.4434,0.4074,0.3245,0.3406,0.3135,0.32,0.3147,0.311,0.3452,0.3542,0.3511,0.3994,0.4082,0.3941,0.3547,0.3426,0.3566,0.3504,0.3617,0.3443,0.306,0.2998,0.2775,0.2969,0.3011,0.3025,0.3031,0.2952,0.3014,0.3045,0.2871,0.2735,0.2771,0.2784,0.2806,0.2686,0.2663,0.2789,0.3255,0.2987,0.3266,0.3154,0.3105,0.3014,0.2956,0.2867,0.2991,0.2946,0.2927,0.2704,0.2781]');
});
