<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Facades\Network;
use App\Services\CryptoCompare;
use App\Services\Transactions\Aggregates\FeeByRangeAggregate;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class CacheChartData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chart:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * The currencies that can be looked up.
     *
     * @var string[]
     */
    protected $currencies = [
        'AUD',
        'BRL',
        'BTC',
        'CAD',
        'CHF',
        'CNY',
        'ETH',
        'EUR',
        'GBP',
        'JPY',
        'KRW',
        'LTC',
        'NZD',
        'RUB',
        'USD',
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (Network::canBeExchanged()) {
            $this->cachePrices();
        }

        $this->cacheFees();
    }

    private function cachePrices(): void
    {
        foreach ($this->currencies as $currency) {
            $prices = (new CryptoCompare())->historical(Network::currency(), $currency);

            $this->cacheKeyValue(
                'chart.prices.day',
                fn () => $prices->take(1),
                'H:s'
            );

            $this->cacheKeyValue(
                'chart.prices.week',
                fn () => $prices->take(7),
                'd.m'
            );

            $this->cacheKeyValue(
                'chart.prices.month',
                fn () => $prices->take(30),
                'd.m'
            );

            $this->cacheKeyValue(
                'chart.prices.quarter',
                fn () => $prices->take(120),
                'W'
            );

            $this->cacheKeyValue(
                'chart.prices.year',
                fn () => $prices->take(365),
                'M'
            );
        }
    }

    private function cacheFees(): void
    {
        $fees  = new FeeByRangeAggregate();
        $today = Carbon::now()->endOfDay();

        $this->cacheKeyValue(
            'chart.fees.day',
            fn () => $fees->aggregate(Carbon::now()->subDay(), $today),
            'H:s'
        );

        $this->cacheKeyValue(
            'chart.fees.week',
            fn () => $fees->aggregate(Carbon::now()->subDays(7), $today),
            'd.m'
        );

        $this->cacheKeyValue(
            'chart.fees.month',
            fn () => $fees->aggregate(Carbon::now()->subDays(30), $today),
            'd.m'
        );

        $this->cacheKeyValue(
            'chart.fees.quarter',
            fn () => $fees->aggregate(Carbon::now()->subDays(120), $today),
            'W'
        );

        $this->cacheKeyValue(
            'chart.fees.year',
            fn () => $fees->aggregate(Carbon::now()->subDays(365), $today),
            'M'
        );
    }

    private function groupByDate(Collection $datasets, string $dateFormat): array
    {
        $datasets = $datasets
            ->groupBy(fn ($_, $key) => Carbon::parse($key)->format($dateFormat))
            ->reverse()
            ->mapWithKeys(fn ($values, $key) => [$key => $values->first()]);

        return [
            'labels'   => $datasets->keys()->toArray(),
            'datasets' => $datasets->values()->toArray(),
        ];
    }

    private function cacheKeyValue(string $key, \Closure $callback, string $dateFormat): void
    {
        Cache::remember(
            $key,
            Carbon::now()->addHour(),
            fn () => $this->groupByDate($callback(), $dateFormat)
        );
    }
}
