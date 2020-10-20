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
        $formatter = new \NumberFormatter('de_DE', \NumberFormatter::CURRENCY);

        return $formatter->formatCurrency($value, $currency);
    }
}
