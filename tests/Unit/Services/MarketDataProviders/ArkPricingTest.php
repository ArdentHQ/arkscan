<?php

declare(strict_types=1);

use App\Exceptions\MarketDataThrottledException;
use App\Models\Exchange;
use App\Services\MarketDataProviders\ArkPricing;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

it('should fetch the price data for the given collection', function () {
    Http::fake([
        'ark-pricing.localhost/api/v1/coins/ark/price*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/ark-pricing/price.json')), true), 200),
    ]);

    $prices = (new ArkPricing())->priceAndPriceChange('ARK', collect(['USD', 'EUR']));

    expect($prices->get('USD')->price())->toEqual(0.412);
    expect($prices->get('USD')->priceChange())->toEqual(0.0231);
    expect($prices->get('EUR')->price())->toEqual(0.381);
    expect($prices->get('EUR')->priceChange())->toEqual(0.0218);
});

it('should only return the requested currencies for price data', function () {
    Http::fake([
        'ark-pricing.localhost/api/v1/coins/ark/price*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/ark-pricing/price.json')), true), 200),
    ]);

    $prices = (new ArkPricing())->priceAndPriceChange('ARK', collect(['USD']));

    expect($prices->keys()->all())->toEqual(['USD']);
});

it('should return an empty value if failed response for price data', function () {
    expect((new ArkPricing())->priceAndPriceChange('ARK', collect(['USD'])))->toEqual(collect());
});

it('should return an empty value if empty response for price data', function () {
    Http::fake([
        'ark-pricing.localhost/*' => Http::response(null, 200),
    ]);

    expect((new ArkPricing())->priceAndPriceChange('ARK', collect(['USD'])))->toEqual(collect());
});

it('should fetch the historical prices for the given pair', function () {
    Http::fake([
        'ark-pricing.localhost/api/v1/coins/ark/history*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/ark-pricing/history-day.json')), true), 200),
    ]);

    expect((new ArkPricing())->historical('ARK', 'USD')->all())->toEqual([
        '2024-01-13' => 0.401,
        '2024-01-14' => 0.408,
        '2024-01-15' => 0.412,
    ]);
});

it('should store the daily market chart series when fetching historical prices', function () {
    Http::fake([
        'ark-pricing.localhost/api/v1/coins/ark/history*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/ark-pricing/history-day.json')), true), 200),
    ]);

    $provider = new ArkPricing();

    expect($provider->marketChart('ARK', 'USD'))->toEqual([]);

    $provider->historical('ARK', 'USD');

    $chart = $provider->marketChart('ARK', 'USD');

    expect($chart['prices'])->toHaveCount(3);
    expect($chart['prices'][0][1])->toEqual(0.401);
    expect($chart['market_caps'][2][1])->toEqual(41200000.0);
    expect($chart['total_volumes'][2][1])->toEqual(1230000.0);
});

it('should return an empty value if empty response for historical', function () {
    Http::fake([
        'ark-pricing.localhost/*' => Http::response(null, 200),
    ]);

    expect((new ArkPricing())->historical('ARK', 'USD'))->toEqual(collect());
});

it('should return an empty value if failed response for historical', function () {
    expect((new ArkPricing())->historical('ARK', 'USD'))->toEqual(collect());
});

it('should fetch the historical prices per hour for the given pair', function () {
    Http::fake([
        'ark-pricing.localhost/api/v1/coins/ark/history*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/ark-pricing/history-hour.json')), true), 200),
    ]);

    expect((new ArkPricing())->historicalHourly('ARK', 'USD')->all())->toEqual([
        '2024-01-15 10:00:00' => 0.407,
        '2024-01-15 11:00:00' => 0.41,
        '2024-01-15 12:00:00' => 0.412,
    ]);
});

it('should store the hourly market chart series when fetching hourly prices', function () {
    Http::fake([
        'ark-pricing.localhost/api/v1/coins/ark/history*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/ark-pricing/history-hour.json')), true), 200),
    ]);

    $provider = new ArkPricing();

    expect($provider->marketChartHourly('ARK', 'USD'))->toEqual([]);

    $provider->historicalHourly('ARK', 'USD');

    $chart = $provider->marketChartHourly('ARK', 'USD');

    expect($chart['prices'])->toHaveCount(3);
    expect($chart['prices'][2][1])->toEqual(0.412);
    expect($chart['market_caps'][0][1])->toEqual(40700000.0);
    expect($chart['total_volumes'][1][1])->toEqual(1210000.0);
});

it('should return an empty value if empty response for historical hourly', function () {
    Http::fake([
        'ark-pricing.localhost/*' => Http::response(null, 200),
    ]);

    expect((new ArkPricing())->historicalHourly('ARK', 'USD'))->toEqual(collect());
});

it('should return an empty value if failed response for historical hourly', function () {
    expect((new ArkPricing())->historicalHourly('ARK', 'USD'))->toEqual(collect());
});

it('should fetch volume for the given network', function () {
    Http::fake([
        'ark-pricing.localhost/api/v1/coins/ark/market*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/ark-pricing/market.json')), true), 200),
    ]);

    expect((new ArkPricing())->volume('ARK'))->toEqual([
        'usd' => 1230000,
        'btc' => 29.13,
    ]);
});

it('should return empty array if empty response for volume', function () {
    Http::fake([
        'ark-pricing.localhost/*' => Http::response(null, 200),
    ]);

    expect((new ArkPricing())->volume('ARK'))->toEqual([]);
});

it('should fetch all-time high and low for the given currencies', function () {
    Http::fake([
        'ark-pricing.localhost/api/v1/coins/ark/market*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/ark-pricing/market.json')), true), 200),
    ]);

    $highLows = (new ArkPricing())->allTimeHighLow('ARK', collect(['USD', 'BTC']));

    expect($highLows->get('USD'))->toEqual([
        'ath' => ['value' => 10.91, 'timestamp' => Carbon\Carbon::parse('2018-01-10')->getTimestamp()],
        'atl' => ['value' => 0.0261, 'timestamp' => Carbon\Carbon::parse('2017-03-22')->getTimestamp()],
    ]);

    expect($highLows->get('BTC')['ath']['value'])->toEqual(0.0004901);
});

it('should return an empty collection if empty response for all-time high and low', function () {
    Http::fake([
        'ark-pricing.localhost/*' => Http::response(null, 200),
    ]);

    expect((new ArkPricing())->allTimeHighLow('ARK', collect(['USD'])))->toEqual(collect());
});

it('should fetch exchange details for the given exchange', function () {
    Artisan::call('migrate:fresh');

    $exchange = Exchange::factory()->create(['coingecko_id' => 'binance']);

    Http::fake([
        'ark-pricing.localhost/api/v1/exchanges/binance/tickers*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/ark-pricing/tickers.json')), true), 200),
    ]);

    expect((new ArkPricing())->exchangeDetails($exchange))->toEqual([
        'price'  => 0.412,
        'volume' => 1230000,
    ]);
});

it('should return empty array if failed response for volume', function () {
    expect((new ArkPricing())->volume('ARK'))->toEqual([]);
});

it('should return null all-time values when high and low data is missing', function () {
    Http::fake([
        'ark-pricing.localhost/api/v1/coins/ark/market*' => Http::response([
            'data' => [
                'coin' => 'ark',
                'USD'  => [
                    'price' => 0.412,
                    'ath'   => null,
                    'atl'   => ['price' => null, 'date' => null],
                ],
            ],
        ], 200),
    ]);

    $highLow = (new ArkPricing())->allTimeHighLow('ARK', collect(['USD']));

    expect($highLow->get('USD'))->toEqual(['ath' => null, 'atl' => null]);
});

it('should throw an exception if the request fails for exchange details', function () {
    Artisan::call('migrate:fresh');

    $exchange = Exchange::factory()->create(['coingecko_id' => 'binance']);

    (new ArkPricing())->exchangeDetails($exchange);
})->throws(MarketDataThrottledException::class);

it('should throw an exception if the API response is empty for exchange details', function () {
    Artisan::call('migrate:fresh');

    $exchange = Exchange::factory()->create(['coingecko_id' => 'binance']);

    Http::fake([
        'ark-pricing.localhost/*' => Http::response(null, 200),
    ]);

    (new ArkPricing())->exchangeDetails($exchange);
})->throws(MarketDataThrottledException::class);

it('should reset exception trigger for empty responses', function ($attempt) {
    Http::fake([
        'ark-pricing.localhost/*' => Http::response(null, 200),
    ]);

    Config::set('arkscan.market_data.ark_pricing.ignore_errors', false);
    Config::set('arkscan.market_data.ark_pricing.exception_frequency', 6);

    Cache::set('ark_pricing_response_error', (($attempt - 1) % 6) + 1);

    if ($attempt === 6) {
        $this->expectExceptionMessage('Too many empty ArkPricing responses');
    }

    expect((new ArkPricing())->priceAndPriceChange('ARK', collect(['USD'])))->toEqual(collect());
})->with([1, 2, 3, 4, 5, 6]);

it('should count a 429 response as throttled instead of empty', function () {
    Http::fake([
        'ark-pricing.localhost/*' => Http::response(['message' => 'Too Many Attempts.'], 429),
    ]);

    Cache::forget('ark_pricing_response_error');
    Cache::forget('ark_pricing_response_throttled');

    expect((new ArkPricing())->historical('ARK', 'USD'))->toEqual(collect());

    expect(Cache::get('ark_pricing_response_throttled'))->toEqual(1);
    expect(Cache::get('ark_pricing_response_error'))->toBeNull();
});

it('should not count a connection failure as throttled', function () {
    Cache::forget('ark_pricing_response_error');
    Cache::forget('ark_pricing_response_throttled');

    expect((new ArkPricing())->historical('ARK', 'USD'))->toEqual(collect());

    expect(Cache::get('ark_pricing_response_throttled'))->toBeNull();
    expect(Cache::get('ark_pricing_response_error'))->toEqual(1);
});

it('should not throw exception if ignored', function ($attempt) {
    Http::fake([
        'ark-pricing.localhost/*' => Http::response(null, 200),
    ]);

    Config::set('arkscan.market_data.ark_pricing.ignore_errors', true);
    Config::set('arkscan.market_data.ark_pricing.exception_frequency', 6);

    Cache::set('ark_pricing_response_error', (($attempt - 1) % 6) + 1);

    expect((new ArkPricing())->priceAndPriceChange('ARK', collect(['USD'])))->toEqual(collect());
})->with([1, 2, 3, 4, 5, 6]);
