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
            'language'        => 'en',
            'currency'        => 'usd',
            'priceSource'     => 'cryptocompare',
            'statisticsChart' => true,
            'darkTheme'       => true,
        ];
    }

    public static function language(): string
    {
        return static::setting('language', 'en');
    }

    public static function currency(): string
    {
        return static::setting('currency', 'usd');
    }

    public static function priceSource(): string
    {
        return static::setting('priceSource', 'cryptocompare');
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
