<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\BigNumber;
use App\Services\Cache\Statistics;
use Illuminate\Contracts\View\View;

final class TransactionsController
{
    public function __invoke(): View
    {
        $data = Statistics::transactionData();

        return view('app.transactions', [
            'transactionCount' => $data['transaction_count'],
            'volume'           => BigNumber::new($data['volume'])->toFloat(),
            'totalFees'        => BigNumber::new($data['total_fees'])->toFloat(),
            'averageFee'       => BigNumber::new($data['average_fee'])->toFloat(),
        ]);
    }
}
