<?php

declare(strict_types=1);

namespace App\Services\Cache;

use App\Contracts\Cache as Contract;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class CryptoCompareCache implements Contract
{
    use Concerns\ManagesCache;

    public function setHistorical(string $source, string $target, string $format, \Closure $callback): Collection
    {
        return $this->remember(sprintf('historical/%s/%s/%s', $source, $target, $format), now()->addMinutes(10), $callback);
    }

    public function setPrice(string $source, string $target, \Closure $callback): float
    {
        return (float) $this->remember(sprintf('price/%s/%s', $source, $target), now()->addMinutes(10), $callback);
    }

    public function getPrices(string $currency): Collection
    {
        return $this->get(sprintf('prices/%s', $currency));
    }

    public function setPrices(string $currency, Collection $prices): Collection
    {
        return $this->remember(sprintf('prices/%s', $currency), now()->addMinutes(10), fn () =>$prices);
    }

    public function getCache(): TaggedCache
    {
        return Cache::tags('crypto_compare');
    }
}
