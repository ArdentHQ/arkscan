<?php

declare(strict_types=1);

use App\Exceptions\CoinGeckoThrottledException;
use App\Models\Exchange;
use App\Services\MarketDataProviders\CoinGecko;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
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

    Config::set('arkscan.market_data.coingecko.exception_frequency', 6);

    Cache::set('coingecko_response_error', (($attempt - 1) % 6) + 1);

    if (($attempt % 6) === 0) {
        $this->expectException(Exception::class);
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

    Config::set('arkscan.market_data.coingecko.exception_frequency', 6);

    Cache::set('coingecko_response_error', (($attempt - 1) % 6) + 1);

    if (($attempt % 6) === 0) {
        $this->expectException(Exception::class);
    } else {
        $this->expectNotToPerformAssertions();
    }

    (new CoinGecko())->historicalHourly('ARK', 'USD');
})->with(range(1, 12));

it('should not throw exception if ignored', function ($attempt) {
    Http::fake([
        'api.coingecko.com/*' => Http::response([
            'status' => [
                'error_code' => 1,
            ],
        ], 500),
    ]);

    Config::set('arkscan.market_data.coingecko.ignore_error', true);
    Config::set('arkscan.market_data.coingecko.exception_frequency', 6);

    Cache::set('coingecko_response_error', (($attempt - 1) % 6) + 1);

    $this->expectNotToPerformAssertions();

    (new CoinGecko())->historicalHourly('ARK', 'USD');
})->with(range(1, 12));

it('should fetch exchange details for the given exchange', function () {
    Artisan::call('migrate:fresh');

    Http::fake([
        'api.coingecko.com/*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/coingecko/exchange_details.json')), true), 200),
    ]);

    $exchange = Exchange::factory()->create([
        'coingecko_id' => 'example_exchange_id',
    ]);

    $details = (new CoinGecko())->exchangeDetails($exchange);

    expect($details)->toHaveKeys(['price', 'volume']);
    expect($details['price'])->toEqual('0.256662');
    expect($details['volume'])->toEqual('54880');
});

it('should return null if no usd price conversion on the response', function () {
    Artisan::call('migrate:fresh');

    $response = json_decode(file_get_contents(base_path('tests/fixtures/coingecko/exchange_details.json')), true);
    Arr::set($response, 'tickers.0.converted_last.usd', null);

    Http::fake([
        'api.coingecko.com/*' => Http::response($response, 200),
    ]);

    $exchange = Exchange::factory()->create([
        'coingecko_id' => 'example_exchange_id',
    ]);

    $details = (new CoinGecko())->exchangeDetails($exchange);

    expect($details['price'])->toBeNull();
    expect($details['volume'])->toBe(54880);
});

it('should return null if no usd volume conversion on the response', function () {
    Artisan::call('migrate:fresh');

    $response = json_decode(file_get_contents(base_path('tests/fixtures/coingecko/exchange_details.json')), true);
    Arr::set($response, 'tickers.0.converted_volume.usd', null);

    Http::fake([
        'api.coingecko.com/*' => Http::response($response, 200),
    ]);

    $exchange = Exchange::factory()->create([
        'coingecko_id' => 'example_exchange_id',
    ]);

    $details = (new CoinGecko())->exchangeDetails($exchange);

    expect($details['price'])->toBe(0.256662);
    expect($details['volume'])->toBeNull();
});

it('should throw an exception if the API response is empty for exchange details', function () {
    Artisan::call('migrate:fresh');

    Http::fake([
        'api.coingecko.com/*' => Http::response(null, 200),
    ]);

    $exchange = Exchange::factory()->create([
        'coingecko_id' => 'example_exchange_id',
    ]);

    (new CoinGecko())->exchangeDetails($exchange);
})->throws(CoinGeckoThrottledException::class);

it('should throw an exception if the API response throws an exception', function () {
    Artisan::call('migrate:fresh');

    Http::fake([
        'api.coingecko.com/*' => fn () => throw new Exception('Test'),
    ]);

    $exchange = Exchange::factory()->create([
        'coingecko_id' => 'example_exchange_id',
    ]);

    (new CoinGecko())->exchangeDetails($exchange);
})->throws(CoinGeckoThrottledException::class);

it('should throw an exception if the API response indicates throttling for exchange details', function () {
    Artisan::call('migrate:fresh');

    Http::fake([
        'api.coingecko.com/*' => Http::response([
            'status' => [
                'error_code' => 1,
            ],
        ], 500),
    ]);

    $exchange = Exchange::factory()->create([
        'coingecko_id' => 'example_exchange_id',
    ]);

    (new CoinGecko())->exchangeDetails($exchange);
})->throws(CoinGeckoThrottledException::class);

it('should fetch volume for the given network', function () {
    Http::fake([
        'api.coingecko.com/*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/coingecko/coin.json')), true), 200),
    ]);

    $volume = (new CoinGecko())->volume('ARK');

    expect($volume)->toHaveLength(61);
    expect($volume['usd'])->toEqual(16232625);
});

it('should return empty array if no volume in response', function () {
    $response = json_decode(file_get_contents(base_path('tests/fixtures/coingecko/coin.json')), true);
    Arr::set($response, 'market_data', []);

    Http::fake([
        'api.coingecko.com/*' => Http::response($response, 200),
    ]);

    $volume = (new CoinGecko())->volume('ARK');

    expect($volume)->toHaveLength(0);
});

it('should throw an exception if the API response is empty for volume', function () {
    Http::fake([
        'api.coingecko.com/*' => Http::response(null, 200),
    ]);

    (new CoinGecko())->volume('ARK');
})->throws(CoinGeckoThrottledException::class);

it('should throw an exception if the API response throws an exception for volume', function () {
    Http::fake([
        'api.coingecko.com/*' => fn () => throw new Exception('Test'),
    ]);

    (new CoinGecko())->volume('ARK');
})->throws(CoinGeckoThrottledException::class);

it('should throw an exception if the API response indicates throttling for volume', function () {
    Http::fake([
        'api.coingecko.com/*' => Http::response([
            'status' => [
                'error_code' => 1,
            ],
        ], 500),
    ]);

    (new CoinGecko())->volume('ARK');
})->throws(CoinGeckoThrottledException::class);
