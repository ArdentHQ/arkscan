<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Contracts\MarketDataProvider;
use App\Enums\StatsPeriods;
use App\Events\CurrencyUpdate;
use App\Facades\Network;
use App\Models\Price;
use App\Services\Cache\CryptoDataCache;
use App\Services\Cache\PriceCache;
use App\Services\Cache\PriceChartCache;
use App\Services\MarketDataProviders\ArkPricing;
use App\Services\MarketDataProviders\CoinGecko;
use App\Services\MarketDataProviders\CryptoCompare;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class CachePrices extends Command
{
    public const PERIODS = [
        StatsPeriods::DAY,
        StatsPeriods::WEEK,
        StatsPeriods::MONTH,
        StatsPeriods::QUARTER,
        StatsPeriods::YEAR,
        StatsPeriods::ALL,
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache prices and exchange rates.';

    public function handle(
        CryptoDataCache $crypto,
        PriceChartCache $cache,
        PriceCache $priceCache,
        MarketDataProvider $marketDataProvider,
    ): void {
        if (! Network::canBeExchanged()) {
            return;
        }

        $currencyLastUpdated = $priceCache->getLastUpdated();

        $allCurrencies = (new Collection(config('currencies')))->pluck('currency');

        $currencies = $allCurrencies
            // Only update currency prices if they're 10+ minutes old
            ->filter(fn ($currency) => Arr::get($currencyLastUpdated, $currency, 0) < Carbon::now()->sub('minutes', 10)->unix())
            ->sort(function ($a, $b) use ($currencyLastUpdated) {
                $aLastUpdated = Arr::get($currencyLastUpdated, $a, 0);
                $bLastUpdated = Arr::get($currencyLastUpdated, $b, 0);

                return $aLastUpdated - $bLastUpdated;
            });

        $skipped = $allCurrencies->diff($currencies);
        if ($skipped->isNotEmpty()) {
            $this->line(sprintf('Skipping %s - updated within the last 10 minutes', $skipped->implode(', ')));
        }

        foreach ($currencies as $currency) {
            $prices       = $marketDataProvider->historical(Network::currency(), $currency);
            $hourlyPrices = $marketDataProvider->historicalHourly(Network::currency(), $currency);

            if ($prices->isEmpty() || $hourlyPrices->isEmpty()) {
                $this->warn(sprintf(
                    '%s: %d daily, %d hourly prices%s',
                    $currency,
                    $prices->count(),
                    $hourlyPrices->count(),
                    $this->emptyResponseHint($marketDataProvider),
                ));
            } else {
                $this->info(sprintf('%s: %d daily, %d hourly prices', $currency, $prices->count(), $hourlyPrices->count()));
            }

            $dispatchEvent = false;
            foreach (self::PERIODS as $period) {
                $periodPrices = $prices;
                if ($period === StatsPeriods::DAY) {
                    if ($hourlyPrices->isEmpty()) {
                        continue;
                    }

                    $crypto->setPrices($currency.'.'.$period, $hourlyPrices);

                    $cache->setHistorical($currency, $period, $this->statsByPeriod($period, $hourlyPrices));
                    $cache->setHistoricalRaw($currency, $period, $this->statsByPeriodRaw($period, $hourlyPrices));
                } elseif ($periodPrices->isEmpty()) {
                    continue;
                } elseif ($period === StatsPeriods::WEEK || $period === StatsPeriods::ALL) {
                    /** @var Collection $priceMapping */
                    $priceMapping = collect($periodPrices)
                        ->map(fn (float $value, string $timestamp) => [
                            'timestamp' => $timestamp.' 00:00:00',
                            'currency'  => $currency,
                            'value'     => $value,
                        ])->values();

                    Price::upsert($priceMapping->toArray(), ['timestamp', 'currency'], ['value']);

                    $allPrices = Price::where('currency', $currency)
                        ->orderBy('timestamp')
                        ->get()
                        ->mapWithKeys(fn (Price $price) => [$price->timestamp->format('Y-m-d') => $price['value']]);

                    $crypto->setPrices($currency.'.'.$period, $allPrices);

                    if ($period === StatsPeriods::ALL) {
                        $cache->setHistorical($currency, $period, $this->statsByPeriod($period, $allPrices));
                        $cache->setHistoricalRaw($currency, $period, $this->statsByPeriodRaw($period, $allPrices));
                    } else {
                        $cache->setHistorical($currency, $period, $this->statsByPeriod($period, $periodPrices));
                        $cache->setHistoricalRaw($currency, $period, $this->statsByPeriodRaw($period, $periodPrices));
                    }
                } else {
                    $cache->setHistorical($currency, $period, $this->statsByPeriod($period, $periodPrices));
                    $cache->setHistoricalRaw($currency, $period, $this->statsByPeriodRaw($period, $periodPrices));
                }

                $currencyLastUpdated[$currency] = Carbon::now()->unix();

                $dispatchEvent = true;
            }

            if ($dispatchEvent) {
                CurrencyUpdate::dispatch($currency);
            }
        }

        $priceCache->setLastUpdated($currencyLastUpdated);
    }

    /**
     * Explain an empty provider response using the consecutive failure
     * counters tracked by AbstractMarketDataProvider::isAcceptableResponse().
     */
    private function emptyResponseHint(MarketDataProvider $marketDataProvider): string
    {
        $prefix = match ($marketDataProvider::class) {
            ArkPricing::class    => 'ark_pricing',
            CoinGecko::class     => 'coingecko',
            CryptoCompare::class => 'cryptocompare',
            default              => null,
        };

        if ($prefix === null) {
            return '';
        }

        $providerName = class_basename($marketDataProvider);

        $throttled = (int) Cache::get($prefix.'_response_throttled', 0);
        if ($throttled > 0) {
            return sprintf(' (%s is throttling - %d consecutive throttled responses)', $providerName, $throttled);
        }

        $errors = (int) Cache::get($prefix.'_response_error', 0);
        if ($errors > 0) {
            return sprintf(' (%d consecutive empty responses from %s)', $errors, $providerName);
        }

        return '';
    }

    private function statsByPeriod(string $period, Collection $datasets): Collection
    {
        return match ($period) {
            'day'     => $this->groupByDate($datasets->take(-24), 'H:s'),
            'week'    => $this->groupByDate($datasets->take(-7), 'd.m'),
            'month'   => $this->groupByDate($datasets->take(-30), 'd.m'),
            'quarter' => $this->groupByDate($datasets->take(-120), 'd.m'),
            'year'    => $this->groupByDate($datasets->take(-365), 'd.m'),
            default   => $this->groupByDate($datasets, 'm.Y'),
        };
    }

    private function statsByPeriodRaw(string $period, Collection $datasets): Collection
    {
        $data = match ($period) {
            'day'     => $datasets->take(-24),
            'week'    => $datasets->take(-7),
            'month'   => $datasets->take(-30),
            'quarter' => $datasets->take(-120),
            'year'    => $datasets->take(-365),
            default   => $datasets,
        };

        return $this->groupByDate($data, 'U');
    }

    private function groupByDate(Collection $datasets, string $format): Collection
    {
        return $datasets
            ->groupBy(fn ($_, $key) => Carbon::parse($key)->format($format))
            ->mapWithKeys(fn ($values, $key) => [$key => (float) $values->first()]);
    }
}
