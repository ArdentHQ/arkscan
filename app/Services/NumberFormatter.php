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
    public static function currency($value, string $currency): string
    {
        if (Str::contains((string) $value, ['.', ','])) {
            return $value.' '.strtoupper($currency);
        }

        return static::number($value).' '.strtoupper($currency);
    }
}
