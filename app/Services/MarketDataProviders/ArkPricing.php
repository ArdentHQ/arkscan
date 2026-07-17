<?php

declare(strict_types=1);

namespace App\Services\MarketDataProviders;

use App\Contracts\MarketDataProvider;
use App\DTO\MarketData;
use App\Exceptions\MarketDataThrottledException;
use App\Facades\Network;
use App\Models\Exchange;
use App\Services\Cache\CryptoDataCache;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

final class ArkPricing implements MarketDataProvider
{
    /**
     * @return array{prices: array{0:int, 1:float}[], market_caps: array{0:int, 1:float}[], total_volumes: array{0:int, 1:float}[]}|array{}
     */
    public function marketChart(string $source, string $target): array
    {
        return (new CryptoDataCache())->getHistoricalFullResponse($source, $target);
    }

    /**
     * @return array{prices: array{0:int, 1:float}[], market_caps: array{0:int, 1:float}[], total_volumes: array{0:int, 1:float}[]}|array{}
     */
    public function marketChartHourly(string $source, string $target): array
    {
        return (new CryptoDataCache())->getHistoricalHourlyFullResponse($source, $target);
    }

    public function historical(string $source, string $target, string $format = 'Y-m-d'): Collection
    {
        $cache = new CryptoDataCache();

        return $cache->setHistorical($source, $target, $format, function () use ($source, $target, $format, $cache): Collection {
            $response = null;

            try {
                $response = Http::get(
                    $this->url('coins/'.Str::lower($source).'/history'),
                    [
                        'currency' => Str::upper($target),
                        'interval' => 'day',
                        'limit'    => (int) ceil(Network::epoch()->diffInDays()) + 1, // +1 to handle edge case where first day is not returned in full depending on time of day
                    ]
                );
            } catch (\Throwable) {
                //
            }

            $data = $response?->json();

            if ($this->isThrottledResponse($data, $response?->status()) || $this->isEmptyResponse($data)) {
                /** @var Collection<int, mixed> */
                return collect([]);
            }

            /** @var array<int, array<string, string|float|null>> */
            $prices = Arr::get($data, 'data.prices', []);

            // Store value in cache for others to use (statistics)
            $cache->setHistoricalFullResponse($source, $target, $this->marketChartSeries($prices));

            return collect($prices)
                ->mapWithKeys(fn ($item) => [Carbon::parse($item['date'])->format($format) => $item['close']]);
        });
    }

    public function historicalHourly(string $source, string $target, int $limit = 23, string $format = 'Y-m-d H:i:s'): Collection
    {
        $cache = new CryptoDataCache();

        return $cache->setHistoricalHourly($source, $target, $format, $limit, function () use ($source, $target, $format, $limit, $cache): Collection {
            $response = null;

            try {
                $response = Http::get(
                    $this->url('coins/'.Str::lower($source).'/history'),
                    [
                        'currency' => Str::upper($target),
                        'interval' => 'hour',
                        'limit'    => $limit,
                    ]
                );
            } catch (\Throwable) {
                //
            }

            $data = $response?->json();

            if ($this->isThrottledResponse($data, $response?->status()) || $this->isEmptyResponse($data)) {
                /** @var Collection<int, mixed> */
                return collect([]);
            }

            /** @var array<int, array<string, string|float|null>> */
            $prices = Arr::get($data, 'data.prices', []);

            // Store value in cache for others to use (statistics)
            $cache->setHistoricalHourlyFullResponse($source, $target, $this->marketChartSeries($prices));

            return collect($prices)
                ->mapWithKeys(fn ($item) => [Carbon::parse($item['date'])->format($format) => $item['close']]);
        });
    }

    public function priceAndPriceChange(string $baseCurrency, Collection $targetCurrencies): Collection
    {
        $response = null;

        try {
            $response = Http::get(
                $this->url('coins/'.Str::lower($baseCurrency).'/price'),
                [
                    'currencies' => $targetCurrencies->map(fn ($currency) => Str::upper($currency))->values()->all(),
                ]
            );
        } catch (\Throwable) {
            //
        }

        $data = $response?->json();

        if ($this->isThrottledResponse($data, $response?->status()) || $this->isEmptyResponse($data)) {
            /** @var Collection<string, MarketData> */
            return collect([]);
        }

        /** @var array<string, array<string, mixed>> $prices */
        $prices = Arr::get($data, 'data.prices', []);

        return $targetCurrencies
            ->mapWithKeys(fn (string $currency) => [Str::upper($currency) => Arr::get($prices, Str::upper($currency))])
            ->filter()
            ->map(fn (array $price) => MarketData::fromArkPricingApiResponse($price));
    }

    /**
     * @return array{
     *   price: float|int|null,
     *   volume: float|int|null,
     * }
     */
    public function exchangeDetails(Exchange $exchange): array
    {
        $response = null;

        try {
            $response = Http::get($this->url('exchanges/'.$exchange->coingecko_id.'/tickers'));
        } catch (\Throwable) {
            //
        }

        /** @var array<mixed>|null $data */
        $data = $response?->json('data.tickers');

        if ($this->isThrottledResponse($response?->json(), $response?->status()) || $this->isEmptyResponse($data, checkData: false)) {
            throw new MarketDataThrottledException();
        }

        /** @var array<mixed> $data */
        $price  = collect($data)->avg('price');
        $volume = collect($data)->sum('volume');

        return [
            'price'  => $price > 0 ? $price : null,
            'volume' => $volume > 0 ? $volume : null,
        ];
    }

    public function volume(string $baseCurrency): array
    {
        $data = $this->marketData($baseCurrency, $this->allCurrencies());

        return collect($data)
            ->filter(fn ($values) => is_array($values) && Arr::has($values, 'volume'))
            ->mapWithKeys(fn ($values, $currency) => [Str::lower($currency) => $values['volume']])
            ->all();
    }

    public function allTimeHighLow(string $baseCurrency, Collection $targetCurrencies): Collection
    {
        $data = $this->marketData($baseCurrency, $targetCurrencies);

        return $targetCurrencies
            ->mapWithKeys(fn (string $currency) => [Str::upper($currency) => Arr::get($data, Str::upper($currency))])
            ->filter()
            ->map(fn (array $values) => [
                'ath' => $this->allTimeExtreme($values, 'ath'),
                'atl' => $this->allTimeExtreme($values, 'atl'),
            ]);
    }

    private function allCurrencies(): Collection
    {
        /** @var array<string, array<string, string>> */
        $currencies = config('currencies');

        return collect($currencies)->pluck('currency');
    }

    private function marketData(string $baseCurrency, Collection $currencies): array
    {
        $response = null;

        try {
            $response = Http::get(
                $this->url('coins/'.Str::lower($baseCurrency).'/market'),
                [
                    'currencies' => $currencies->map(fn ($currency) => Str::upper($currency))->values()->all(),
                ]
            );
        } catch (\Throwable) {
            //
        }

        $data = $response?->json();

        if ($this->isThrottledResponse($data, $response?->status()) || $this->isEmptyResponse($data)) {
            return [];
        }

        /** @var array<string, mixed> */
        return Arr::get($data, 'data', []);
    }

    /**
     * @return array{value: float, timestamp: int}|null
     */
    private function allTimeExtreme(array $values, string $key): ?array
    {
        /** @var array{price: float|string|null, date: string|null}|null $extreme */
        $extreme = Arr::get($values, $key);

        if ($extreme === null || $extreme['price'] === null || $extreme['date'] === null) {
            return null;
        }

        return [
            'value'     => floatval($extreme['price']),
            'timestamp' => Carbon::parse($extreme['date'])->getTimestamp(),
        ];
    }

    /**
     * Normalize history entries into CoinGecko-style market chart series.
     *
     * @param array<int, array<string, string|float|null>> $prices
     *
     * @return array{prices: array{0:int, 1:float}[], market_caps: array{0:int, 1:float}[], total_volumes: array{0:int, 1:float}[]}
     */
    private function marketChartSeries(array $prices): array
    {
        $series = collect($prices)->map(fn ($item) => [
            'timestamp' => Carbon::parse($item['date'])->getTimestampMs(),
            'price'     => $item['close'],
            'marketCap' => Arr::get($item, 'marketCap'),
            'volume'    => Arr::get($item, 'volume'),
        ]);

        $tuples = fn (string $key) => $series
            ->filter(fn ($item) => $item[$key] !== null)
            ->map(fn ($item) => [$item['timestamp'], floatval($item[$key])])
            ->values()
            ->all();

        return [
            'prices'        => $tuples('price'),
            'market_caps'   => $tuples('marketCap'),
            'total_volumes' => $tuples('volume'),
        ];
    }

    private function url(string $path): string
    {
        $baseUrl = config('arkscan.market_data.ark_pricing.url');

        if (! is_string($baseUrl) || $baseUrl === '') {
            throw new \RuntimeException('ARK_PRICING_URL is not configured');
        }

        return rtrim($baseUrl, '/').'/api/v1/'.$path;
    }

    private function isEmptyResponse(?array $data, bool $checkData = true): bool
    {
        $errorCheck = fn () => false;
        if ($checkData) {
            $errorCheck = fn ($data) => Arr::get($data, 'data') === null;
        }

        return $this->isAcceptableResponse(
            $data,
            'ark_pricing_response_error',
            (int) config('arkscan.market_data.ark_pricing.exception_frequency', 60),
            'Too many empty ArkPricing responses',
            $errorCheck,
            config('arkscan.market_data.ark_pricing.ignore_errors', false) === false,
        );
    }

    private function isThrottledResponse(?array $data, ?int $status = null): bool
    {
        return $this->isAcceptableResponse(
            $data ?? [], // Never null so connection failures count as errors, not throttling
            'ark_pricing_response_throttled',
            (int) config('arkscan.market_data.ark_pricing.exception_frequency', 60),
            'ArkPricing requests are being throttled',
            fn ($data) => $status === 429 || Arr::get($data, 'error') !== null,
            config('arkscan.market_data.ark_pricing.ignore_errors', false) === false,
        );
    }

    private function isAcceptableResponse(
        ?array $data,
        string $cacheKey,
        int $threshold,
        string $message,
        callable $errorCheck,
        bool $throwException = true,
    ): bool {
        $hasError = $errorCheck($data);

        if ($hasError || $data === null) {
            if (Cache::increment($cacheKey) > $threshold) {
                Cache::forget($cacheKey);

                if ($throwException) {
                    throw new MarketDataThrottledException($message);
                }
            }

            return true;
        }

        Cache::forget($cacheKey);

        return false;
    }
}
