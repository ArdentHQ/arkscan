<?php

declare(strict_types=1);

use App\Contracts\MarketDataProvider;
use App\Services\Cache\StatisticsCache;
use Illuminate\Support\Facades\Config;

it('should cache market data statistics', function () {
    Config::set('arkscan.networks.development.canBeExchanged', true);
    $cache = new StatisticsCache();

    $this->mock(MarketDataProvider::class)
        ->shouldReceive('historicalAll')
        ->andReturn(json_decode(file_get_contents(base_path('tests/fixtures/coingecko/historical_all.json')), true));

    $this->artisan('explorer:cache-market-data-statistics');

    expect($cache->getPriceRangeDaily())->toBe(['low' => 0.0339403, 'high' => 10.2219]);
    expect($cache->getPriceRange52())->toBe(['low' => 0.2221350324167072, 'high' => 1.795718158629526]);
    expect($cache->getPriceAth())->toBe(['timestamp' => 1515542400, 'value' => 10.2219]);
    expect($cache->getPriceAtl())->toBe(['timestamp' => 1490140800, 'value' => 0.0339403]);

    expect($cache->getVolumeAtl())->toBe(['timestamp' => 1688774400, 'value' => 40548.95038391039]);
    expect($cache->getVolumeAth())->toBe(['timestamp' => 1698710400, 'value' => 443956833.91000223]);

    expect($cache->getMarketCapAtl())->toBe(['timestamp' => 1490140800, 'value' => 3181903.0]);
    expect($cache->getMarketCapAth())->toBe(['timestamp' => 1515542400, 'value' => 1001554886.9196]);
});

it('should exit early if network cannot be exchanged', function () {
    Config::set('arkscan.networks.development.canBeExchanged', false);

    $cache = new StatisticsCache();

    $this->artisan('explorer:cache-market-data-statistics');

    expect($cache->getPriceRangeDaily())->toBe(null);
    expect($cache->getPriceRange52())->toBe(null);
    expect($cache->getPriceAth())->toBe(null);
    expect($cache->getPriceAtl())->toBe(null);

    expect($cache->getVolumeAtl())->toBe(null);
    expect($cache->getVolumeAth())->toBe(null);

    expect($cache->getMarketCapAtl())->toBe(null);
    expect($cache->getMarketCapAth())->toBe(null);
});

it('should handle null scenarios for statistics', function () {
    Config::set('arkscan.networks.development.canBeExchanged', true);
    $cache = new StatisticsCache();

    $this->mock(MarketDataProvider::class)->shouldReceive('historicalAll')->andReturn([]);

    $this->artisan('explorer:cache-market-data-statistics');

    expect($cache->getPriceRangeDaily())->toBe(null);
    expect($cache->getPriceRange52())->toBe(null);
    expect($cache->getPriceAth())->toBe(null);
    expect($cache->getPriceAtl())->toBe(null);

    expect($cache->getVolumeAtl())->toBe(null);
    expect($cache->getVolumeAth())->toBe(null);

    expect($cache->getMarketCapAtl())->toBe(null);
    expect($cache->getMarketCapAth())->toBe(null);
});
