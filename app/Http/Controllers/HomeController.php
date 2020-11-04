<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Cache\FeeCache;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\PriceChartCache;
use App\Services\ExchangeRate;
use App\Services\NumberFormatter;
use App\Services\Settings;
use Illuminate\Support\Arr;
use Illuminate\View\View;

final class HomeController
{
    public function __invoke(NetworkCache $network, PriceChartCache $prices, FeeCache $fees): View
    {
        return view('app.home', [
            'prices' => [
                'day'     => $this->formatPriceChart($prices->getDay(Settings::currency())),
                'week'    => $this->formatPriceChart($prices->getWeek(Settings::currency())),
                'month'   => $this->formatPriceChart($prices->getMonth(Settings::currency())),
                'quarter' => $this->formatPriceChart($prices->getQuarter(Settings::currency())),
                'year'    => $this->formatPriceChart($prices->getYear(Settings::currency())),
            ],
            'fees' => [
                'day'     => $this->formatFeeChart($fees->all('day')),
                'week'    => $this->formatFeeChart($fees->all('week')),
                'month'   => $this->formatFeeChart($fees->all('month')),
                'quarter' => $this->formatFeeChart($fees->all('quarter')),
                'year'    => $this->formatFeeChart($fees->all('year')),
            ],
            'aggregates' => [
                'price'             => ExchangeRate::now(),
                'volume'            => $network->getVolume(),
                'transactionsCount' => $network->getTransactionsCount(),
                'votesCount'        => $network->getVotesCount(),
                'votesPercentage'   => $network->getVotesPercentage(),
            ],
        ]);
    }

    private function formatPriceChart(array $data): array
    {
        $labels   = Arr::get($data, 'labels', []);
        $datasets = Arr::get($data, 'datasets', []);
        $numbers  = collect($datasets)->map(fn ($dataset) => (float) $dataset);

        return [
            'labels'   => $labels,
            'datasets' => $datasets,
            'min'      => NumberFormatter::number($numbers->min()),
            'avg'      => NumberFormatter::number($numbers->avg()),
            'max'      => NumberFormatter::number($numbers->max()),
        ];
    }

    private function formatFeeChart(array $data): array
    {
        $labels   = Arr::get($data, 'historical.labels', []);
        $datasets = Arr::get($data, 'historical.datasets', []);
        $numbers  = collect($datasets)->map(fn ($dataset) => (float) $dataset);

        return [
            'labels'   => $labels,
            'datasets' => $datasets,
            'min'      => NumberFormatter::number($data['min']),
            'avg'      => NumberFormatter::number($data['avg']),
            'max'      => NumberFormatter::number($data['max']),
        ];
    }
}
