<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\BigNumber;
use App\Services\Timestamp;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class TransactionsController
{
    public const STATS_TTL = 300;

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
        return Cache::remember('transactions:stats', self::STATS_TTL, function () {
            $timestamp = Timestamp::fromUnix(Carbon::now()->subDays(1)->unix())->unix();
            $data = (array) DB::connection('explorer')
                ->table('transactions')
                ->select([
                    'multipayment_volume' => function ($query) use ($timestamp) {
                        $query->selectRaw('SUM(MP_AMOUNT)')
                            ->from(function ($query) use ($timestamp) {
                                $query->selectRaw('(jsonb_array_elements(t.asset->\'payments\')->>\'amount\')::numeric as MP_AMOUNT')
                                    ->from('transactions', 't')
                                    ->where('type', 6)
                                    ->where('timestamp', '>', $timestamp);
                            }, 'b');
                    }
                ])
                ->selectRaw('COUNT(*) as transaction_count')
                ->selectRaw('SUM(amount) as volume')
                ->selectRaw('SUM(fee) as total_fees')
                ->selectRaw('AVG(fee) as average_fee')
                ->from('transactions')
                ->where('timestamp', '>', $timestamp)
                ->first();

            return [
                'transaction_count' => $data['transaction_count'],
                'volume'            => $data['volume'] + $data['multipayment_volume'] ?? 0,
                'total_fees'        => $data['total_fees'] ?? 0,
                'average_fee'       => $data['average_fee'] ?? 0,
            ];
        });
    }
}
