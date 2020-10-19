<?php

declare(strict_types=1);

namespace App;

final class Explorer
{
    public static function network(): string
    {
        return config('explorer.network');
    }

    public static function usesMarketsquare(): bool
    {
        return config('explorer.usesMarketsquare');
    }
}
