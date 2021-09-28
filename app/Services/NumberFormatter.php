<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CryptoCurrencies;
use Konceiver\BetterNumberFormatter\BetterNumberFormatter;
use Konceiver\BetterNumberFormatter\ResolveScientificNotation;
use ReflectionClass;

final class NumberFormatter
{
    public const CRYPTO_DECIMALS = 8;

    public const FIAT_DECIMALS = 2;

    /**
     * @param string|int|float $value
     */
    public static function number($value): string
    {
        return BetterNumberFormatter::new()->formatWithDecimal((float) $value);
    }

    /**
     * @param string|int|float $value
     */
    public static function percentage($value): string
    {
        return BetterNumberFormatter::new()->formatWithPercent((float) $value, 2);
    }

    /**
     * @param string|int|float $value
     */
    public static function satoshi($value): string
    {
        return BetterNumberFormatter::new()->formatWithDecimal(BigNumber::new($value)->toFloat());
    }

    /**
     * @param string|int|float $value
     */
    public static function currency($value, string $currency): string
    {
        if (! static::isFiat($currency)) {
            return BetterNumberFormatter::new()
                ->formatWithCurrencyCustom($value, $currency, static::decimalsFor($currency));
        }

        return BetterNumberFormatter::new()
            ->withLocale('international')
            ->withFractionDigits(static::decimalsFor($currency))
            ->formatCurrency((float) $value, $currency);
    }

    /**
     * @param string|int|float $value
     */
    public static function currencyWithoutSuffix($value, string $currency): string
    {
        return trim(BetterNumberFormatter::new()->formatWithCurrencyCustom($value, '', static::decimalsFor($currency)));
    }

    /**
     * @param string|int|float $value
     */
    public static function currencyWithDecimalsWithoutSuffix($value, string $currency): string
    {
        return number_format((float) ResolveScientificNotation::execute((float) $value), static::decimalsFor($currency));
    }

    /**
     * @param string|int|float $value
     */
    public static function currencyShort($value, string $currency): string
    {
        return BetterNumberFormatter::new()->formatWithCurrencyShort($value, $currency);
    }

    /**
     * @param string|int|float $value
     */
    public static function currencyShortNotation($value): string
    {
        $value = is_string($value) ? (float) $value : $value;

        if ($value < 1000) {
            return sprintf('%d', $value);
        }

        if ($value < 1000000) {
            return sprintf('%d%s', number_format($value / 1000, 3), 'K');
        }

        return sprintf('%0.2f%s', number_format($value / 1000000, 6), 'M');
    }

    public static function isFiat(string $currency): bool
    {
        $cryptoCurrencies = (new ReflectionClass(CryptoCurrencies::class))->getConstants();

        return ! in_array($currency, $cryptoCurrencies, true);
    }

    public static function decimalsFor(string $currency): int
    {
        if (static::isFiat($currency)) {
            return self::FIAT_DECIMALS;
        }

        return self::CRYPTO_DECIMALS;
    }
}
