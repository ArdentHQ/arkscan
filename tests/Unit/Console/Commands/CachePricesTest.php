<?php

declare(strict_types=1);

use App\Console\Commands\CachePrices;
use App\Contracts\Network;
use App\Services\Blockchain\Network as Blockchain;
use App\Services\Cache\CryptoCompareCache;
use App\Services\Cache\PriceChartCache;
use Illuminate\Support\Collection;
use function Tests\fakeCryptoCompare;

it('should execute the command', function (string $network) {
    fakeCryptoCompare();

    $this->app->singleton(Network::class, fn () => new Blockchain(config($network)));

    $crypto = new CryptoCompareCache();
    $prices = new PriceChartCache();

    (new CachePrices())->handle($crypto, $prices);

    expect($crypto->getPrices('USD'))->toBeInstanceOf(Collection::class);
    expect($prices->getHistorical('USD', 'day'))->toBeArray();
    expect($prices->getHistorical('USD', 'week'))->toBeArray();
    expect($prices->getHistorical('USD', 'month'))->toBeArray();
    expect($prices->getHistorical('USD', 'quarter'))->toBeArray();
    expect($prices->getHistorical('USD', 'year'))->toBeArray();
})->with(['explorer.networks.development', 'explorer.networks.production']);
