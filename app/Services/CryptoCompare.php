<?php

declare(strict_types=1);

namespace App\Services;

use App\Facades\Network;
use App\Services\Cache\CryptoCompareCache;
use Carbon\Carbon;
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
                ->mapWithKeys(fn ($transactions, $day) => [$day => NumberFormatter::number($transactions->sum('close'))]);
        });
    }
}
