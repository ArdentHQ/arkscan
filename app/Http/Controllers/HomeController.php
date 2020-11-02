<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Cache\FeeChartCache;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\PriceChartCache;
use App\Services\ExchangeRate;
use App\Services\NumberFormatter;
use App\Services\Settings;
use Illuminate\Support\Arr;
use Illuminate\View\View;

final class HomeController
{
    public function __invoke(NetworkCache $network, PriceChartCache $prices, FeeChartCache $fees): View
    {
        return view('app.home', [
            'prices' => [
                'day'     => $this->getChart($prices->getDay(Settings::currency())),
                'week'    => $this->getChart($prices->getWeek(Settings::currency())),
                'month'   => $this->getChart($prices->getMonth(Settings::currency())),
                'quarter' => $this->getChart($prices->getQuarter(Settings::currency())),
                'year'    => $this->getChart($prices->getYear(Settings::currency())),
            ],
            'fees' => [
                'day'     => $this->getChart($fees->getDay()),
                'week'    => $this->getChart($fees->getWeek()),
                'month'   => $this->getChart($fees->getMonth()),
                'quarter' => $this->getChart($fees->getQuarter()),
                'year'    => $this->getChart($fees->getYear()),
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

    private function getChart(array $data): array
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
}
