<?php

declare(strict_types=1);

use App\Events\Statistics\MarketData;
use App\Facades\Network;
use App\Services\Cache\CryptoDataCache;
use App\Services\Cache\StatisticsCache;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->travelTo('2024-06-24 16:55:23');

    Config::set('currencies', [
        'usd' => config('currencies.usd'),
    ]);
});

it('should cache market data statistics', function () {
    Event::fake();

    Config::set('arkscan.networks.development.canBeExchanged', true);
    $cache  = new StatisticsCache();
    $crypto = new CryptoDataCache();

    $currency = 'USD';
    $crypto->setHistoricalFullResponse(Network::currency(), $currency, json_decode(file_get_contents(base_path('tests/fixtures/coingecko/historical_all.json')), true));
    $crypto->setHistoricalHourlyFullResponse(Network::currency(), $currency, json_decode(file_get_contents(base_path('tests/fixtures/coingecko/historical_all.json')), true));
    $crypto->setPriceData(Network::currency(), json_decode(file_get_contents(base_path('tests/fixtures/coingecko/coin.json')), true));

    Http::fakeSequence('https://min-api.cryptocompare.com/*')
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-1.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-2.json')), true), 200);

    $priceData = $crypto->getPriceData(Network::currency());

    $this->artisan('explorer:cache-market-data-statistics');

    expect($cache->getPriceRangeDaily($currency))->toBe(['low' => 0.0339403, 'high' => 10.2219]);
    expect($cache->getPriceRange52($currency))->toBe(['low' => 0.23108521034764695, 'high' => 1.795718158629526]);

    expect($cache->getPriceAth($currency))->toBe([
        'timestamp' => Carbon::parse(Arr::get($priceData, 'market_data.ath_date.usd'))->timestamp,
        'value'     => Arr::get($priceData, 'market_data.ath.usd'),
    ]);

    expect($cache->getPriceAtl($currency))->toBe([
        'timestamp' => Carbon::parse(Arr::get($priceData, 'market_data.atl_date.usd'))->timestamp,
        'value'     => Arr::get($priceData, 'market_data.atl.usd'),
    ]);

    expect($cache->getVolumeAtl($currency))->toBe([
        'timestamp' => 1491609600,
        'value'     => 58232.28,
    ]);

    expect($cache->getVolumeAth($currency))->toBe([
        'timestamp' => 1698624000,
        'value'     => 443394014.01,
    ]);

    expect($cache->getMarketCapAtl($currency))->toBe(['timestamp' => 1490140800, 'value' => 3181903.0]);
    expect($cache->getMarketCapAth($currency))->toBe(['timestamp' => 1515542400, 'value' => 1001554886.9196]);

    Event::assertDispatchedTimes(MarketData::class, 1);
});

it('should cache volume statistics for multiple currencies', function () {
    Event::fake();

    Config::set('arkscan.networks.development.canBeExchanged', true);
    $cache  = new StatisticsCache();
    $crypto = new CryptoDataCache();

    Config::set('currencies', [
        'aud' => [
            'currency' => 'AUD',
            'locale'   => 'en_AU',
            'symbol'   => '$',
        ],

        'usd' => config('currencies.usd'),
    ]);

    Http::fakeSequence('https://min-api.cryptocompare.com/*')
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-AUD-page-1.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-AUD-page-2.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-1.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-2.json')), true), 200);

    foreach (['AUD', 'USD'] as $currency) {
        $crypto->setHistoricalFullResponse(Network::currency(), $currency, json_decode(file_get_contents(base_path('tests/fixtures/coingecko/historical_all.json')), true));
        $crypto->setHistoricalHourlyFullResponse(Network::currency(), $currency, json_decode(file_get_contents(base_path('tests/fixtures/coingecko/historical_all.json')), true));
        $crypto->setPriceData(Network::currency(), json_decode(file_get_contents(base_path('tests/fixtures/coingecko/coin.json')), true));
    }

    $priceData = $crypto->getPriceData(Network::currency());

    $this->artisan('explorer:cache-market-data-statistics');

    expect($cache->getPriceRangeDaily('USD'))->toBe(['low' => 0.0339403, 'high' => 10.2219]);
    expect($cache->getPriceRange52('USD'))->toBe(['low' => 0.23108521034764695, 'high' => 1.795718158629526]);

    expect($cache->getPriceAth('USD'))->toBe([
        'timestamp' => Carbon::parse(Arr::get($priceData, 'market_data.ath_date.usd'))->timestamp,
        'value'     => Arr::get($priceData, 'market_data.ath.usd'),
    ]);

    expect($cache->getPriceAtl('USD'))->toBe([
        'timestamp' => Carbon::parse(Arr::get($priceData, 'market_data.atl_date.usd'))->timestamp,
        'value'     => Arr::get($priceData, 'market_data.atl.usd'),
    ]);

    expect($cache->getVolumeAtl('USD'))->toBe([
        'timestamp' => 1491609600,
        'value'     => 58232.28,
    ]);

    expect($cache->getVolumeAth('USD'))->toBe([
        'timestamp' => 1698624000,
        'value'     => 443394014.01,
    ]);

    expect($cache->getMarketCapAtl('USD'))->toBe(['timestamp' => 1490140800, 'value' => 3181903.0]);
    expect($cache->getMarketCapAth('USD'))->toBe(['timestamp' => 1515542400, 'value' => 1001554886.9196]);

    expect($cache->getPriceRangeDaily('AUD'))->toBe(['low' => 0.0339403, 'high' => 10.2219]);
    expect($cache->getPriceRange52('AUD'))->toBe(['low' => 0.23108521034764695, 'high' => 1.795718158629526]);

    expect($cache->getPriceAth('AUD'))->toBe([
        'timestamp' => Carbon::parse(Arr::get($priceData, 'market_data.ath_date.aud'))->timestamp,
        'value'     => Arr::get($priceData, 'market_data.ath.aud'),
    ]);

    expect($cache->getPriceAtl('AUD'))->toBe([
        'timestamp' => Carbon::parse(Arr::get($priceData, 'market_data.atl_date.aud'))->timestamp,
        'value'     => Arr::get($priceData, 'market_data.atl.aud'),
    ]);

    expect($cache->getVolumeAtl('AUD'))->toBe([
        'timestamp' => 1490054400,
        'value'     => 6695.21,
    ]);

    expect($cache->getVolumeAth('AUD'))->toBe([
        'timestamp' => 1698624000,
        'value'     => 698419508.98,
    ]);

    expect($cache->getMarketCapAtl('AUD'))->toBe(['timestamp' => 1490140800, 'value' => 3181903.0]);
    expect($cache->getMarketCapAth('AUD'))->toBe(['timestamp' => 1515542400, 'value' => 1001554886.9196]);

    Event::assertDispatchedTimes(MarketData::class, 1);
});

it('should exit early if network cannot be exchanged', function () {
    Event::fake();

    Config::set('arkscan.networks.development.canBeExchanged', false);

    $cache    = new StatisticsCache();
    $currency = 'USD';

    $this->artisan('explorer:cache-market-data-statistics');

    expect($cache->getPriceRangeDaily($currency))->toBe(null);
    expect($cache->getPriceRange52($currency))->toBe(null);
    expect($cache->getPriceAth($currency))->toBe(null);
    expect($cache->getPriceAtl($currency))->toBe(null);

    expect($cache->getVolumeAtl($currency))->toBe(null);
    expect($cache->getVolumeAth($currency))->toBe(null);

    expect($cache->getMarketCapAtl($currency))->toBe(null);
    expect($cache->getMarketCapAth($currency))->toBe(null);

    Event::assertDispatchedTimes(MarketData::class, 0);
});

it('should handle null scenarios for statistics', function () {
    Event::fake();

    Http::fake([
        'https://min-api.cryptocompare.com/*' => Http::response([]),
    ]);

    Config::set('arkscan.networks.development.canBeExchanged', true);
    $cache = new StatisticsCache();

    $currency = 'USD';

    $this->artisan('explorer:cache-market-data-statistics');

    expect($cache->getPriceRangeDaily($currency))->toBe(null);
    expect($cache->getPriceRange52($currency))->toBe(null);
    expect($cache->getPriceAth($currency))->toBe(null);
    expect($cache->getPriceAtl($currency))->toBe(null);

    expect($cache->getVolumeAtl($currency))->toBe(null);
    expect($cache->getVolumeAth($currency))->toBe(null);

    expect($cache->getMarketCapAtl($currency))->toBe(null);
    expect($cache->getMarketCapAth($currency))->toBe(null);

    Event::assertDispatchedTimes(MarketData::class, 0);
});

it('should should not dispatch event if no changes', function () {
    Event::fake();

    Config::set('arkscan.networks.development.canBeExchanged', true);
    $crypto = new CryptoDataCache();

    $currency = 'USD';
    $crypto->setHistoricalFullResponse(Network::currency(), $currency, json_decode(file_get_contents(base_path('tests/fixtures/coingecko/historical_all.json')), true));
    $crypto->setHistoricalHourlyFullResponse(Network::currency(), $currency, json_decode(file_get_contents(base_path('tests/fixtures/coingecko/historical_all.json')), true));
    $crypto->setPriceData(Network::currency(), json_decode(file_get_contents(base_path('tests/fixtures/coingecko/coin.json')), true));

    Http::fakeSequence('https://min-api.cryptocompare.com/*')
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-1.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-2.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-1.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-2.json')), true), 200);

    $this->artisan('explorer:cache-market-data-statistics');

    Event::assertDispatchedTimes(MarketData::class, 1);

    Event::fake();

    $this->artisan('explorer:cache-market-data-statistics');

    Event::assertDispatchedTimes(MarketData::class, 0);
});

it('should should dispatch event if price atl changes', function () {
    Event::fake();

    Config::set('arkscan.networks.development.canBeExchanged', true);
    $cache  = new StatisticsCache();
    $crypto = new CryptoDataCache();

    $currency = 'USD';
    $crypto->setHistoricalFullResponse(Network::currency(), $currency, json_decode(file_get_contents(base_path('tests/fixtures/coingecko/historical_all.json')), true));
    $crypto->setHistoricalHourlyFullResponse(Network::currency(), $currency, json_decode(file_get_contents(base_path('tests/fixtures/coingecko/historical_all.json')), true));
    $crypto->setPriceData(Network::currency(), json_decode(file_get_contents(base_path('tests/fixtures/coingecko/coin.json')), true));

    Http::fakeSequence('https://min-api.cryptocompare.com/*')
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-1.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-2.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-1.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-2.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-1.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-2.json')), true), 200);

    $this->artisan('explorer:cache-market-data-statistics');

    Event::assertDispatchedTimes(MarketData::class, 1);

    Event::fake();

    $cache->setPriceAtl($currency, 1490140800, 0.339403);

    $this->artisan('explorer:cache-market-data-statistics');

    Event::assertDispatchedTimes(MarketData::class, 1);

    Event::fake();

    $cache->setPriceAtl($currency, 1490140900, 0.0339403);

    $this->artisan('explorer:cache-market-data-statistics');

    Event::assertDispatchedTimes(MarketData::class, 1);
});

it('should should dispatch event if price ath changes', function () {
    Event::fake();

    Config::set('arkscan.networks.development.canBeExchanged', true);
    $cache  = new StatisticsCache();
    $crypto = new CryptoDataCache();

    $currency = 'USD';
    $crypto->setHistoricalFullResponse(Network::currency(), $currency, json_decode(file_get_contents(base_path('tests/fixtures/coingecko/historical_all.json')), true));
    $crypto->setHistoricalHourlyFullResponse(Network::currency(), $currency, json_decode(file_get_contents(base_path('tests/fixtures/coingecko/historical_all.json')), true));
    $crypto->setPriceData(Network::currency(), json_decode(file_get_contents(base_path('tests/fixtures/coingecko/coin.json')), true));

    Http::fakeSequence('https://min-api.cryptocompare.com/*')
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-1.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-2.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-1.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-2.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-1.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-2.json')), true), 200);

    $this->artisan('explorer:cache-market-data-statistics');

    Event::assertDispatchedTimes(MarketData::class, 1);

    Event::fake();

    $cache->setPriceAth($currency, 1515542400, 20.2219);

    $this->artisan('explorer:cache-market-data-statistics');

    Event::assertDispatchedTimes(MarketData::class, 1);

    Event::fake();

    $cache->setPriceAth($currency, 1515542500, 10.2219);

    $this->artisan('explorer:cache-market-data-statistics');

    Event::assertDispatchedTimes(MarketData::class, 1);
});

it('should should dispatch event if 52 week range changes', function () {
    Event::fake();

    Config::set('arkscan.networks.development.canBeExchanged', true);
    $cache  = new StatisticsCache();
    $crypto = new CryptoDataCache();

    $currency = 'USD';
    $crypto->setHistoricalFullResponse(Network::currency(), $currency, json_decode(file_get_contents(base_path('tests/fixtures/coingecko/historical_all.json')), true));
    $crypto->setHistoricalHourlyFullResponse(Network::currency(), $currency, json_decode(file_get_contents(base_path('tests/fixtures/coingecko/historical_all.json')), true));
    $crypto->setPriceData(Network::currency(), json_decode(file_get_contents(base_path('tests/fixtures/coingecko/coin.json')), true));

    Http::fakeSequence('https://min-api.cryptocompare.com/*')
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-1.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-2.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-1.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-2.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-1.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-2.json')), true), 200);

    $this->artisan('explorer:cache-market-data-statistics');

    Event::assertDispatchedTimes(MarketData::class, 1);

    $baseValues = $cache->getPriceRange52($currency);

    Event::fake();

    $cache->setPriceRange52($currency, 0.2221350324167072, $baseValues['high']);

    $this->artisan('explorer:cache-market-data-statistics');

    Event::assertDispatchedTimes(MarketData::class, 1);

    Event::fake();

    $cache->setPriceRange52($currency, $baseValues['low'], 2.795718158629526);

    $this->artisan('explorer:cache-market-data-statistics');

    Event::assertDispatchedTimes(MarketData::class, 1);
});

it('should should dispatch event if daily range changes', function () {
    Event::fake();

    Config::set('arkscan.networks.development.canBeExchanged', true);
    $cache  = new StatisticsCache();
    $crypto = new CryptoDataCache();

    $currency = 'USD';
    $crypto->setHistoricalFullResponse(Network::currency(), $currency, json_decode(file_get_contents(base_path('tests/fixtures/coingecko/historical_all.json')), true));
    $crypto->setHistoricalHourlyFullResponse(Network::currency(), $currency, json_decode(file_get_contents(base_path('tests/fixtures/coingecko/historical_all.json')), true));
    $crypto->setPriceData(Network::currency(), json_decode(file_get_contents(base_path('tests/fixtures/coingecko/coin.json')), true));

    Http::fakeSequence('https://min-api.cryptocompare.com/*')
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-1.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-2.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-1.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-2.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-1.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-2.json')), true), 200);

    $this->artisan('explorer:cache-market-data-statistics');

    Event::assertDispatchedTimes(MarketData::class, 1);

    Event::fake();

    $cache->setPriceRangeDaily($currency, 0.0339403, 20.2219);

    $this->artisan('explorer:cache-market-data-statistics');

    Event::assertDispatchedTimes(MarketData::class, 1);

    Event::fake();

    $cache->setPriceRangeDaily($currency, 0.339403, 10.2219);

    $this->artisan('explorer:cache-market-data-statistics');

    Event::assertDispatchedTimes(MarketData::class, 1);
});

it('should should dispatch event if volume atl changes', function () {
    Event::fake();

    Config::set('arkscan.networks.development.canBeExchanged', true);
    $cache  = new StatisticsCache();
    $crypto = new CryptoDataCache();

    $currency = 'USD';
    $crypto->setHistoricalFullResponse(Network::currency(), $currency, json_decode(file_get_contents(base_path('tests/fixtures/coingecko/historical_all.json')), true));
    $crypto->setHistoricalHourlyFullResponse(Network::currency(), $currency, json_decode(file_get_contents(base_path('tests/fixtures/coingecko/historical_all.json')), true));
    $crypto->setPriceData(Network::currency(), json_decode(file_get_contents(base_path('tests/fixtures/coingecko/coin.json')), true));

    Http::fakeSequence('https://min-api.cryptocompare.com/*')
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-1.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-2.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-1.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-2.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-1.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-2.json')), true), 200);

    $this->artisan('explorer:cache-market-data-statistics');

    $allTimeLow = $cache->getVolumeAtl($currency);

    Event::assertDispatchedTimes(MarketData::class, 1);

    Event::fake();

    $cache->setVolumeAtl($currency, $allTimeLow['timestamp'], 50548.95038391039);

    $cache->setLastExchangeVolumeUpdate($currency, now()->sub('day', 2));

    $this->artisan('explorer:cache-market-data-statistics');

    $allTimeLow = $cache->getVolumeAtl($currency);

    Event::assertDispatchedTimes(MarketData::class, 1);

    Event::fake();

    $cache->setVolumeAtl($currency, 1688774500, $allTimeLow['value']);

    $cache->setLastExchangeVolumeUpdate($currency, now()->sub('day', 2));

    $this->artisan('explorer:cache-market-data-statistics');

    Event::assertDispatchedTimes(MarketData::class, 1);
});

it('should should dispatch event if volume ath changes', function () {
    Event::fake();

    Config::set('arkscan.networks.development.canBeExchanged', true);
    $cache  = new StatisticsCache();
    $crypto = new CryptoDataCache();

    $currency = 'USD';
    $crypto->setHistoricalFullResponse(Network::currency(), $currency, json_decode(file_get_contents(base_path('tests/fixtures/coingecko/historical_all.json')), true));
    $crypto->setHistoricalHourlyFullResponse(Network::currency(), $currency, json_decode(file_get_contents(base_path('tests/fixtures/coingecko/historical_all.json')), true));
    $crypto->setPriceData(Network::currency(), json_decode(file_get_contents(base_path('tests/fixtures/coingecko/coin.json')), true));

    Http::fakeSequence('https://min-api.cryptocompare.com/*')
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-1.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-2.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-1.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-2.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-1.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-2.json')), true), 200);

    $this->artisan('explorer:cache-market-data-statistics');

    $allTimeHigh = $cache->getVolumeAth($currency);

    Event::assertDispatchedTimes(MarketData::class, 1);

    Event::fake();

    $cache->setVolumeAth($currency, $allTimeHigh['timestamp'], 543956833.91000223);

    $cache->setLastExchangeVolumeUpdate($currency, now()->sub('day', 2));

    $this->artisan('explorer:cache-market-data-statistics');

    $allTimeHigh = $cache->getVolumeAth($currency);

    Event::assertDispatchedTimes(MarketData::class, 1);

    Event::fake();

    $cache->setVolumeAth($currency, 1698710500, $allTimeHigh['value']);

    $cache->setLastExchangeVolumeUpdate($currency, now()->sub('day', 2));

    $this->artisan('explorer:cache-market-data-statistics');

    Event::assertDispatchedTimes(MarketData::class, 1);
});

it('should not update exchange volume for a currency within 24 hours', function () {
    $this->freezeTime();

    $timestamp = now()->format('Y-m-d H:i:s');

    Event::fake();

    Config::set('arkscan.networks.development.canBeExchanged', true);
    $cache  = new StatisticsCache();
    $crypto = new CryptoDataCache();

    $currency = 'USD';
    $crypto->setHistoricalFullResponse(Network::currency(), $currency, json_decode(file_get_contents(base_path('tests/fixtures/coingecko/historical_all.json')), true));
    $crypto->setHistoricalHourlyFullResponse(Network::currency(), $currency, json_decode(file_get_contents(base_path('tests/fixtures/coingecko/historical_all.json')), true));
    $crypto->setPriceData(Network::currency(), json_decode(file_get_contents(base_path('tests/fixtures/coingecko/coin.json')), true));

    Http::fakeSequence('https://min-api.cryptocompare.com/*')
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-1.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-2.json')), true), 200);

    expect($cache->getLastExchangeVolumeUpdate($currency))->toBeNull();

    $this->artisan('explorer:cache-market-data-statistics');

    Event::assertDispatchedTimes(MarketData::class, 1);

    Event::fake();

    expect($cache->getLastExchangeVolumeUpdate($currency)->format('Y-m-d H:i:s'))->toBe($timestamp);

    $this->travel(1)->hour();

    $this->artisan('explorer:cache-market-data-statistics');

    Event::assertDispatchedTimes(MarketData::class, 0);

    expect($cache->getLastExchangeVolumeUpdate($currency)->format('Y-m-d H:i:s'))->toBe($timestamp);
});

it('should not update exchange volume if there is no data', function () {
    $this->freezeTime();

    Event::fake();

    Config::set('arkscan.networks.development.canBeExchanged', true);
    $cache  = new StatisticsCache();
    $crypto = new CryptoDataCache();

    $currency = 'USD';
    $crypto->setHistoricalFullResponse(Network::currency(), $currency, json_decode(file_get_contents(base_path('tests/fixtures/coingecko/historical_all.json')), true));
    $crypto->setHistoricalHourlyFullResponse(Network::currency(), $currency, json_decode(file_get_contents(base_path('tests/fixtures/coingecko/historical_all.json')), true));
    $crypto->setPriceData(Network::currency(), json_decode(file_get_contents(base_path('tests/fixtures/coingecko/coin.json')), true));

    Http::fake([
        'https://min-api.cryptocompare.com/*' => Http::response([]),
    ]);

    expect($cache->getLastExchangeVolumeUpdate($currency))->toBeNull();

    $this->artisan('explorer:cache-market-data-statistics');

    expect($cache->getLastExchangeVolumeUpdate($currency))->toBeNull();
});

it('should should dispatch event if market cap atl changes', function () {
    Event::fake();

    Config::set('arkscan.networks.development.canBeExchanged', true);
    $cache  = new StatisticsCache();
    $crypto = new CryptoDataCache();

    $currency = 'USD';
    $crypto->setHistoricalFullResponse(Network::currency(), $currency, json_decode(file_get_contents(base_path('tests/fixtures/coingecko/historical_all.json')), true));
    $crypto->setHistoricalHourlyFullResponse(Network::currency(), $currency, json_decode(file_get_contents(base_path('tests/fixtures/coingecko/historical_all.json')), true));
    $crypto->setPriceData(Network::currency(), json_decode(file_get_contents(base_path('tests/fixtures/coingecko/coin.json')), true));

    Http::fakeSequence('https://min-api.cryptocompare.com/*')
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-1.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-2.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-1.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-2.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-1.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-2.json')), true), 200);

    $this->artisan('explorer:cache-market-data-statistics');

    Event::assertDispatchedTimes(MarketData::class, 1);

    Event::fake();

    $cache->setMarketCapAtl($currency, 1490140800, 4181903.0);

    $this->artisan('explorer:cache-market-data-statistics');

    Event::assertDispatchedTimes(MarketData::class, 1);

    Event::fake();

    $cache->setMarketCapAtl($currency, 1490140900, 3181903.0);

    $this->artisan('explorer:cache-market-data-statistics');

    Event::assertDispatchedTimes(MarketData::class, 1);
});

it('should should dispatch event if market cap ath changes', function () {
    Event::fake();

    Config::set('arkscan.networks.development.canBeExchanged', true);
    $cache  = new StatisticsCache();
    $crypto = new CryptoDataCache();

    $currency = 'USD';
    $crypto->setHistoricalFullResponse(Network::currency(), $currency, json_decode(file_get_contents(base_path('tests/fixtures/coingecko/historical_all.json')), true));
    $crypto->setHistoricalHourlyFullResponse(Network::currency(), $currency, json_decode(file_get_contents(base_path('tests/fixtures/coingecko/historical_all.json')), true));
    $crypto->setPriceData(Network::currency(), json_decode(file_get_contents(base_path('tests/fixtures/coingecko/coin.json')), true));

    Http::fakeSequence('https://min-api.cryptocompare.com/*')
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-1.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-2.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-1.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-2.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-1.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-2.json')), true), 200);

    $this->artisan('explorer:cache-market-data-statistics');

    Event::assertDispatchedTimes(MarketData::class, 1);

    Event::fake();

    $cache->setMarketCapAth($currency, 1515542400, 2001554886.9196);

    $this->artisan('explorer:cache-market-data-statistics');

    Event::assertDispatchedTimes(MarketData::class, 1);

    Event::fake();

    $cache->setMarketCapAth($currency, 1515542500, 1001554886.9196);

    $this->artisan('explorer:cache-market-data-statistics');

    Event::assertDispatchedTimes(MarketData::class, 1);
});
