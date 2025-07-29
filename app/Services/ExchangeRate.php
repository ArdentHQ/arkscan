<?php

declare(strict_types=1);

namespace App\Services;

use App\Facades\Network;
use App\Facades\Settings;
use App\Services\Cache\CryptoDataCache;
use App\Services\Cache\NetworkStatusBlockCache;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

final class ExchangeRate
{
    public static function convert(float | BigNumber $amount, ?int $timestamp = null, bool $showSmallAmounts = false): string
    {
        if ($amount instanceof BigNumber) {
            $amount = $amount->toFloat();
        }

        return NumberFormatter::currency(self::convertNumerical($amount, $timestamp), Settings::currency(), $showSmallAmounts);
    }

    public static function convertNumerical(float $amount, ?int $timestamp = null): float
    {
        $exchangeRate = 0;
        if ($timestamp !== null) {
            $prices       = (new CryptoDataCache())->getPrices(Settings::currency().'.week');
            $exchangeRate = Arr::get($prices, Carbon::parse(static::timestamp($timestamp))->format('Y-m-d'), 0);
        } else {
            $exchangeRate = static::currentRate();
        }

        return $amount * $exchangeRate;
    }

    public static function convertFiatToCurrency(float $amount, string $from, string $to, int $decimals = 4): ?string
    {
        // Determine the exchange rate based on Network token currency value
        $cache = new NetworkStatusBlockCache();

        $fromValue = $cache->getPrice(Network::currency(), $from);
        $toValue   = $cache->getPrice(Network::currency(), $to);

        if ($fromValue === null || $toValue === null) {
            return null;
        }

        $exchangeRate = $toValue / $fromValue;

        if (! NumberFormatter::isFiat($to)) {
            $decimals = 8;
        }

        return NumberFormatter::currencyWithDecimals($amount * $exchangeRate, Settings::currency(), $decimals);
    }

    public static function now(): float
    {
        return (float) (new CryptoDataCache())->getPrices(Settings::currency().'.day')->last();
    }

    public static function currentRate(): ?float
    {
        return (new NetworkStatusBlockCache())->getPrice(Network::currency(), Settings::currency());
    }

    public static function rates(): Collection
    {
        return (new CryptoDataCache())->getPrices(Settings::currency().'.week');
    }

    private static function timestamp(int $timestamp): Carbon
    {
        return Timestamp::fromUnix($timestamp);
    }
}
