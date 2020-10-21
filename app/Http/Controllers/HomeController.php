<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Transactions\Aggregates\FeeByRangeAggregate;
use Carbon\Carbon;
use Illuminate\View\View;

final class HomeController
{
    private FeeByRangeAggregate $aggregate;

    public function __construct(FeeByRangeAggregate $aggregate)
    {
        $this->aggregate = $aggregate;
    }

    public function __invoke(): View
    {
        return view('app.home', [
            'fees' => [
                'daily'   => $this->aggregate->aggregate(Carbon::now()->startOfDay(), Carbon::now()->endOfDay()),
                'weekly'  => $this->aggregate->aggregate(Carbon::now()->subDays(7), Carbon::now()->endOfDay()),
                'monthly' => $this->aggregate->aggregate(Carbon::now()->subDays(30), Carbon::now()->endOfDay()),
            ],
        ]);
    }
}
