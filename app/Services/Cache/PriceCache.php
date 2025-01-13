<?php

declare(strict_types=1);

namespace App\Services\Cache;

use App\Contracts\Cache as Contract;
use App\Services\Cache\Concerns\ManagesCache;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Facades\Cache;

final class PriceCache implements Contract
{
    use ManagesCache;

    public function getLastUpdated(): array
    {
        return $this->get('last_updated', []);
    }

    public function setLastUpdated(array $data): void
    {
        $this->put('last_updated', $data);
    }

    public function hasDailyPriceForCurrency(string $currency, int $timestamp): bool
    {
        return $this->has(sprintf('daily_price/%s/%d', $currency, $timestamp));
    }

    public function getDailyPriceForCurrency(string $currency, int $timestamp): float
    {
        return (float) $this->get(sprintf('daily_price/%s/%d', $currency, $timestamp), 0);
    }

    public function setDailyPriceForCurrency(string $currency, int $timestamp, float $value): void
    {
        $this->put(sprintf('daily_price/%s/%d', $currency, $timestamp), $value);
    }

    public function getCache(): TaggedCache
    {
        return Cache::tags('price');
    }
}
