<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Contracts\MarketDataProvider;
use App\Enums\StatsPeriods;
use App\Events\CurrencyUpdate;
use App\Facades\Network;
use App\Services\Cache\CryptoDataCache;
use App\Services\Cache\PriceCache;
use App\Services\Cache\PriceChartCache;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

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
     * @var string|null
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

        $currencies = (new Collection(config('currencies.currencies')))
            ->pluck('currency')
            // Only update currency prices if they're 10+ minutes old
            ->filter(fn ($currency) => Arr::get($currencyLastUpdated, $currency, 0) < Carbon::now()->sub('minutes', 10)->unix())
            ->sort(function ($a, $b) use ($currencyLastUpdated) {
                $aLastUpdated = Arr::get($currencyLastUpdated, $a, 0);
                $bLastUpdated = Arr::get($currencyLastUpdated, $b, 0);

                return $aLastUpdated - $bLastUpdated;
            });

        foreach ($currencies as $currency) {
            $prices       = $marketDataProvider->historical(Network::currency(), $currency);
            $hourlyPrices = $marketDataProvider->historicalHourly(Network::currency(), $currency);

            $dispatchEvent = false;
            foreach (self::PERIODS as $period) {
                $periodPrices = $prices;
                if ($period === StatsPeriods::DAY) {
                    $periodPrices = $hourlyPrices;
                }

                if ($periodPrices->isEmpty()) {
                    continue;
                }

                $crypto->setPrices($currency.'.'.$period, $periodPrices);
                $cache->setHistorical($currency, $period, $this->statsByPeriod($period, $periodPrices));
                $cache->setHistoricalRaw($currency, $period, $this->statsByPeriodRaw($period, $periodPrices));

                $currencyLastUpdated[$currency] = Carbon::now()->unix();

                $dispatchEvent = true;
            }

            if ($dispatchEvent) {
                CurrencyUpdate::dispatch($currency);
            }
        }

        $priceCache->setLastUpdated($currencyLastUpdated);
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
