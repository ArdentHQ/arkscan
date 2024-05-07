<?php

declare(strict_types=1);

namespace App\Services\MarketDataProviders;

use App\Contracts\MarketDataProvider;
use Illuminate\Support\Facades\Cache;

abstract class AbstractMarketDataProvider implements MarketDataProvider
{
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
                    throw new \Exception($message);
                }
            }

            return true;
        }

        Cache::forget($cacheKey);

        return false;
    }
}
