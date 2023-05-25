<?php

declare(strict_types=1);

use App\Contracts\MarketDataProvider;
use App\Jobs\FetchExchangeDetails;
use App\Models\Exchange;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    Artisan::call('migrate:fresh');
});

it('should fetch exchange details and update the exchange model', function () {
    $this->mock(MarketDataProvider::class)->shouldReceive('exchangeDetails')->andReturn([
        'price'  => 10.5,
        'volume' => 1000,
    ]);

    $exchange = Exchange::factory()->create([
        'price'  => null,
        'volume' => null,
    ]);

    FetchExchangeDetails::dispatch($exchange);

    $exchange->refresh();

    expect($exchange->price)->toBe('10.5');
    expect($exchange->volume)->toBe('1000');
});

it('should clear exchange details when no information is available for the exchange', function () {
    $this->mock(MarketDataProvider::class)->shouldReceive('exchangeDetails')->andReturn(null);

    $exchange = Exchange::factory()->create([
        'price'  => '10.5',
        'volume' => '1000',
    ]);

    FetchExchangeDetails::dispatch($exchange);

    $exchange->refresh();

    expect($exchange->price)->toBeNull();
    expect($exchange->volume)->toBeNull();
});

it('retries after 60s', function () {
    $exchange = Exchange::factory()->create();

    expect((new FetchExchangeDetails($exchange))->retryAfter())->toBe(60);
});
