<?php

declare(strict_types=1);

namespace App\Services;

use App\Facades\Network;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

final class CryptoCompare
{
    public static function price(string $source, string $target): float
    {
        return (float) Cache::remember('cryptocompare.price:'.$source.':'.$target, 1800, function () use ($source, $target): string {
            $result = Http::get('https://min-api.cryptocompare.com/data/price', [
                'fsym'  => $source,
                'tsyms' => $target,
            ])->json()[strtoupper($target)];

            return ResolveScientificNotation::execute($result);
        });
    }

    public static function historical(string $source, string $target, string $format = 'Y-m-d'): Collection
    {
        $cacheKey = 'cryptocompare.historical:'.$source.':'.$target.':'.$format;
        $ttl      = Carbon::now()->addDay();

        return Cache::remember($cacheKey, $ttl, function () use ($source, $target, $format): Collection {
            $result = Http::get('https://min-api.cryptocompare.com/data/histoday', [
                'fsym'  => $source,
                'tsym'  => $target,
                'toTs'  => Carbon::now()->unix(),
                'limit' => Network::epoch()->diffInDays(),
            ])->json()['Data'];

            return collect($result)
                ->groupBy(fn ($day) => Carbon::createFromTimestamp($day['time'])->format($format))
                ->mapWithKeys(fn ($transactions, $day) => [$day => NumberFormatter::number($transactions->sum('close'))]);
        });
    }
}
