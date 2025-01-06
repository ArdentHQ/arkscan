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

    // @TODO Review the data stored here - https://app.clickup.com/t/86dvm349z
    public function setHistorical(string $source, string $target, string $format, Closure $callback): Collection
    {
        $data = $callback();

        $this->put(sprintf('historical/%s/%s/%s', $source, $target, $format), $data);

        return $data;
    }

    // @TODO Review the data stored here - https://app.clickup.com/t/86dvm349z
    public function setHistoricalHourly(string $source, string $target, string $format, int $limit, Closure $callback): Collection
    {
        $data = $callback();

        $this->put(sprintf('historical/%s/%s/%s/%s', $source, $target, $format, $limit), $data);

        return $data;
    }

    public function getPrices(string $currency): Collection
    {
        return $this->get(sprintf('prices/%s', $currency), collect([]));
    }

    public function setPrices(string $currency, Collection $prices): Collection
    {
        $this->put(sprintf('prices/%s', $currency), $prices);

        return $prices;
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

    public function getPriceData(string $currency): array
    {
        return $this->get(sprintf('price_data/%s', $currency), []);
    }

    public function setPriceData(string $currency, array $data): void
    {
        $this->put(sprintf('price_data/%s', $currency), $data);
    }

    public function setExchangeVolume(string $source, string $target, Closure $callback): Collection
    {
        $data = $callback();

        $this->put(sprintf('exchange_volume/%s/%s', $source, $target), $data);

        return $data;
    }

    /**
     * @return array{prices: array{0:int, 1:float}[], market_caps: array{0:int, 1:float}[], total_volumes: array{0:int, 1:float}[]}|array{}
     */
    public function getHistoricalFullResponse(string $source, string $target): array
    {
        return $this->get(sprintf('historical_full/all_time/%s/%s', $source, $target), []);
    }

    public function setHistoricalFullResponse(string $source, string $target, array $values): void
    {
        $this->put(sprintf('historical_full/all_time/%s/%s', $source, $target), $values);
    }

    /**
     * @return array{prices: array{0:int, 1:float}[], market_caps: array{0:int, 1:float}[], total_volumes: array{0:int, 1:float}[]}|array{}
     */
    public function getHistoricalHourlyFullResponse(string $source, string $target): array
    {
        return $this->get(sprintf('historical_full/hourly/%s/%s', $source, $target), []);
    }

    public function setHistoricalHourlyFullResponse(string $source, string $target, array $values): void
    {
        $this->put(sprintf('historical_full/hourly/%s/%s', $source, $target), $values);
    }

    public function getCache(): TaggedCache
    {
        return Cache::tags('crypto_compare');
    }
}
