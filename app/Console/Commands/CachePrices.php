<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\StatsPeriods;
use App\Facades\Network;
use App\Services\Cache\CryptoCompareCache;
use App\Services\Cache\PriceChartCache;
use App\Services\CryptoCompare;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class CachePrices extends Command
{
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

    public function handle(CryptoCompareCache $crypto, PriceChartCache $cache): void
    {
        if (! Network::canBeExchanged()) {
            return;
        }

        collect(config('currencies'))->values()->each(function ($currency) use ($crypto, $cache): void {
            $currency = $currency['currency'];
            $prices   = CryptoCompare::historical(Network::currency(), $currency);
            $hourlyPrices   = CryptoCompare::historicalHourly(Network::currency(), $currency);

            collect([
                StatsPeriods::DAY,
                StatsPeriods::WEEK,
                StatsPeriods::MONTH,
                StatsPeriods::QUARTER,
                StatsPeriods::YEAR,
                StatsPeriods::ALL,
            ])->each(function ($period) use ($currency, $crypto, $cache, $prices, $hourlyPrices): void {
                if ($period === StatsPeriods::DAY) {
                    $prices = $hourlyPrices;
                }

                $crypto->setPrices($currency.'.'.$period, $prices);
                $method = sprintf('get%s', Str::title($period));

                $cache->setHistorical($currency, $period, $this->{$method}($prices));
            });
        });
    }

    private function getDay(Collection $datasets): Collection
    {
        return $this->groupByDate($datasets->take(-24), 'H:s');
    }

    private function getWeek(Collection $datasets): Collection
    {
        return $this->groupByDate($datasets->take(-7), 'd.m');
    }

    private function getMonth(Collection $datasets): Collection
    {
        return $this->groupByDate($datasets->take(-30), 'd.m');
    }

    private function getQuarter(Collection $datasets): Collection
    {
        return $this->groupByDate($datasets->take(-120), 'd.m');
    }

    private function getYear(Collection $datasets): Collection
    {
        return $this->groupByDate($datasets->take(-365), 'd.m');
    }

    private function getAll(Collection $datasets): Collection
    {
        return $this->groupByDate($datasets, 'm.Y');
    }

    private function groupByDate(Collection $datasets, string $format): Collection
    {
        return $datasets
            ->groupBy(fn ($_, $key) => Carbon::parse($key)->format($format))
            ->mapWithKeys(fn ($values, $key) => [$key => (float) $values->first()]);
    }
}
