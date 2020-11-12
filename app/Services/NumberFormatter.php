<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Str;

final class NumberFormatter
{
    /**
     * @param string|int|float $value
     */
    public static function number($value): string
    {
        $formatter = new \NumberFormatter('en_US', \NumberFormatter::DECIMAL);

        return $formatter->format($value);
    }

    /**
     * @param string|int|float $value
     */
    public static function percentage($value): string
    {
        return sprintf('%0.2f', $value).'%';
    }

    /**
     * @param string|int|float $value
     */
    public static function satoshi($value): string
    {
        $formatter = new \NumberFormatter('en_US', \NumberFormatter::DECIMAL);

        return $formatter->format(BigNumber::new($value)->toFloat());
    }

    /**
     * @param string|int|float $value
     */
    public static function currency($value, string $currency, ?int $decimals = null): string
    {
        if (Str::contains((string) $value, ',')) {
            return $value.' '.strtoupper($currency);
        }

        if (Str::contains((string) $value, '.')) {
            $value = (float) ResolveScientificNotation::execute((float) $value);

            return rtrim(number_format($value, $decimals ?? 8), '0').' '.strtoupper($currency);
        }

        return static::number($value).' '.strtoupper($currency);
    }

    /**
     * @param string|int|float $value
     */
    public static function currencyShort($value, string $currency): string
    {
        $i     = 0;
        $units = ['', 'K', 'M', 'B', 'T'];

        for ($i = 0; $value >= 1000; $i++) {
            $value /= 1000;
        }

        return round((float) $value, 1).$units[$i].' '.strtoupper($currency);
    }
}
