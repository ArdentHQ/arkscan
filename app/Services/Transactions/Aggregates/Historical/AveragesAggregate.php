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
        /** @var object{count: int, fee: string, value: BigNumber, recipient_value: BigNumber} */
        $data = Transaction::select([
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(transactions.gas_price * COALESCE(receipts.gas_used, 0)) as fee'),
                DB::raw('SUM(transactions.value) FILTER (WHERE COALESCE(is_multipayment, FALSE) != TRUE) as value'),
                DB::raw('COALESCE(SUM(recipient_amount), 0) as recipient_value'),
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

        $totalAmount = $data->value->plus((string) $data->recipient_value)->toFloat();

        return [
            'count'  => (int) round($data->count / $daysSinceEpoch),
            'amount' => (int) round($totalAmount / $daysSinceEpoch),
            'fee'    => UnitConverter::formatUnits(
                (string) BigNumber::new($data->fee)->valueOf()->dividedBy($daysSinceEpoch, null, RoundingMode::DOWN),
                'wei'
            ),
        ];
    }
}
