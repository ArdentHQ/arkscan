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

beforeEach(function () {
    $this->travelTo('2024-08-01 01:00:00');
});

it('should cache market data statistics', function () {
    $this->travelTo('2024-06-24 16:55:23');

    Event::fake();

    Config::set('arkscan.networks.development.canBeExchanged', true);
    $cache  = new StatisticsCache();
    $crypto = new CryptoDataCache();

    $currency = 'USD';
    $crypto->setHistoricalFullResponse(Network::currency(), $currency, json_decode(file_get_contents(base_path('tests/fixtures/coingecko/historical_all.json')), true));
    $crypto->setHistoricalHourlyFullResponse(Network::currency(), $currency, json_decode(file_get_contents(base_path('tests/fixtures/coingecko/historical_all.json')), true));
    $crypto->setPriceData(Network::currency(), json_decode(file_get_contents(base_path('tests/fixtures/coingecko/coin.json')), true));

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

    expect($cache->getVolumeAtl($currency))->toBe(['timestamp' => 1688774400, 'value' => 40548.95038391039]);
    expect($cache->getVolumeAth($currency))->toBe(['timestamp' => 1698710400, 'value' => 443956833.91000223]);

    expect($cache->getMarketCapAtl($currency))->toBe(['timestamp' => 1490140800, 'value' => 3181903.0]);
    expect($cache->getMarketCapAth($currency))->toBe(['timestamp' => 1515542400, 'value' => 1001554886.9196]);

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

    $this->artisan('explorer:cache-market-data-statistics');

    Event::assertDispatchedTimes(MarketData::class, 1);

    Event::fake();

    $existingAtl = $cache->getVolumeAtl($currency);

    $cache->setVolumeAtl($currency, 1688774400, $existingAtl['value'] + 1);

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

    $this->artisan('explorer:cache-market-data-statistics');

    Event::assertDispatchedTimes(MarketData::class, 1);

    Event::fake();

    $existingAth = $cache->getVolumeAth($currency);

    $cache->setVolumeAth($currency, 1698710400, $existingAth['value'] - 1);

    $this->artisan('explorer:cache-market-data-statistics');

    Event::assertDispatchedTimes(MarketData::class, 1);
});

it('should should dispatch event if market cap atl value changes', function () {
    Event::fake();

    Config::set('arkscan.networks.development.canBeExchanged', true);
    $cache  = new StatisticsCache();
    $crypto = new CryptoDataCache();

    $currency = 'USD';
    $crypto->setHistoricalFullResponse(Network::currency(), $currency, json_decode(file_get_contents(base_path('tests/fixtures/coingecko/historical_all.json')), true));
    $crypto->setHistoricalHourlyFullResponse(Network::currency(), $currency, json_decode(file_get_contents(base_path('tests/fixtures/coingecko/historical_all.json')), true));
    $crypto->setPriceData(Network::currency(), json_decode(file_get_contents(base_path('tests/fixtures/coingecko/coin.json')), true));

    $this->artisan('explorer:cache-market-data-statistics');

    Event::assertDispatchedTimes(MarketData::class, 1);

    Event::fake();

    $existingAtl = $cache->getMarketCapAth($currency);

    $cache->setMarketCapAtl($currency, 1490140800, $existingAtl['value'] + 1);

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

    $this->artisan('explorer:cache-market-data-statistics');

    Event::assertDispatchedTimes(MarketData::class, 1);

    Event::fake();

    $existingAth = $cache->getMarketCapAth($currency);

    $cache->setMarketCapAth($currency, 1515542400, $existingAth['value'] - 1);

    $this->artisan('explorer:cache-market-data-statistics');

    Event::assertDispatchedTimes(MarketData::class, 1);

    Event::fake();
});
