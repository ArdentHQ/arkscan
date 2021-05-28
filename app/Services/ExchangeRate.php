<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CryptoCurrencies;
use App\Services\Cache\CryptoCompareCache;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use ReflectionClass;

final class ExchangeRate
{
    const CRYPTO_DECIMALS = 8;

    const FIAT_DECIMALS = 2;

    public static function convert(float $amount, int $timestamp): string
    {
        $prices       = (new CryptoCompareCache())->getPrices(Settings::currency());
        $exchangeRate = Arr::get($prices, Carbon::parse(static::timestamp($timestamp))->format('Y-m-d'), 0);

        return NumberFormatter::currency($amount * $exchangeRate, Settings::currency(), static::decimalsFor(Settings::currency()));
    }

    public static function now(): float
    {
        return (float) Arr::get(
            (new CryptoCompareCache())->getPrices(Settings::currency()),
            Carbon::now()->format('Y-m-d'),
            0
        );
    }

    public static function decimalsFor(string $currency): int
    {
        if (static::isFiat($currency)) {
            return self::FIAT_DECIMALS;
        }

        return self::CRYPTO_DECIMALS;
    }

    public static function isFiat(string $currency): bool
    {
        $cryptoCurrencies = (new ReflectionClass(CryptoCurrencies::class))->getConstants();

        return ! in_array($currency, $cryptoCurrencies, true);
    }

    private static function timestamp(int $timestamp): Carbon
    {
        return Timestamp::fromGenesis($timestamp);
    }
}
