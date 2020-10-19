<?php

declare(strict_types=1);

namespace App\Services;

final class ResolveScientificNotation
{
    public static function execute(float $float): string
    {
        $parts = explode('E', strtoupper((string) $float));

        if (count($parts) === 2) {
            $exp     = abs(end($parts)) + strlen($parts[0]);
            $decimal = number_format($float, $exp);

            return strval(rtrim($decimal, '.0'));
        }

        return strval($float);
    }
}
