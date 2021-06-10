<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Facades\Network;
use App\Services\Cache\CryptoCompareCache;
use App\Services\Cache\PriceChartCache;
use App\Services\CryptoCompare;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

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

    public function handle(CryptoCompareCache $crypto, PriceChartCache $chart): void
    {
        if (! Network::canBeExchanged()) {
            return;
        }

        foreach (array_values(config('currencies')) as $currency) {
            $currency = $currency['currency'];
            $prices   = CryptoCompare::historical(Network::currency(), $currency);

            $crypto->setPrices($currency, $prices);

            // TODO: remove if obsolete after stats page is implemented
            // $chart->setDay($currency, $this->groupByDate($prices->take(1), 'H:s'));

            // $chart->setWeek($currency, $this->groupByDate($prices->take(7), 'd.m'));

            // $chart->setMonth($currency, $this->groupByDate($prices->take(30), 'd.m'));

            // $chart->setQuarter($currency, $this->groupByDate($prices->take(120), 'W'));

            // $chart->setYear($currency, $this->groupByDate($prices->take(365), 'M'));
        }
    }

    // private function groupByDate(Collection $datasets, string $dateFormat): Collection
    // {
    //     return $datasets
    //         ->groupBy(fn ($_, $key) => Carbon::parse($key)->format($dateFormat))
    //         ->mapWithKeys(fn ($values, $key) => [$key => $values->first()])
    //         ->ksort();
    // }
}
