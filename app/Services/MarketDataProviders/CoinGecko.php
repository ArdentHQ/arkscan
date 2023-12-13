<?php

declare(strict_types=1);

namespace App\Services\MarketDataProviders;

use App\DTO\MarketData;
use App\Exceptions\CoinGeckoThrottledException;
use App\Facades\Network;
use App\Models\Exchange;
use App\Services\Cache\CryptoDataCache;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * @phpstan-type MarketDataArray array{prices: array{0:int, 1:float}[], market_caps: array{0:int, 1:float}[], total_volumes: array{0:int, 1:float}[]}
 */
final class CoinGecko extends AbstractMarketDataProvider
{
    public function historical(string $source, string $target, string $format = 'Y-m-d'): Collection
    {
        return (new CryptoDataCache())->setHistorical($source, $target, $format, function () use ($source, $target, $format): Collection {
            $params = [
                'vs_currency' => Str::lower($target),
                'days'        => Network::epoch()->diffInDays(),
                'interval'    => 'daily',
            ];

            $data = null;

            try {
                $data = Http::get(
                    'https://api.coingecko.com/api/v3/coins/'.Str::lower($source).'/market_chart',
                    $params
                )->json();
            } catch (\Throwable) {
                //
            }

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

    /**
     * @return MarketDataArray|array{}
     */
    public function historicalAll(string $source, string $target, int $limit = 1): array
    {
        $params = [
            'vs_currency' => Str::lower($target),
            'days'        => $limit,
        ];

        $data = null;

        try {
            /** @var MarketDataArray */
            $data = Http::get(
                'https://api.coingecko.com/api/v3/coins/'.Str::lower($source).'/market_chart',
                $params
            )->json();
        } catch (\Throwable) {
            //
        }

        if ($this->isEmptyResponse($data) || $this->isThrottledResponse($data) || $data === null) {
            return [];
        }

        return $data;
    }

    public function historicalHourly(string $source, string $target, int $limit = 23, string $format = 'Y-m-d H:i:s'): Collection
    {
        return (new CryptoDataCache())->setHistoricalHourly($source, $target, $format, $limit, function () use ($source, $target, $format, $limit): Collection {
            $params = [
                'vs_currency' => Str::lower($target),
                'days'        => strval(ceil($limit / 24)),
            ];

            $data = null;

            try {
                /** @var array<string, array<string, string>> */
                $data = Http::get(
                    'https://api.coingecko.com/api/v3/coins/'.Str::lower($source).'/market_chart',
                    $params
                )->json();
            } catch (\Throwable) {
                //
            }

            if ($this->isEmptyResponse($data) || $this->isThrottledResponse($data)) {
                /** @var Collection<int, mixed> */
                return collect([]);
            }

            /** @var array<string, array<string, string>> $data */
            return collect($data['prices'])
                ->groupBy(fn ($item) => Carbon::createFromTimestampMsUTC($item[0])->format('Y-m-d H:00:00'))
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
        $data = null;

        try {
            $data = Http::get('https://api.coingecko.com/api/v3/coins/'.Str::lower($baseCurrency))->json();
        } catch (\Throwable) {
            //
        }

        if ($this->isEmptyResponse($data) || $this->isThrottledResponse($data)) {
            /** @var Collection<string, MarketData> */
            return collect([]);
        }

        return $targetCurrencies
            ->mapWithKeys(fn (string $currency) => [strtoupper($currency) => MarketData::fromCoinGeckoApiResponse($currency, $data)]);
    }

    /**
     * @return array{
     *   price: float|int|null,
     *   volume: float|int|null,
     * }
     */
    public function exchangeDetails(Exchange $exchange): array
    {
        $data = null;

        try {
            $data = Http::get('https://api.coingecko.com/api/v3/exchanges/'.$exchange->coingecko_id.'/tickers', [
                'coin_ids' => 'ark',
            ])->json('tickers');
        } catch (\Throwable) {
            //
        }

        if ($this->isThrottledResponse($data) || $this->isEmptyResponse($data)) {
            throw new CoinGeckoThrottledException();
        }

        /** @var array<mixed> $data */
        $price  = collect($data)->avg('converted_last.usd');
        $volume = collect($data)->sum('converted_volume.usd');

        return [
            'price'  => $price > 0 ? $price : null,
            'volume' => $volume > 0 ? $volume : null,
        ];
    }

    public function volume(string $baseCurrency): array
    {
        // Just fetch it here and return it
        $data = null;

        try {
            $data = Http::get('https://api.coingecko.com/api/v3/coins/'.Str::lower($baseCurrency).'?tickers=false&community_data=false&developer_data=false&sparkline=false')->json();
        } catch (\Throwable) {
            //
        }

        if ($this->isThrottledResponse($data) || $this->isEmptyResponse($data)) {
            throw new CoinGeckoThrottledException();
        }

        /** @var array<mixed> $volume */
        $volume = Arr::get($data, 'market_data.total_volume', []);

        return $volume;
    }

    private function isEmptyResponse(?array $data): bool
    {
        return $this->isAcceptableResponse(
            $data,
            'coingecko_response_error',
            (int) config('arkscan.coingecko_exception_frequency', 60),
            'Too many empty CoinGecko responses',
            fn ($data) => Arr::get($data, 'status.error_code') !== null,
        );
    }

    private function isThrottledResponse(?array $data): bool
    {
        return $this->isAcceptableResponse(
            $data,
            'coingecko_response_throttled',
            (int) config('arkscan.coingecko_exception_frequency', 60),
            'CoinGecko requests are being throttled',
            fn ($data) => Arr::get($data, 'status.error_code') !== null,
        );
    }
}
