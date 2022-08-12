<?php

declare(strict_types=1);

namespace App\Services\MarketDataProviders;

use App\Contracts\MarketDataProvider;
use App\DTO\MarketData;
use App\Facades\Network;
use App\Services\Cache\CryptoDataCache;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

final class CoinGecko implements MarketDataProvider
{
    public function historical(string $source, string $target, string $format = 'Y-m-d'): Collection
    {
        return (new CryptoDataCache())->setHistorical($source, $target, $format, function () use ($source, $target, $format): Collection {
            $params = [
                'vs_currency' => Str::lower($target),
                'days'        => Network::epoch()->diffInDays(),
                'interval'    => 'daily',
            ];

            $data = Http::get(
                'https://api.coingecko.com/api/v3/coins/'.Str::lower($source).'/market_chart',
                $params
            )->json();

            if ($this->isEmptyResponse($data) || $this->isThrottledResponse($data)) {
                /** @var Collection<int, mixed> */
                return collect([]);
            }

            /** @var array<int, array<int, string>> */
            $prices = $data['prices'];

            return collect($prices)
                ->mapWithKeys(fn ($item) => [Carbon::createFromTimestampMs($item[0])->format($format) => $item[1]]);
        });
    }

    public function historicalHourly(string $source, string $target, int $limit = 23, string $format = 'Y-m-d H:i:s'): Collection
    {
        return (new CryptoDataCache())->setHistoricalHourly($source, $target, $format, $limit, function () use ($source, $target, $format, $limit): Collection {
            $params = [
                'vs_currency' => Str::lower($target),
                'days'        => strval(ceil($limit / 24)),
            ];

            /** @var array<string, array<string, string>> */
            $data = Http::get(
                'https://api.coingecko.com/api/v3/coins/'.Str::lower($source).'/market_chart',
                $params
            )->json();

            if ($this->isEmptyResponse($data) || $this->isThrottledResponse($data)) {
                /** @var Collection<int, mixed> */
                return collect([]);
            }

            return collect($data['prices'])
                ->groupBy(fn ($item) => Carbon::createFromTimestampMsUTC($item[0])->format('Y-m-d H:').'00:00')
                ->mapWithKeys(fn ($items, $day) => [
                    /* @phpstan-ignore-next-line */
                    Carbon::createFromFormat('Y-m-d H:i:s', $day)->format($format) => collect($items)->average(fn ($item) => $item[1]),
                ])
                // Take the last $limit items (since the API returns a whole days and the limit is per hour)
                ->splice(-$limit - 1);
        });
    }

    public function priceAndPriceChange(string $baseCurrency, Collection $targetCurrencies): Collection
    {
        $data = Http::get('https://api.coingecko.com/api/v3/coins/'.Str::lower($baseCurrency))->json();

        if ($this->isEmptyResponse($data)) {
            /** @var Collection<string, MarketData> */
            return collect([]);
        }

        return $targetCurrencies
            ->mapWithKeys(fn (string $currency) => [strtoupper($currency) => MarketData::fromCoinGeckoApiResponse($currency, $data)]);
    }

    private function isEmptyResponse(?array $data): bool
    {
        return $this->isAcceptableResponse(
            $data,
            'coin_gecko_response_error',
            'Too many empty coinGecko responses',
        );
    }

    private function isThrottledResponse(?array $data): bool
    {
        return $this->isAcceptableResponse(
            $data,
            'coin_gecko_response_throttled',
            'CoinGecko requests are being throttled',
        );
    }

    private function isAcceptableResponse(?array $data, string $cacheKey, string $message): bool
    {
        $errorCode = null;
        if ($data !== null && array_key_exists('status', $data) && array_key_exists('error_code', $data['status'])) {
            $errorCode = $data['status']['error_code'];
        }

        if ($errorCode !== null || $data === null) {
            if (Cache::increment('coin_gecko_response_error') > config('explorer.coingecko_exception_frequency')) {
                Cache::forget('coin_gecko_response_error');

                throw new \Exception($message);
            }

            return true;
        }

        Cache::forget($cacheKey);

        return false;
    }
}
