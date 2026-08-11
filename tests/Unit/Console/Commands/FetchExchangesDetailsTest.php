<?php

declare(strict_types=1);

use App\Models\Exchange;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->freezeTime();

    $this->travelTo(Carbon::parse('2024-05-14 12:22:49'));
});

it('should update exchange details for exchanges with a provider id once per hour', function () {
    Http::fake([
        'ark-pricing.localhost/api/v1/exchanges/binance/tickers*' => Http::response([
            'data' => [
                'tickers' => [
                    [
                        'price'  => 123,
                        'volume' => 456,
                    ],
                ],
            ],
        ], 200),
    ]);

    $providerExchange = Exchange::factory()->create([
        'provider_exchange_id' => 'binance',
        'volume'               => null,
        'price'                => null,
    ]);

    $genericExchange = Exchange::factory()->create([
        'provider_exchange_id' => null,
        'volume'               => null,
        'price'                => null,
    ]);

    $this->artisan('exchanges:fetch-details');

    $this->travel(59)->minutes();

    expect($providerExchange->fresh()->price)->toBeNull();
    expect($providerExchange->fresh()->volume)->toBeNull();

    expect($genericExchange->fresh()->price)->toBeNull();
    expect($genericExchange->fresh()->volume)->toBeNull();

    $this->travel(2)->minutes();

    $this->artisan('exchanges:fetch-details');

    expect($providerExchange->fresh()->price)->toBe('123');
    expect($providerExchange->fresh()->volume)->toBe('456');

    expect($genericExchange->fresh()->price)->toBeNull();
    expect($genericExchange->fresh()->volume)->toBeNull();
});

it('should do nothing if there is an ark-pricing error', function () {
    Http::fake([
        'ark-pricing.localhost/api/v1/exchanges/binance/tickers*' => Http::response([
            'error' => 'Internal Server Error',
        ], 500),
    ]);

    $providerExchange = Exchange::factory()->create([
        'provider_exchange_id' => 'binance',
        'volume'               => null,
        'price'                => null,
    ]);

    $genericExchange = Exchange::factory()->create([
        'provider_exchange_id' => null,
        'volume'               => null,
        'price'                => null,
    ]);

    $this->artisan('exchanges:fetch-details');

    $this->travel(59)->minutes();

    expect($providerExchange->fresh()->price)->toBeNull();
    expect($providerExchange->fresh()->volume)->toBeNull();

    expect($genericExchange->fresh()->price)->toBeNull();
    expect($genericExchange->fresh()->volume)->toBeNull();

    $this->travel(2)->minutes();

    $this->artisan('exchanges:fetch-details');

    expect($providerExchange->fresh()->price)->toBeNull();
    expect($providerExchange->fresh()->volume)->toBeNull();

    expect($genericExchange->fresh()->price)->toBeNull();
    expect($genericExchange->fresh()->volume)->toBeNull();
});
