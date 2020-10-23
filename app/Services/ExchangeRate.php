<?php

declare(strict_types=1);

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

final class ExchangeRate
{
    public static function convert(float $amount, int $timestamp): string
    {
        $prices       = Cache::get('prices.'.Settings::currency());
        $exchangeRate = Arr::get($prices, Carbon::parse(static::timestamp($timestamp))->format('Y-m-d'), 0);

        return NumberFormatter::currency($amount * $exchangeRate, Settings::currency());
    }

    public static function now(): float
    {
        return (float) Arr::get(Cache::get('prices.'.Settings::currency()), Carbon::now()->format('Y-m-d'), 0);
    }

    private static function timestamp(int $timestamp): Carbon
    {
        return Timestamp::fromGenesis($timestamp);
    }
}
