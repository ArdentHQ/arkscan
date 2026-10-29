<?php

declare(strict_types=1);

use App\Jobs\FetchExchangeDetails;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Artisan::call('migrate:fresh');

    Bus::fake(FetchExchangeDetails::class);
});

it('loads and syncs exchanges', function () {
    $responseJson = [
        'data' => [
            [
                'name'               => 'Exchange 1',
                'url'                => 'http://exchange1.com',
                'isExchange'         => true,
                'isAggregator'       => false,
                'btc'                => true,
                'eth'                => false,
                'stablecoins'        => true,
                'other'              => false,
                'providerExchangeId' => 'exchange1_id',
                'icon'               => '7b',
                'price'              => 1.23,
                'volume'             => 456.0,
            ],
            [
                'name'               => 'Exchange 2',
                'url'                => 'http://exchange2.com',
                'isExchange'         => true,
                'isAggregator'       => true,
                'btc'                => false,
                'eth'                => true,
                'stablecoins'        => false,
                'other'              => true,
                'providerExchangeId' => 'exchange2_id',
                'icon'               => '7b',
                'price'              => null,
                'volume'             => null,
            ],
        ],
    ];

    Http::fake([
        '*' => Http::response($responseJson),
    ]);

    $this->artisan('exchanges:load')
        ->assertExitCode(0);

    $this->assertDatabaseCount('exchanges', 2);

    $this->assertDatabaseHas('exchanges', [
        'name'                 => 'Exchange 1',
        'url'                  => 'http://exchange1.com',
        'is_exchange'          => true,
        'is_aggregator'        => false,
        'btc'                  => true,
        'eth'                  => false,
        'stablecoins'          => true,
        'other'                => false,
        'provider_exchange_id' => 'exchange1_id',
        'icon'                 => '7b',
        'price'                => 1.23,
        'volume'               => 456.0,
    ]);

    $this->assertDatabaseHas('exchanges', [
        'name'                 => 'Exchange 2',
        'url'                  => 'http://exchange2.com',
        'is_exchange'          => true,
        'is_aggregator'        => true,
        'btc'                  => false,
        'eth'                  => true,
        'stablecoins'          => false,
        'other'                => true,
        'provider_exchange_id' => 'exchange2_id',
        'icon'                 => '7b',
        'price'                => null,
        'volume'               => null,
    ]);
});

it('throws an exception if response format is unexpected', function () {
    $responseJson = [
        'data' => [
            [
                'name'         => 'Exchange 1',
                'url'          => 'http://exchange1.com',
                'isExchange'   => true,
                'isAggregator' => false,
                'btc'          => true,
                'eth'          => false,
                'stablecoins'  => true,
                'icon'         => '7b',
            ],
            [
                'name'               => 'Exchange 2',
                'url'                => 'http://exchange2.com',
                'isExchange'         => true,
                'isAggregator'       => true,
                'btc'                => false,
                'eth'                => true,
                'stablecoins'        => false,
                'other'              => true,
                'providerExchangeId' => 'exchange2_id',
                'icon'               => '7b',
            ],
        ],
    ];

    Http::fake([
        '*' => Http::response($responseJson),
    ]);

    $this->expectException(Exception::class);
    $this->expectExceptionMessage('Unexpected response format');

    $this->artisan('exchanges:load');
});

it('throws an exception if failed to load exchanges list', function () {
    Http::fake([
        '*' => Http::response('', 404),
    ]);

    $this->expectException(Exception::class);
    $this->expectExceptionMessage('Failed to load exchanges list');

    $this->artisan('exchanges:load');
});

it('throws an exception if ark pricing url is not configured', function () {
    config()->set('arkscan.market_data.ark_pricing.url', '');

    $this->expectException(Exception::class);
    $this->expectExceptionMessage('ARK_PRICING_URL is not configured');

    $this->artisan('exchanges:load');
});
