<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

final class Settings
{
    public static function all(): array
    {
        $defaultSettings = [
            'currency'      => 'USD',
            'priceChart'    => true,
            'feeChart'      => true,
            'darkTheme'     => false,
            'compactTables' => true,
        ];

        if (Session::has('settings')) {
            $sessionSettings = json_decode(Session::get('settings'), true);

            return $sessionSettings + $defaultSettings;
        }

        return $defaultSettings;
    }

    public static function currency(): string
    {
        return Str::upper(Arr::get(static::all(), 'currency'));
    }

    public static function locale(): string
    {
        // Since sometimes the key exists (crypto values) but returns `null`, we need another default value
        // in the end to handle that case
        return Arr::get(config('currencies'), strtolower(static::currency()).'.locale', 'en_US') ?? 'en_US';
    }

    public static function priceChart(): bool
    {
        return (bool) Arr::get(static::all(), 'priceChart', true);
    }

    public static function feeChart(): bool
    {
        return (bool) Arr::get(static::all(), 'feeChart', true);
    }

    public static function darkTheme(): bool
    {
        return (bool) Arr::get(static::all(), 'darkTheme', true);
    }

    public static function theme(): string
    {
        if (static::darkTheme()) {
            return 'dark';
        }

        return 'light';
    }

    public static function compactTables(): bool
    {
        return (bool) Arr::get(static::all(), 'compactTables', true);
    }

    public static function usesCharts(): bool
    {
        return static::usesPriceChart() || static::usesFeeChart();
    }

    public static function usesPriceChart(): bool
    {
        if (config('explorer.network') !== 'production') {
            return false;
        }

        return static::priceChart();
    }

    public static function usesFeeChart(): bool
    {
        return static::feeChart();
    }

    public static function usesDarkTheme(): bool
    {
        return static::darkTheme();
    }

    public static function usesCompactTables(): bool
    {
        return static::compactTables();
    }
}
