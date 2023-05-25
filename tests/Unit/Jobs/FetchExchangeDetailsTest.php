<?php

declare(strict_types=1);

use App\Contracts\MarketDataProvider;
use App\Exceptions\CoinGeckoThrottledException;
use App\Jobs\FetchExchangeDetails;
use App\Models\Exchange;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;

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

it('should keep exchange details when no information is available for the exchange', function () {
    $this->mock(MarketDataProvider::class)->shouldReceive('exchangeDetails')->andReturn(null);

    $exchange = Exchange::factory()->create([
        'price'  => '10.5',
        'volume' => '1000',
    ]);

    FetchExchangeDetails::dispatch($exchange);

    $exchange->refresh();

    expect($exchange->price)->toBe('10.5');
    expect($exchange->volume)->toBe('1000');
});

it('should release the job again if throttled exception', function () {
    Queue::after(function (JobProcessed $event) {
        $this->assertTrue($event->job->isReleased());
    });

    $this->mock(MarketDataProvider::class)
        ->shouldReceive('exchangeDetails')
        ->once()
        ->andThrow(new CoinGeckoThrottledException());

    $exchange = Exchange::factory()->create();

    FetchExchangeDetails::dispatch($exchange);
});
