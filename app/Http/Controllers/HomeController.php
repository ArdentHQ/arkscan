<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Transactions\Aggregates\FeeByRangeAggregate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class HomeController
{
    public function __invoke(Request $request): View
    {
        return view('app.home', [
            'fees' => [
                'daily'   => FeeByRangeAggregate::aggregate(Carbon::now()->startOfDay(), Carbon::now()->endOfDay()),
                'weekly'  => FeeByRangeAggregate::aggregate(Carbon::now()->subDays(7), Carbon::now()->endOfDay()),
                'monthly' => FeeByRangeAggregate::aggregate(Carbon::now()->subDays(30), Carbon::now()->endOfDay()),
            ],
        ]);
    }
}
