<?php

declare(strict_types=1);

namespace App\Services\Cache;

use App\Contracts\Cache as Contract;
use App\Services\Cache\Concerns\ManagesCache;
use Closure;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class CryptoDataCache implements Contract
{
    use ManagesCache;

    public function setHistorical(string $source, string $target, string $format, Closure $callback): Collection
    {
        return $this->remember(sprintf('historical/%s/%s/%s', $source, $target, $format), now()->addMinutes(10), $callback);
    }

    public function setHistoricalHourly(string $source, string $target, string $format, int $limit, Closure $callback): Collection
    {
        return $this->remember(sprintf('historical/%s/%s/%s/%s', $source, $target, $format, $limit), now()->addMinutes(10), $callback);
    }

    public function getPrices(string $currency): Collection
    {
        return $this->get(sprintf('prices/%s', $currency), collect([]));
    }

    public function setPrices(string $currency, Collection $prices): Collection
    {
        $this->put(sprintf('prices/%s', $currency), $prices, now()->addMinutes(10));

        return $prices;
    }

    public function getCache(): TaggedCache
    {
        return Cache::tags('crypto_compare');
    }

    // Add caches for volume in all currencies
    public function getVolume(string $currency): ?string
    {
        return $this->get(sprintf('volume/%s', $currency), null);
    }

    public function setVolume(string $currency, string $volume): void
    {
        $this->put(sprintf('volume/%s', $currency), $volume);
    }

    public function setHistoricalFullResponse(string $source, string $target, array $values): void
    {
        $this->put(sprintf('historical_full/all_time/%s/%s', $source, $target), $values);
    }

    public function setHistoricalHourlyFullResponse(string $source, string $target, array $values): void
    {
        $this->put(sprintf('historical_full/hourly/%s/%s', $source, $target), $values);
    }

    /**
     * @return array{prices: array{0:int, 1:float}[], market_caps: array{0:int, 1:float}[], total_volumes: array{0:int, 1:float}[]}|array{}
     */
    public function getHistoricalFullResponse(string $source, string $target): array
    {
        return $this->get(sprintf('historical_full/all_time/%s/%s', $source, $target), []);
    }

    /**
     * @return array{prices: array{0:int, 1:float}[], market_caps: array{0:int, 1:float}[], total_volumes: array{0:int, 1:float}[]}|array{}
     */
    public function getHistoricalHourlyFullResponse(string $source, string $target): array
    {
        return $this->get(sprintf('historical_full/hourly/%s/%s', $source, $target), []);
    }
}
