<?php

declare(strict_types=1);

use App\Services\MarketDataProviders\CoinGecko;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use function Spatie\Snapshots\assertMatchesSnapshot;

it('should fetch the price data for the given collection', function () {
    Http::fake([
        'api.coingecko.com/*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/coingecko/coin.json')), true), 200),
    ]);

    $dto = (new CoinGecko())->priceAndPriceChange('ARK', collect(['USD']))->get('USD');
    expect(number_format($dto->priceChange(), 7))->toEqual(-0.0466092);
    expect($dto->price())->toEqual(1.63);
});

it('should return an empty value if failed response for price data', function () {
    expect((new CoinGecko())->priceAndPriceChange('ARK', collect(['USD'])))->toEqual(collect());
});

it('should return an empty value if empty response for price data', function () {
    Http::fake([
        'api.coingecko.com/*' => Http::response(null, 200),
    ]);

    expect((new CoinGecko())->priceAndPriceChange('ARK', collect(['USD'])))->toEqual(collect());
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

it('should return an empty value if failed response for historical', function () {
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

it('should return an empty value if failed response for historical hourly', function () {
    expect((new CoinGecko())->historicalHourly('ARK', 'USD'))->toEqual(collect());
});

it('should reset exception trigger for empty responses', function ($attempt) {
    Http::fake([
        'api.coingecko.com/*' => Http::response(null, 200),
    ]);

    Config::set('explorer.coingecko_exception_frequency', 6);

    Cache::set('coingecko_response_error', (($attempt - 1) % 6) + 1);

    if (($attempt % 6) === 0) {
        $this->expectException(\Exception::class);
    } else {
        $this->expectNotToPerformAssertions();
    }

    (new CoinGecko())->historicalHourly('ARK', 'USD');
})->with(range(1, 12));

it('should trigger exception for throttled requests', function ($attempt) {
    Http::fake([
        'api.coingecko.com/*' => Http::response([
            'status' => [
                'error_code' => 1,
            ],
        ], 500),
    ]);

    Config::set('explorer.coingecko_exception_frequency', 6);

    Cache::set('coingecko_response_error', (($attempt - 1) % 6) + 1);

    if (($attempt % 6) === 0) {
        $this->expectException(\Exception::class);
    } else {
        $this->expectNotToPerformAssertions();
    }

    (new CoinGecko())->historicalHourly('ARK', 'USD');
})->with(range(1, 12));
