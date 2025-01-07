<?php

declare(strict_types=1);

use App\Models\Exchange;
use App\Services\MarketDataProviders\CryptoCompare;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use function Spatie\Snapshots\assertMatchesSnapshot;
use function Tests\fakeCryptoCompare;

it('should fetch the price data for the given collection', function () {
    fakeCryptoCompare();

    $dto = (new CryptoCompare())->priceAndPriceChange('ARK', collect(['USD']))->get('USD');

    expect($dto->priceChange())->toEqual(0.14989143413680925);

    expect($dto->price())->toEqual(1.2219981765);
});

it('should return an empty value if failed response for price data', function () {
    expect((new CryptoCompare())->priceAndPriceChange('ARK', collect(['USD'])))->toEqual(collect());
});

it('should return an empty value if empty response for price data', function () {
    Http::fake([
        'cryptocompare.com/*' => Http::response(null, 200),
    ]);

    expect((new CryptoCompare())->priceAndPriceChange('ARK', collect(['USD'])))->toEqual(collect());
});

it('should fetch the historical prices for the given pair', function () {
    Http::fake([
        'cryptocompare.com/*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/historical.json')), true)),
    ]);

    assertMatchesSnapshot((new CryptoCompare())->historical('ARK', 'USD'));
});

it('should return an empty value if empty response for historical', function () {
    Http::fake([
        'cryptocompare.com/*' => Http::response(null, 200),
    ]);

    expect((new CryptoCompare())->historical('ARK', 'USD'))->toEqual(collect());
});

it('should return an empty value if failed response for historical', function () {
    expect((new CryptoCompare())->historical('ARK', 'USD'))->toEqual(collect());
});

it('should fetch the historical prices per hour for the given pair', function () {
    Http::fake([
        'cryptocompare.com/*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histohour.json')), true)),
    ]);

    assertMatchesSnapshot((new CryptoCompare())->historicalHourly('ARK', 'USD'));
});

it('should return an empty value if empty response for historical hourly', function () {
    Http::fake([
        'cryptocompare.com/*' => Http::response(null, 200),
    ]);

    expect((new CryptoCompare())->historicalHourly('ARK', 'USD'))->toEqual(collect());
});

it('should return an empty value if failed response for historical hourly', function () {
    expect((new CryptoCompare())->historicalHourly('ARK', 'USD'))->toEqual(collect());
});

it('should reset exception trigger for empty responses', function ($attempt) {
    Http::fake([
        'cryptocompare.com/*' => Http::response(null, 200),
    ]);

    Config::set('arkscan.market_data.cryptocompare.ignore_errors', false);
    Config::set('arkscan.market_data.cryptocompare.exception_frequency', 6);

    Cache::set('cryptocompare_response_error', (($attempt - 1) % 6) + 1);

    if (($attempt % 6) === 0) {
        $this->expectException(Exception::class);
    } else {
        $this->expectNotToPerformAssertions();
    }

    (new CryptoCompare())->historicalHourly('ARK', 'USD');
})->with(range(1, 12));

it('should trigger exception for throttled requests', function ($attempt) {
    Http::fake([
        'cryptocompare.com/*' => Http::response([
            'Response' => 'Error',
        ], 500),
    ]);

    Config::set('arkscan.market_data.cryptocompare.ignore_errors', false);
    Config::set('arkscan.market_data.cryptocompare.exception_frequency', 6);

    Cache::set('cryptocompare_response_error', (($attempt - 1) % 6) + 1);

    if (($attempt % 6) === 0) {
        $this->expectException(Exception::class);
    } else {
        $this->expectNotToPerformAssertions();
    }

    (new CryptoCompare())->historicalHourly('ARK', 'USD');
})->with(range(1, 12));

it('should not throw exception if ignored', function ($attempt) {
    Http::fake([
        'cryptocompare.com/*' => Http::response([
            'status' => [
                'error_code' => 1,
            ],
        ], 500),
    ]);

    Config::set('arkscan.market_data.cryptocompare.ignore_errors', true);
    Config::set('arkscan.market_data.cryptocompare.exception_frequency', 6);

    Cache::set('cryptocompare_response_error', (($attempt - 1) % 6) + 1);

    (new CryptoCompare())->historicalHourly('ARK', 'USD');

    // We shouldn't receive any exceptions
    expect(true)->toBe(true);
})->with(range(1, 12));

it('should throw an exception if the API response indicates throttling for exchange details', function () {
    Artisan::call('migrate:fresh');

    $exchange = Exchange::factory()->create();

    (new CryptoCompare())->exchangeDetails($exchange);
})->throws(Exception::class, 'Not implemented');

it('should throw an exception for volume', function () {
    (new CryptoCompare())->volume('ARK');
})->throws(Exception::class, 'Not implemented');

it('should fetch the exchange volume for the given pair', function () {
    Http::fakeSequence('cryptocompare.com/*')
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-1.json')), true), 200)
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histoday-USD-page-2.json')), true), 200);

    assertMatchesSnapshot((new CryptoCompare())->exchangeVolume('ARK', 'USD'));
});

it('should return an empty value if empty response for exchange volume', function () {
    Http::fake([
        'cryptocompare.com/*' => Http::response(null, 200),
    ]);

    expect((new CryptoCompare())->exchangeVolume('ARK', 'USD'))->toEqual(collect());
});

it('should return an empty value if failed response for exchange volume', function () {
    Http::fake([
        'cryptocompare.com/*' => Http::response([
            'Response' => 'Error',
        ], 500),
    ]);

    expect((new CryptoCompare())->exchangeVolume('ARK', 'USD'))->toEqual(collect());
});
