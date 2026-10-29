<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Exchange;
use Illuminate\Support\Collection;

interface MarketDataProvider
{
    public function historical(string $source, string $target, string $format = 'Y-m-d'): Collection;

    public function historicalHourly(string $source, string $target, int $limit = 23, string $format = 'Y-m-d H:i:s'): Collection;

    public function priceAndPriceChange(string $baseCurrency, Collection $targetCurrencies): Collection;

    /**
     * @return array{
     *   price: float|int|null,
     *   volume: float|int|null,
     * }
     */
    public function exchangeDetails(Exchange $exchange): array;

    public function volume(string $baseCurrency): array;

    /**
     * All-time daily market data series as [timestamp in milliseconds, value] tuples.
     *
     * @return array{prices: array{0:int, 1:float}[], market_caps: array{0:int, 1:float}[], total_volumes: array{0:int, 1:float}[]}|array{}
     */
    public function marketChart(string $source, string $target): array;

    /**
     * Hourly market data series for the last day, same shape as marketChart().
     *
     * @return array{prices: array{0:int, 1:float}[], market_caps: array{0:int, 1:float}[], total_volumes: array{0:int, 1:float}[]}|array{}
     */
    public function marketChartHourly(string $source, string $target): array;

    /**
     * All-time high and low per target currency, with timestamps in seconds.
     *
     * @return Collection<string, array{ath: array{value: float, timestamp: int}|null, atl: array{value: float, timestamp: int}|null}>
     */
    public function allTimeHighLow(string $baseCurrency, Collection $targetCurrencies): Collection;
}
