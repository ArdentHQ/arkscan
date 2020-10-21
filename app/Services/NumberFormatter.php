<?php

declare(strict_types=1);

namespace App\Services;

final class NumberFormatter
{
    public static function number($value): string
    {
        $formatter = new \NumberFormatter('en_US', \NumberFormatter::DECIMAL);

        return $formatter->format($value);
    }

    public static function currency($value, string $currency): string
    {
        return static::number($value).' '.strtoupper($currency);
    }
}
