<?php

declare(strict_types=1);

use App\Services\MarketDataProviders\CoinGecko;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use function Spatie\Snapshots\assertMatchesSnapshot;

it('should fetch the price data for the given collection', function () {
    Http::fake([
        'api.coingecko.com/*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/coingecko/coin.json')), true), 200),
    ]);

    $dto = (new CoinGecko())->priceAndPriceChange('ARK', collect(['USD']))->get('USD');
    expect($dto->priceChange())->toEqual(-0.0466092);
    expect($dto->price())->toEqual(1.63);
});

it('should fetch the historical prices for the given pair', function () {
    Http::fake([
        'api.coingecko.com/*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/coingecko/market_data.json')), true), 200),
    ]);

    assertMatchesSnapshot((new CoinGecko())->historical('ARK', 'USD'));
});

it('should return an empty value if empty response for historical', function () {
    Http::fake([
        'api.coingecko.com/*' => Http::response(null, 200),
    ]);

    expect((new CoinGecko())->historical('ARK', 'USD'))->toEqual(collect());
});

it('should fetch the historical prices per hour for the given pair', function () {
    Http::fake([
        'api.coingecko.com/*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/coingecko/market_data_1_day.json')), true), 200),
    ]);

    assertMatchesSnapshot((new CoinGecko())->historicalHourly('ARK', 'USD'));
});

it('should return an empty value if empty response for historical hourly', function () {
    Http::fake([
        'api.coingecko.com/*' => Http::response(null, 200),
    ]);

    expect((new CoinGecko())->historicalHourly('ARK', 'USD'))->toEqual(collect());
});

it('should throw an exception after 30 empty responses', function () {
    Http::fake([
        'api.coingecko.com/*' => Http::response(null, 200),
    ]);

    Cache::set('coin_gecko_response_error', 30);

    $this->expectException(\Exception::class);

    (new CoinGecko())->historicalHourly('ARK', 'USD');
});
