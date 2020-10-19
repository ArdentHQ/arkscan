<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Session;

final class Settings
{
    public static function all(): array
    {
        if (Session::has('settings')) {
            return json_decode(Session::get('settings'), true);
        }

        return [
            'currency'        => 'usd',
            'statisticsChart' => true,
            'darkTheme'       => true,
        ];
    }

    public static function currency(): string
    {
        return static::setting('currency', 'USD');
    }

    public static function statisticsChart(): bool
    {
        return static::setting('statisticsChart', true);
    }

    public static function darkTheme(): bool
    {
        return static::setting('darkTheme', true);
    }

    private static function setting(string $key, $default)
    {
        return Arr::get(static::all(), $key, $default);
    }
}
