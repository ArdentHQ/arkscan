<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Artisan::call('migrate:fresh');
});

it('loads and syncs exchanges', function () {
    $responseJson = [
        [
            'exchangeName' => 'Exchange 1',
            'baseURL'      => 'http://exchange1.com',
            'exchange'     => true,
            'aggregator'   => false,
            'BTC'          => true,
            'ETH'          => false,
            'stablecoins'  => true,
            'other'        => false,
            'coingeckoId'  => 'exchange1_id',
            'icon'         => '7b',
        ],
        [
            'exchangeName' => 'Exchange 2',
            'baseURL'      => 'http://exchange2.com',
            'exchange'     => true,
            'aggregator'   => true,
            'BTC'          => false,
            'ETH'          => true,
            'stablecoins'  => false,
            'other'        => true,
            'coingeckoId'  => 'exchange2_id',
            'icon'         => '7b',
        ],
    ];

    Http::fake([
        '*' => Http::response($responseJson),
    ]);

    $this->artisan('exchanges:load')
        ->assertExitCode(0);

    $this->assertDatabaseCount('exchanges', 2);

    $this->assertDatabaseHas('exchanges', [
        'name'          => 'Exchange 1',
        'url'           => 'http://exchange1.com',
        'is_exchange'   => true,
        'is_aggregator' => false,
        'btc'           => true,
        'eth'           => false,
        'stablecoins'   => true,
        'other'         => false,
        'coingecko_id'  => 'exchange1_id',
        'icon'          => '7b',
    ]);

    $this->assertDatabaseHas('exchanges', [
        'name'          => 'Exchange 2',
        'url'           => 'http://exchange2.com',
        'is_exchange'   => true,
        'is_aggregator' => true,
        'btc'           => false,
        'eth'           => true,
        'stablecoins'   => false,
        'other'         => true,
        'coingecko_id'  => 'exchange2_id',
        'icon'          => '7b',
    ]);
});

it('throws an exception if response format is unexpected', function () {
    $responseJson = [
        [
            'exchangeName' => 'Exchange 1',
            'baseURL'      => 'http://exchange1.com',
            'exchange'     => true,
            'aggregator'   => false,
            'BTC'          => true,
            'ETH'          => false,
            'stablecoins'  => true,
            'icon'         => '7b',
        ],
        [
            'exchangeName' => 'Exchange 2',
            'baseURL'      => 'http://exchange2.com',
            'exchange'     => true,
            'aggregator'   => true,
            'BTC'          => false,
            'ETH'          => true,
            'stablecoins'  => false,
            'other'        => true,
            'coingeckoId'  => 'exchange2_id',
            'icon'         => '7b',
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

it('throws an exception if no exchanges list source is configured', function () {
    config()->set('arkscan.exchanges.list_src', '');

    $this->expectException(Exception::class);
    $this->expectExceptionMessage('No exchanges list source configured');

    $this->artisan('exchanges:load');
});
