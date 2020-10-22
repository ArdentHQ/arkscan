<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\NumberFormatter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

final class HomeController
{
    public function __invoke(): View
    {
        return view('app.home', [
            'prices' => [
                'day'     => $this->getChart('chart.prices.day'),
                'week'    => $this->getChart('chart.prices.week'),
                'month'   => $this->getChart('chart.prices.month'),
                'quarter' => $this->getChart('chart.prices.quarter'),
                'year'    => $this->getChart('chart.prices.year'),
            ],
            'fees' => [
                'day'     => $this->getChart('chart.fees.day'),
                'week'    => $this->getChart('chart.fees.week'),
                'month'   => $this->getChart('chart.fees.month'),
                'quarter' => $this->getChart('chart.fees.quarter'),
                'year'    => $this->getChart('chart.fees.year'),
            ],
        ]);
    }

    private function getChart(string $cacheKey): array
    {
        $data     = Cache::get($cacheKey, []);
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
