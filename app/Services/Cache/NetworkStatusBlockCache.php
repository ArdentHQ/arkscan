<?php

declare(strict_types=1);

namespace App\Services\Cache;

use App\Contracts\Cache as Contract;
use App\Services\Cache\Concerns\ManagesCache;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class NetworkStatusBlockCache implements Contract
{
    use ManagesCache;

    public function getHistoricalHourly(string $source, string $target): ?Collection
    {
        return $this->get(sprintf('historical-hourly/%s/%s', $source, $target));
    }

    public function setHistoricalHourly(string $source, string $target, ?Collection $historical): ?Collection
    {
        $this->put(sprintf('historical-hourly/%s/%s', $source, $target), $historical);

        return $historical;
    }

    public function getPrice(string $source, string $target): ?float
    {
        $price = $this->get(sprintf('price/%s/%s', $source, $target));

        return $price === null ? null : (float) $price;
    }

    public function setPrice(string $source, string $target, ?float $price): ?float
    {
        $this->put(sprintf('price/%s/%s', $source, $target), $price);

        return $price;
    }

    public function getPriceChange(string $source, string $target): ?float
    {
        $priceChange = $this->get(sprintf('pricechange/%s/%s', $source, $target));

        return $priceChange === null ? null : (float) $priceChange;
    }

    public function setPriceChange(string $source, string $target, ?float $priceChange): ?float
    {
        $this->put(sprintf('pricechange/%s/%s', $source, $target), $priceChange);

        return $priceChange;
    }

    public function getIsAvailable(string $source, string $target): bool
    {
        return $this->getPrice($source, $target) !== null;
    }

    public function getCache(): TaggedCache
    {
        return Cache::tags('network_status_block');
    }
}
