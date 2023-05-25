<?php

declare(strict_types=1);

use App\Jobs\FetchExchangeDetails;
use App\Models\Exchange;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;

beforeEach(function () {
    Artisan::call('migrate:fresh');

    Bus::fake(FetchExchangeDetails::class);
});

it('calls the job for fetching the exchange details for exchanges with coingecko id', function () {
    Exchange::factory()->create([
        'coingecko_id' => 'binance',
    ]);

    Exchange::factory()->create([
        'coingecko_id' => null
    ]);

    $this->artisan('exchanges:fetch-details');

    Bus::assertDispatchedTimes(FetchExchangeDetails::class, 1);

    Bus::assertDispatched(FetchExchangeDetails::class, function ($job) {
        return $job->exchange->coingecko_id === 'binance';
    });
});
