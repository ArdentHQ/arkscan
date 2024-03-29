<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\CacheNetworkSupply;
use App\Services\Cache\NetworkStatusBlockCache;

final class MarketCap
{
    public static function get(string $source, string $target): ? float
    {
        $price = static::getPrice($source, $target);

        if ($price === null) {
            return null;
        }

        return $price * static::getSupply();
    }

    public static function getFormatted(string $source, string $target): ? string
    {
        $marketcap = static::get($source, $target);

        if ($marketcap === null) {
            return null;
        }

        return NumberFormatter::currencyForViews($marketcap, $target);
    }

    private static function getSupply(): float
    {
        return CacheNetworkSupply::execute() / 1e8;
    }

    private static function getPrice(string $source, string $target): ? float
    {
        return (new NetworkStatusBlockCache())->getPrice($source, $target);
    }
}
