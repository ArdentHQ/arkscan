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
        return $this->remember(sprintf('prices/%s', $currency), now()->addMinutes(10), fn () => $prices);
    }

    public function getCache(): TaggedCache
    {
        return Cache::tags('crypto_compare');
    }
}
