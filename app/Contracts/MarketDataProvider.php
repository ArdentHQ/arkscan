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
     *   price: float|null,
     *   volume: float|null,
     * }
     */
    public function exchangeDetails(Exchange $exchange): array;
}
