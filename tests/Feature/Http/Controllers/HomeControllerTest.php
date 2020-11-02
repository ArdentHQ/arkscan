<?php

declare(strict_types=1);

use App\Services\Cache\CryptoCompareCache;
use App\Services\Cache\FeeChartCache;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\PriceChartCache;
use function Tests\configureExplorerDatabase;

it('should render the page without any errors', function () {
    $this->withoutExceptionHandling();

    configureExplorerDatabase();

    (new PriceChartCache())->setDay('USD', collect([]));
    (new PriceChartCache())->setWeek('USD', collect([]));
    (new PriceChartCache())->setMonth('USD', collect([]));
    (new PriceChartCache())->setQuarter('USD', collect([]));
    (new PriceChartCache())->setYear('USD', collect([]));

    (new FeeChartCache())->setDay(collect([]));
    (new FeeChartCache())->setWeek(collect([]));
    (new FeeChartCache())->setMonth(collect([]));
    (new FeeChartCache())->setQuarter(collect([]));
    (new FeeChartCache())->setYear(collect([]));

    (new CryptoCompareCache())->setPrices('USD', collect([]));

    (new NetworkCache())->setVolume(fn () => 1e8);
    (new NetworkCache())->setTransactionsCount(fn () => 1000);
    (new NetworkCache())->setVotesCount(fn () => 100);
    (new NetworkCache())->setVotesPercentage(fn () => 10);

    $this
        ->get(route('home'))
        ->assertOk();
});
