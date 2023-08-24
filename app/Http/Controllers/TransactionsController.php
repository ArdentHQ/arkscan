<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\BigNumber;
use App\Services\Timestamp;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

final class TransactionsController
{
    public function __invoke(): View
    {
        $data = $this->transactionData();

        return view('app.transactions', [
            'transactionCount' => $data['transaction_count'],
            'volume'           => BigNumber::new($data['volume'])->toFloat(),
            'totalFees'        => BigNumber::new($data['total_fees'])->toFloat(),
            'averageFee'       => BigNumber::new($data['average_fee'])->toFloat(),
        ]);
    }

    private function transactionData(): array
    {
        $data = (array) DB::connection('explorer')
            ->table('transactions')
            ->selectRaw('COUNT(*) as transaction_count')
            ->selectRaw('SUM(amount) as volume')
            ->selectRaw('SUM(fee) as total_fees')
            ->selectRaw('AVG(fee) as average_fee')
            ->where('timestamp', '>', Timestamp::fromUnix(Carbon::now()->subDays(1)->unix())->unix())
            ->first();

        return [
            'transaction_count' => $data['transaction_count'],
            'volume' => $data['volume'] ?? 0,
            'total_fees' => $data['total_fees'] ?? 0,
            'average_fee' => $data['average_fee'] ?? 0,
        ];
    }
}
