<?php

declare(strict_types=1);

namespace App\Services\Cache;

use App\Services\Timestamp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class Statistics
{
    public const STATS_TTL = 300;

    public static function transactionData(): array
    {
        return Cache::remember('transactions:stats', self::STATS_TTL, function () {
            $timestamp = Timestamp::fromUnix(Carbon::now()->subDays(1)->unix())->unix() * 1000;
            $data      = (array) DB::connection('explorer')
                ->table('transactions')
                ->selectRaw('COUNT(*) as transaction_count')
                ->selectRaw('SUM(amount) as volume')
                ->selectRaw('SUM(fee) as total_fees')
                ->selectRaw('AVG(fee) as average_fee')
                ->from('transactions')
                ->where('timestamp', '>', $timestamp)
                ->first();

            return [
                'transaction_count' => $data['transaction_count'],
                'volume'            => ($data['volume'] ?? 0),
                'total_fees'        => $data['total_fees'] ?? 0,
                'average_fee'       => $data['average_fee'] ?? 0,
            ];
        });
    }
}
