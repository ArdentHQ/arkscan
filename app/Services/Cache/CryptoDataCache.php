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
        $data = $callback();

        $this->put(sprintf('historical/%s/%s/%s', $source, $target, $format), $data);

        return $data;
    }

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
}
