<?php

declare(strict_types=1);

use App\Models\Exchange;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    Artisan::call('migrate:fresh');
});

it('filter exchanges that have coingecko id', function () {
    $exchange = Exchange::factory()->create([
        'coingecko_id' => 'binance',
    ]);

    Exchange::factory()->create([
        'coingecko_id' => null,
    ]);

    $exchanges = Exchange::coingecko()->get();

    expect($exchanges->count())->toBe(1);

    expect($exchanges->first()->id)->toBe($exchange->id);
});

it('filter by exchanges', function () {
    $exchange = Exchange::factory()->create([
        'is_exchange' => true,
    ]);

    Exchange::factory()->create([
        'is_exchange' => false,
    ]);

    $exchanges = Exchange::filterByType('exchanges')->get();

    expect($exchanges->count())->toBe(1);

    expect($exchanges->first()->id)->toBe($exchange->id);
});

it('filter by aggregators', function () {
    $exchange = Exchange::factory()->create([
        'is_aggregator' => true,
    ]);

    Exchange::factory()->create([
        'is_aggregator' => false,
    ]);

    $exchanges = Exchange::filterByType('aggregators')->get();

    expect($exchanges->count())->toBe(1);

    expect($exchanges->first()->id)->toBe($exchange->id);
});

it('ignores an invalid type', function ($type) {
    Exchange::factory()->count(2)->create();

    Exchange::filterByType($type)->get();

    expect(Exchange::count())->toBe(2);
})->with([
    'invalid',
    null,
]);

it('filter by pairs', function ($pair) {
    $exchange = Exchange::factory()->create([
        $pair => true,
    ]);

    Exchange::factory()->create([
        $pair => false,
    ]);

    $exchanges = Exchange::filterByPair($pair)->get();

    expect($exchanges->count())->toBe(1);

    expect($exchanges->first()->id)->toBe($exchange->id);
})->with([
    'btc',
    'eth',
    'stablecoins',
    'other',
]);

it('ignores an invalid pair', function ($type) {
    Exchange::factory()->count(2)->create();

    Exchange::filterByPair($type)->get();

    expect(Exchange::count())->toBe(2);
})->with([
    'invalid',
    null,
]);
