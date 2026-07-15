<?php

declare(strict_types=1);

namespace App\Services\MarketDataProviders;

use App\Contracts\MarketDataProvider;
use App\Exceptions\MarketDataThrottledException;
use App\Services\Cache\CryptoDataCache;
use Illuminate\Support\Facades\Cache;

abstract class AbstractMarketDataProvider implements MarketDataProvider
{
    /**
     * @return array{prices: array{0:int, 1:float}[], market_caps: array{0:int, 1:float}[], total_volumes: array{0:int, 1:float}[]}|array{}
     */
    public function marketChart(string $source, string $target): array
    {
        return (new CryptoDataCache())->getHistoricalFullResponse($source, $target);
    }

    /**
     * @return array{prices: array{0:int, 1:float}[], market_caps: array{0:int, 1:float}[], total_volumes: array{0:int, 1:float}[]}|array{}
     */
    public function marketChartHourly(string $source, string $target): array
    {
        return (new CryptoDataCache())->getHistoricalHourlyFullResponse($source, $target);
    }

    final protected function isAcceptableResponse(
        ?array $data,
        string $cacheKey,
        int $threshold,
        string $message,
        callable $errorCheck,
        bool $throwException = true,
    ): bool {
        $hasError = $errorCheck($data);

        if ($hasError || $data === null) {
            if (Cache::increment($cacheKey) > $threshold) {
                Cache::forget($cacheKey);

                if ($throwException) {
                    throw new MarketDataThrottledException($message);
                }
            }

            return true;
        }

        Cache::forget($cacheKey);

        return false;
    }
}
