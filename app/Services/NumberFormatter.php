<?php

declare(strict_types=1);

namespace App\Services;

use Konceiver\BetterNumberFormatter\BetterNumberFormatter;

final class NumberFormatter
{
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
    public static function currency($value, string $currency, ?int $decimals = null): string
    {
        return BetterNumberFormatter::new()->formatWithCurrencyCustom($value, $currency, $decimals);
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
        if ($value < 1000) {
            return sprintf('%d', $value);
        }

        if ($value < 1000000) {
            return sprintf('%d%s', number_format($value / 1000, 3), 'K');
        }

        return sprintf('%0.2f%s', number_format($value / 1000000, 6), 'M');
    }
}
