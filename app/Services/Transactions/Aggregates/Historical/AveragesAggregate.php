<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Historical;

use App\Services\BigNumber;
use App\Services\Timestamp;
use ArkEcosystem\Crypto\Utils\UnitConverter;
use Brick\Math\RoundingMode;
use Illuminate\Support\Facades\DB;

final class AveragesAggregate
{
    public function aggregate(): array
    {
        $data = (array) DB::connection('explorer')
            ->query()
            ->select([
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as amount'),
                DB::raw('SUM(gas_price * COALESCE(receipts.gas_used, 0)) as fee'),
            ])
            ->from('transactions')
            ->join('receipts', 'transactions.id', '=', 'receipts.id')
            ->first();

        $daysSinceEpoch = Timestamp::daysSinceEpoch();

        if ($data['count'] === 0) {
            return [
                'count'  => 0,
                'amount' => 0,
                'fee'    => 0,
            ];
        }

        return [
            'count'  => (int) round($data['count'] / $daysSinceEpoch),
            'amount' => (int) round(($data['amount'] / config('currencies.notation.crypto', 1e18)) / $daysSinceEpoch),
            'fee'    => UnitConverter::formatUnits(
                (string) BigNumber::new($data['fee'])->valueOf()->dividedBy($daysSinceEpoch, null, RoundingMode::DOWN),
                'gwei'
            ),
        ];
    }
}
