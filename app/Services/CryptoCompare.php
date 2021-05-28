<?php

declare(strict_types=1);

namespace App\Services;

use App\Facades\Network;
use App\Services\Cache\CryptoCompareCache;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Konceiver\BetterNumberFormatter\ResolveScientificNotation;

final class CryptoCompare
{
    public static function price(string $source, string $target): float
    {
        return (new CryptoCompareCache())->setPrice($source, $target, function () use ($source, $target): string {
            $result = Http::get('https://min-api.cryptocompare.com/data/price', [
                'fsym'  => $source,
                'tsyms' => $target,
            ])->json()[strtoupper($target)];

            return ResolveScientificNotation::execute($result);
        });
    }

    public static function marketCap(string $source, string $target): float
    {
        return (new CryptoCompareCache())->setMarketCap($source, $target, function () use ($source, $target): float {
            $result = Http::get('https://min-api.cryptocompare.com/data/pricemultifull', [
                'fsyms'  => $source,
                'tsyms'  => $target,
            ])->json();

            return Arr::get($result, 'RAW.'.$source.'.'.$target.'.MKTCAP', 0);
        });
    }

    public static function historical(string $source, string $target, string $format = 'Y-m-d'): Collection
    {
        return (new CryptoCompareCache())->setHistorical($source, $target, $format, function () use ($source, $target, $format): Collection {
            $result = Http::get('https://min-api.cryptocompare.com/data/histoday', [
                'fsym'  => $source,
                'tsym'  => $target,
                'toTs'  => Carbon::now()->unix(),
                'limit' => Network::epoch()->diffInDays(),
            ])->json()['Data'];

            return collect($result)
                ->groupBy(fn ($day) => Carbon::createFromTimestamp($day['time'])->format($format))
                ->mapWithKeys(fn ($transactions, $day) => [$day => $transactions->sum('close')]);
        });
    }

    public static function historicalHourly(string $source, string $target, int $limit = 23, string $format = 'Y-m-d H:i:s'): Collection
    {
        return (new CryptoCompareCache())->setHistoricalHourly($source, $target, $format, $limit, function () use ($source, $target, $format, $limit): Collection {
            $result = Http::get('https://min-api.cryptocompare.com/data/histohour', [
                'fsym'  => $source,
                'tsym'  => $target,
                'toTs'  => Carbon::now()->unix(),
                'limit' => $limit,
            ])->json()['Data'];

            return collect($result)
                ->groupBy(fn ($day) => Carbon::createFromTimestamp($day['time'])->format($format))
                ->mapWithKeys(function ($transactions, $day) use ($target): array {
                    if (ExchangeRate::isFiat($target)) {
                        return [
                            $day => NumberFormatter::number($transactions->sum('close')),
                        ];
                    }

                    return [
                        $day => NumberFormatter::currency($transactions->sum('close'), '', 8),
                    ];
                });
        });
    }
}
