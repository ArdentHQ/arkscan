<?php

declare(strict_types=1);

use App\Contracts\MarketDataProvider;
use App\Exceptions\CoinGeckoThrottledException;
use App\Jobs\FetchExchangeDetails;
use App\Models\Exchange;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Jobs\RedisJob;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Mockery\MockInterface;

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
    $this->mock(MarketDataProvider::class)->shouldReceive('exchangeDetails')->andReturn([
        'price'  => null,
        'volume' => null,
    ]);

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

it('retries after 60s', function () {
    $exchange = Exchange::factory()->create();

    expect((new FetchExchangeDetails($exchange))->retryAfter())->toBe(60);
});

it('should remove the job from the queue if coingecko throttle us for too long', function () {
    $exchange = Exchange::factory()->create();
    $job      = new FetchExchangeDetails($exchange);

    $this->mock(MarketDataProvider::class)
        ->shouldReceive('exchangeDetails')
        ->andThrow(new CoinGeckoThrottledException())
        ->once();

    $redisJobMock = $this->mock(
        RedisJob::class,
        function (MockInterface $mock) {
            $mock->shouldReceive('attempts')
                ->once()
                ->andReturn(10)
                ->shouldReceive('delete')
                ->once()
                ->shouldNotReceive('release');
        }
    );

    $job->setJob($redisJobMock);

    $job->handle();
});

it('should release the job to the queue if attempts less than tries', function () {
    $exchange = Exchange::factory()->create();
    $job      = new FetchExchangeDetails($exchange);

    $this->mock(MarketDataProvider::class)
        ->shouldReceive('exchangeDetails')
        ->andThrow(new CoinGeckoThrottledException())
        ->once();

    $redisJobMock = $this->mock(
        RedisJob::class,
        function (MockInterface $mock) {
            $mock->shouldReceive('attempts')
                ->once()
                ->andReturn(5)
                ->shouldReceive('release')
                ->once()
                ->shouldNotReceive('delete');
        }
    );

    $job->setJob($redisJobMock);

    $job->handle();
});
