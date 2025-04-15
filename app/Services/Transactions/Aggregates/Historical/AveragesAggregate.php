<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Historical;

use App\Models\Scopes\MultiPaymentTotalAmountScope;
use App\Models\Transaction;
use App\Services\BigNumber;
use App\Services\Timestamp;
use ArkEcosystem\Crypto\Utils\UnitConverter;
use Brick\Math\RoundingMode;
use Illuminate\Support\Facades\DB;

final class AveragesAggregate
{
    public function aggregate(): array
    {
        /** @var object{count: int, fee: int, amount: BigNumber} */
        $data = Transaction::select([
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(gas_price * COALESCE(receipts.gas_used, 0)) as fee'),
                DB::raw('SUM(value) + COALESCE(SUM(recipient_amount), 0) as amount'),
            ])
            ->join('receipts', 'transactions.hash', '=', 'receipts.transaction_hash')
            ->withScope(MultiPaymentTotalAmountScope::class)
            ->first();

        $daysSinceEpoch = Timestamp::daysSinceEpoch();

        if ($data->count === 0) {
            return [
                'count'  => 0,
                'amount' => 0,
                'fee'    => 0,
            ];
        }

        return [
            'count'  => (int) round($data->count / $daysSinceEpoch),
            'amount' => (int) round(($data->value->toFloat()) / $daysSinceEpoch),
            'fee'    => UnitConverter::formatUnits(
                (string) BigNumber::new($data->fee)->valueOf()->dividedBy($daysSinceEpoch, null, RoundingMode::DOWN),
                'gwei'
            ),
        ];
    }
}
