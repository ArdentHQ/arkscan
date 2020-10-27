<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Aggregates\TransactionCountAggregate;
use App\Aggregates\TransactionVolumeAggregate;
use App\Aggregates\VoteCountAggregate;
use App\Aggregates\VotePercentageAggregate;
use App\Enums\CacheKeyEnum;
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
    protected $signature = 'cache:charts';

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

        $this->cacheStatistics();
    }

    private function cachePrices(): void
    {
        foreach ($this->currencies as $currency) {
            $prices = (new CryptoCompare())->historical(Network::currency(), $currency);

            Cache::put('prices.'.$currency, $prices);

            $this->cacheKeyValue(
                'chart.prices.day',
                $this->groupByDate($prices->take(1), 'H:s'),
            );

            $this->cacheKeyValue(
                'chart.prices.week',
                $this->groupByDate($prices->take(7), 'd.m'),
            );

            $this->cacheKeyValue(
                'chart.prices.month',
                $this->groupByDate($prices->take(30), 'd.m'),
            );

            $this->cacheKeyValue(
                'chart.prices.quarter',
                $this->groupByDate($prices->take(120), 'W'),
            );

            $this->cacheKeyValue(
                'chart.prices.year',
                $this->groupByDate($prices->take(365), 'M'),
            );
        }
    }

    private function cacheFees(): void
    {
        $fees  = new FeeByRangeAggregate();
        $today = Carbon::now()->endOfDay();

        $this->cacheKeyValue(
            'chart.fees.day',
            $this->groupByDate($fees->aggregate(Carbon::now()->subDay(), $today, 'H:s'), 'H:s')
        );

        $this->cacheKeyValue(
            'chart.fees.week',
            $fees->aggregate(Carbon::now()->subDays(7), $today, 'd.m')
        );

        $this->cacheKeyValue(
            'chart.fees.month',
            $fees->aggregate(Carbon::now()->subDays(30), $today, 'd.m')
        );

        $this->cacheKeyValue(
            'chart.fees.quarter',
            $fees->aggregate(Carbon::now()->subDays(120), $today, 'M')
        );

        $this->cacheKeyValue(
            'chart.fees.year',
            $fees->aggregate(Carbon::now()->subDays(365), $today, 'M')
        );
    }

    private function cacheStatistics(): void
    {
        Cache::put(CacheKeyEnum::VOLUME, (new TransactionVolumeAggregate())->aggregate());
        Cache::put(CacheKeyEnum::TRANSACTIONS_COUNT, (new TransactionCountAggregate())->aggregate());
        Cache::put(CacheKeyEnum::VOTES_COUNT, (new VoteCountAggregate())->aggregate());
        Cache::put(CacheKeyEnum::VOTES_PERCENTAGE, (new VotePercentageAggregate())->aggregate());
    }

    private function cacheKeyValue(string $key, Collection $datasets): void
    {
        Cache::put($key, [
            'labels'   => $datasets->keys()->toArray(),
            'datasets' => $datasets->values()->toArray(),
        ]);
    }

    private function groupByDate(Collection $datasets, string $dateFormat): Collection
    {
        return $datasets
            ->groupBy(fn ($_, $key) => Carbon::parse($key)->format($dateFormat))
            ->mapWithKeys(fn ($values, $key) => [$key => $values->first()])
            ->ksort();
    }
}
