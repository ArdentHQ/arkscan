<?php

declare(strict_types=1);

use App\Services\Cache\CryptoCompareCache;
use App\Services\Cache\FeeCache;
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

    foreach (['day', 'week', 'month', 'quarter', 'year'] as $period) {
        (new FeeCache())->setHistorical($period, collect([]));
        (new FeeCache())->setMinimum($period, 0);
        (new FeeCache())->setAverage($period, 0);
        (new FeeCache())->setMaximum($period, 0);
    }

    (new CryptoCompareCache())->setPrices('USD', collect([]));

    (new NetworkCache())->setVolume(strval(1e8));
    (new NetworkCache())->setTransactionsCount('1000');
    (new NetworkCache())->setVotesCount('100');
    (new NetworkCache())->setVotesPercentage('10');

    $this
        ->get(route('home'))
        ->assertOk();
});
