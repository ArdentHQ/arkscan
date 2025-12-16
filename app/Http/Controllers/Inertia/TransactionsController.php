<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use App\Services\BigNumber;
use App\Services\Cache\StatisticsCache;
use Inertia\Inertia;
use Inertia\Response;

final class TransactionsController
{
    public function __invoke(): Response
    {
        $data = (new StatisticsCache())->getTransactionData();

        return Inertia::render('Transactions/Transactions', [
            'transactionCount' => $data['transaction_count'],
            'volume'           => BigNumber::new($data['volume'])->toFloat(),
            'totalFees'        => BigNumber::new($data['total_fees'])->toFloat(),
            'averageFee'       => BigNumber::new($data['average_fee'])->toFloat(),
        ]);
    }
}
