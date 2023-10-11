<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\CacheNetworkSupply;
use App\Services\Cache\NetworkStatusBlockCache;
use ARKEcosystem\Foundation\NumberFormatter\NumberFormatter as BetterNumberFormatter;

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

        if (NumberFormatter::isFiat($target)) {
            return trim(trim(NumberFormatter::currencyWithDecimals($marketcap, $target, 0), '0'), '.');
        }

        return BetterNumberFormatter::new()
            ->formatWithCurrencyCustom(
                $marketcap,
                $target,
                NumberFormatter::CRYPTO_DECIMALS
            );
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
