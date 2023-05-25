<?php

declare(strict_types=1);

namespace App\Services\MarketDataProviders;

use App\DTO\MarketData;
use App\Exceptions\ApiNotAvailableException;
use App\Facades\Network;
use App\Models\Exchange;
use App\Services\Cache\CryptoDataCache;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

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
     *   price: float,
     *   volume: float,
     * }|null
     */
    public function exchangeDetails(Exchange $exchange): array | null
    {
        $data = null;

        try {
            $data = Http::get('https://api.coingecko.com/api/v3/exchanges/'.$exchange->coingecko_id.'/tickers', [
                'coin_ids' => 'ark',
            ])->json('tickers');
        } catch (\Throwable) {
            //
        }

        if ($this->isEmptyResponse($data) || $this->isThrottledResponse($data)) {
            throw new ApiNotAvailableException();
        }

        /** @var array<mixed> $data */
        $possibleTargets = collect(['USDT', 'USDC', 'BUSD']);

        $tickerData =  collect($data)
            ->filter(fn (array $ticker) => $possibleTargets->contains($ticker['target']))
            ->first();

        if ($tickerData === null) {
            return null;
        }

        return [
            'price'  => $tickerData['converted_last']['usd'],
            'volume' => $tickerData['converted_volume']['usd'],
        ];
    }

    private function isEmptyResponse(?array $data): bool
    {
        return $this->isAcceptableResponse(
            $data,
            'coingecko_response_error',
            (int) config('explorer.coingecko_exception_frequency', 60),
            'Too many empty CoinGecko responses',
            fn ($data) => Arr::get($data, 'status.error_code') !== null,
        );
    }

    private function isThrottledResponse(?array $data): bool
    {
        return $this->isAcceptableResponse(
            $data,
            'coingecko_response_throttled',
            (int) config('explorer.coingecko_exception_frequency', 60),
            'CoinGecko requests are being throttled',
            fn ($data) => Arr::get($data, 'status.error_code') !== null,
        );
    }
}
