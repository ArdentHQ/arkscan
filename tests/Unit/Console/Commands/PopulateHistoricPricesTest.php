<?php

declare(strict_types=1);

use App\Models\Price;
use App\Services\Cache\PriceCache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    Price::truncate();
});

it('should store data', function () {
    Config::set('currencies', [
        'usd' => config('currencies.usd'),
    ]);

    expect(Price::count())->toBe(0);

    Artisan::call('explorer:populate-historic-prices');

    expect(Price::count())->toBe(2850);
});

it('should update existing data', function () {
    Price::factory()->create([
        'timestamp' => '2022-01-01 00:00:00',
        'currency'  => 'USD',
        'value'     => 100000.0,
    ]);

    Config::set('currencies', [
        'usd' => config('currencies.usd'),
    ]);

    $price = Price::where('timestamp', '2022-01-01 00:00:00')->first();

    expect($price->value)->toBe(100000.0);

    Artisan::call('explorer:populate-historic-prices');

    expect($price->fresh()->value)->toBe(1.1810428390693);
});

it('should clear last updated timestamp for currencies', function () {
    $cache = new PriceCache();

    Config::set('currencies', [
        'usd' => config('currencies.usd'),
    ]);

    $cache->setLastUpdated([
        'usd' => Carbon\Carbon::now()->subMinutes(11)->unix(),
    ]);

    Artisan::call('explorer:populate-historic-prices');

    expect($cache->getLastUpdated())->toBe([]);
});

it('should error for unknown currency', function () {
    Config::set('currencies', [
        'twd' => [
            'currency' => 'TWD',
            'symbol'   => 'NT$',
            'locale'   => 'zh_TW',
        ],
    ]);

    expect(Price::count())->toBe(0);

    $outputBuffer = new Symfony\Component\Console\Output\BufferedOutput();
    Artisan::call('explorer:populate-historic-prices', [], $outputBuffer);

    expect(Price::count())->toBe(0);

    expect($outputBuffer->fetch())->toEqual("Currency file not found for twd\n");
});
