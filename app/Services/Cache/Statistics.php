<?php

namespace App\Services\Cache;

use App\Services\Timestamp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Statistics
{
    public const STATS_TTL = 300;

    public static function transactionData(): array
    {
        return Cache::remember('transactions:stats', self::STATS_TTL, function () {
            $timestamp = Timestamp::fromUnix(Carbon::now()->subDays(1)->unix())->unix();
            $data      = (array) DB::connection('explorer')
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
                    },
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
                'volume'            => ($data['volume'] ?? 0) + ($data['multipayment_volume'] ?? 0),
                'total_fees'        => $data['total_fees'] ?? 0,
                'average_fee'       => $data['average_fee'] ?? 0,
            ];
        });
    }
}