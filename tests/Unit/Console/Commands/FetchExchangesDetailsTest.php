<?php

declare(strict_types=1);

use App\Contracts\MarketDataProvider;
use App\Models\Exchange;
use App\Services\MarketDataProviders\CoinGecko;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->freezeTime();

    $this->travelTo(Carbon::parse('2024-05-14 12:22:49'));

    $this->app->singleton(
        MarketDataProvider::class,
        fn () => new CoinGecko()
    );
});

it('should update exchange details for coingecko exchanges once per hour', function () {
    Http::fake([
        'https://api.coingecko.com/api/v3/exchanges/binance/tickers?coin_ids=ark' => Http::response([
            'tickers' => [
                [
                    'converted_last' => [
                        'usd' => 123,
                    ],
                    'converted_volume' => [
                        'usd' => 456,
                    ],
                ],
            ],
        ], 200),
    ]);

    $coingeckoExchange = Exchange::factory()->create([
        'coingecko_id' => 'binance',
        'volume'       => null,
        'price'        => null,
    ]);

    $genericExchange = Exchange::factory()->create([
        'coingecko_id' => null,
        'volume'       => null,
        'price'        => null,
    ]);

    $this->artisan('exchanges:fetch-details');

    $this->travel(59)->minutes();

    expect($coingeckoExchange->fresh()->price)->toBeNull();
    expect($coingeckoExchange->fresh()->volume)->toBeNull();

    expect($genericExchange->fresh()->price)->toBeNull();
    expect($genericExchange->fresh()->volume)->toBeNull();

    $this->travel(2)->minutes();

    $this->artisan('exchanges:fetch-details');

    expect($coingeckoExchange->fresh()->price)->toBe('123');
    expect($coingeckoExchange->fresh()->volume)->toBe('456');

    expect($genericExchange->fresh()->price)->toBeNull();
    expect($genericExchange->fresh()->volume)->toBeNull();
});

it('should do nothing if there is a coingecko error', function () {
    Http::fake([
        'https://api.coingecko.com/api/v3/exchanges/binance/tickers?coin_ids=ark' => Http::response([
            'status' => [
                'error_code' => 1234,
            ],
        ], 500),
    ]);

    $coingeckoExchange = Exchange::factory()->create([
        'coingecko_id' => 'binance',
        'volume'       => null,
        'price'        => null,
    ]);

    $genericExchange = Exchange::factory()->create([
        'coingecko_id' => null,
        'volume'       => null,
        'price'        => null,
    ]);

    $this->artisan('exchanges:fetch-details');

    $this->travel(59)->minutes();

    expect($coingeckoExchange->fresh()->price)->toBeNull();
    expect($coingeckoExchange->fresh()->volume)->toBeNull();

    expect($genericExchange->fresh()->price)->toBeNull();
    expect($genericExchange->fresh()->volume)->toBeNull();

    $this->travel(2)->minutes();

    $this->artisan('exchanges:fetch-details');

    expect($coingeckoExchange->fresh()->price)->toBeNull();
    expect($coingeckoExchange->fresh()->volume)->toBeNull();

    expect($genericExchange->fresh()->price)->toBeNull();
    expect($genericExchange->fresh()->volume)->toBeNull();
});
