<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Services\Cache\CryptoDataCache;
use App\Services\Cache\StatisticsCache;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    $this->travelTo('2024-08-01 01:00:00');
});

it('should cache market data statistics', function () {
    Config::set('arkscan.networks.development.canBeExchanged', true);
    $cache  = new StatisticsCache();
    $crypto = new CryptoDataCache();

    $currency = 'USD';
    $crypto->setHistoricalFullResponse(Network::currency(), $currency, json_decode(file_get_contents(base_path('tests/fixtures/coingecko/historical_all.json')), true));
    $crypto->setHistoricalHourlyFullResponse(Network::currency(), $currency, json_decode(file_get_contents(base_path('tests/fixtures/coingecko/historical_all.json')), true));

    $this->artisan('explorer:cache-market-data-statistics');

    expect($cache->getPriceRangeDaily($currency))->toBe(['low' => 0.0339403, 'high' => 10.2219]);
    expect($cache->getPriceRange52($currency))->toBe(['low' => 0.23108521034764695, 'high' => 1.795718158629526]);
    expect($cache->getPriceAth($currency))->toBe(['timestamp' => 1515542400, 'value' => 10.2219]);
    expect($cache->getPriceAtl($currency))->toBe(['timestamp' => 1490140800, 'value' => 0.0339403]);

    expect($cache->getVolumeAtl($currency))->toBe(['timestamp' => 1688774400, 'value' => 40548.95038391039]);
    expect($cache->getVolumeAth($currency))->toBe(['timestamp' => 1698710400, 'value' => 443956833.91000223]);

    expect($cache->getMarketCapAtl($currency))->toBe(['timestamp' => 1490140800, 'value' => 3181903.0]);
    expect($cache->getMarketCapAth($currency))->toBe(['timestamp' => 1515542400, 'value' => 1001554886.9196]);
});

it('should exit early if network cannot be exchanged', function () {
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
});

it('should handle null scenarios for statistics', function () {
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
});
