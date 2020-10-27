<?php

declare(strict_types=1);

namespace App\Services;

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
        return static::number($value).' '.strtoupper($currency);
    }

    /**
     * @param string|int|float $value
     */
    public static function ordinal($value): string
    {
        $formatter = new \NumberFormatter('en_US', \NumberFormatter::ORDINAL);

        return $formatter->format($value);
    }
}
