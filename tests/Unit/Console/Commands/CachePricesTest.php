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
    expect($prices->getDay('USD'))->toBeArray();
    expect($prices->getWeek('USD'))->toBeArray();
    expect($prices->getMonth('USD'))->toBeArray();
    expect($prices->getQuarter('USD'))->toBeArray();
    expect($prices->getYear('USD'))->toBeArray();
})->with(['explorer.networks.development', 'explorer.networks.production']);
