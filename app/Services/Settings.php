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
            'currency'   => 'usd',
            'priceChart' => true,
            'feeChart'   => true,
            'darkTheme'  => true,
        ];
    }

    public static function currency(): string
    {
        return Arr::get(static::all(), 'currency', 'USD');
    }

    public static function priceChart(): bool
    {
        return Arr::get(static::all(), 'priceChart', true);
    }

    public static function feeChart(): bool
    {
        return Arr::get(static::all(), 'feeChart', true);
    }

    public static function darkTheme(): bool
    {
        return Arr::get(static::all(), 'darkTheme', true);
    }

    public static function theme(): string
    {
        if (static::darkTheme()) {
            return 'dark';
        }

        return 'light';
    }

    public static function usesPriceChart(): bool
    {
        return static::priceChart() === true;
    }

    public static function usesFeeChart(): bool
    {
        return static::feeChart() === true;
    }

    public static function usesDarkTheme(): bool
    {
        return static::darkTheme() === true;
    }
}
