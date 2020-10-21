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
    public static function currency($value, string $currency): string
    {
        return static::number($value).' '.strtoupper($currency);
    }
}
