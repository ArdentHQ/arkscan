<?php

declare(strict_types=1);

namespace App\Services\MarketDataProviders;

use App\DTO\MarketData;
use App\Facades\Network;
use App\Models\Exchange;
use App\Services\Cache\CryptoDataCache;
use ARKEcosystem\Foundation\NumberFormatter\ResolveScientificNotation;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

final class CryptoCompare extends AbstractMarketDataProvider
{
    public function historical(string $source, string $target, string $format = 'Y-m-d'): Collection
    {
        return (new CryptoDataCache())->setHistorical($source, $target, $format, function () use ($source, $target, $format): Collection {
            $data = null;

            try {
                $data = Http::get(
                    'https://min-api.cryptocompare.com/data/histoday',
                    [
                        'fsym'  => $source,
                        'tsym'  => $target,
                        'toTs'  => Carbon::now()->unix(),
                        'limit' => Network::epoch()->diffInDays(),
                    ]
                )->json();
            } catch (\Throwable) {
                //
            }

            if ($this->isEmptyResponse($data) || $this->isThrottledResponse($data)) {
                /** @var Collection<int, mixed> */
                return collect([]);
            }

            /** @var array<int, array<string, string>> */
            $prices = $data['Data'];

            return collect($prices)
                ->groupBy(fn ($day) => Carbon::createFromTimestamp($day['time'])->format($format))
                ->mapWithKeys(fn ($transactions, $day) => [$day => $transactions->sum('close')]);
        });
    }

    public function historicalHourly(string $source, string $target, int $limit = 23, string $format = 'Y-m-d H:i:s'): Collection
    {
        return (new CryptoDataCache())->setHistoricalHourly($source, $target, $format, $limit, function () use ($source, $target, $format, $limit): Collection {
            $data = null;

            try {
                $data = Http::get(
                    'https://min-api.cryptocompare.com/data/histohour',
                    [
                        'fsym'  => $source,
                        'tsym'  => $target,
                        'toTs'  => Carbon::now()->unix(),
                        'limit' => $limit,
                    ]
                )->json();
            } catch (\Throwable) {
                //
            }

            if ($this->isEmptyResponse($data) || $this->isThrottledResponse($data)) {
                /** @var Collection<int, mixed> */
                return collect([]);
            }

            /** @var array<int, array<string, string>> */
            $prices = $data['Data'];

            return collect($prices)
                ->groupBy(fn ($day) => Carbon::createFromTimestamp($day['time'])->format($format))
                ->mapWithKeys(fn ($transactions, $day) => [
                    $day => ResolveScientificNotation::execute($transactions->sum('close')),
                ]);
        });
    }

    public function exchangeVolume(string $source, string $target): Collection
    {
        return (new CryptoDataCache())->setExchangeVolume($source, $target, function () use ($source, $target) {
            $prices = new Collection();

            $maxDays = 2000;
            $toDate  = Network::epoch();
            $days    = $toDate->diffInDays();

            for ($i = 0; $i < $days; $i += $maxDays) {
                $toDate->addDays($maxDays);
                $toTs  = $toDate->unix();
                $limit = min($maxDays, (int) ceil($days - $i));

                try {
                    $data = Http::get(
                        'https://min-api.cryptocompare.com/data/symbol/histoday',
                        [
                            'fsym'  => $source,
                            'tsym'  => $target,
                            'toTs'  => $toTs,
                            'limit' => $limit,
                        ]
                    )->json();

                    if ($this->isEmptyResponse($data) || $this->isThrottledResponse($data)) {
                        return collect([]);
                    }

                    $prices = $prices->concat($data['Data']);
                } catch (\Throwable $e) {
                    //
                }
            }

            if ($prices->isEmpty()) {
                /** @var Collection<string, MarketData> */
                return collect([]);
            }

            return collect($prices)
                ->map(fn ($volumeData) => [
                    'time'   => $volumeData['time'],
                    'volume' => $volumeData['total_volume_total'],
                ]);
        });
    }

    public function priceAndPriceChange(string $baseCurrency, Collection $targetCurrencies): Collection
    {
        $data = null;

        try {
            $data = Http::get(
                'https://min-api.cryptocompare.com/data/pricemultifull',
                [
                    'fsyms'  => $baseCurrency,
                    'tsyms'  => $targetCurrencies->join(','),
                ]
            )->json();
        } catch (\Throwable) {
            //
        }

        if ($this->isEmptyResponse($data, false) || $this->isThrottledResponse($data, false)) {
            /** @var Collection<string, MarketData> */
            return collect([]);
        }

        return $targetCurrencies->mapWithKeys(fn ($targetCurrency) => [
            strtoupper($targetCurrency) => MarketData::fromCryptoCompareApiResponse($baseCurrency, $targetCurrency, $data),
        ]);
    }

    /**
     * @return array{
     *   price: float|int|null,
     *   volume: float|int|null,
     * }
     */
    public function exchangeDetails(Exchange $exchange): array
    {
        throw new \Exception('Not implemented');
    }

    public function volume(string $baseCurrency): array
    {
        throw new \Exception('Not implemented');
    }

    private function isEmptyResponse(?array $data, bool $checkStatus = true): bool
    {
        $errorCheck = fn () => false;
        if ($checkStatus) {
            $errorCheck = fn ($data) => Arr::get($data, 'Data', []) === [];
        }

        return $this->isAcceptableResponse(
            $data,
            'cryptocompare_response_error',
            (int) config('arkscan.market_data.cryptocompare.exception_frequency', 60),
            'Too many empty CryptoCompare responses',
            $errorCheck,
            config('arkscan.market_data.cryptocompare.ignore_errors', false) === false,
        );
    }

    private function isThrottledResponse(?array $data, bool $checkStatus = true): bool
    {
        $errorCheck = fn () => false;
        if ($checkStatus) {
            $errorCheck = fn ($data) => Arr::get($data, 'Response') === 'Error';
        }

        return $this->isAcceptableResponse(
            $data,
            'cryptocompare_response_throttled',
            (int) config('arkscan.market_data.cryptocompare.exception_frequency', 60),
            'CryptoCompare requests are being throttled',
            $errorCheck,
            config('arkscan.market_data.cryptocompare.ignore_errors', false) === false,
        );
    }
}
