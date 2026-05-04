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
    /**
     * @return array{count: int, amount: int, fee: float}
     */
    public function aggregate(): array
    {
        $data = Transaction::select([
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(transactions.gas_price * COALESCE(gas_used, 0)) as fee'),
                DB::raw('SUM(transactions.value) FILTER (WHERE COALESCE(is_multipayment, FALSE) != TRUE) as value'),
                DB::raw('COALESCE(SUM(recipient_amount), 0) as recipient_value'),
            ])
            ->withScope(MultiPaymentTotalAmountScope::class)
            ->first();

        $count = (int) ($data?->getAttribute('count') ?? 0);

        if ($count === 0 || $data === null) {
            return [
                'count'  => 0,
                'amount' => 0,
                'fee'    => 0,
            ];
        }

        $daysSinceEpoch = Timestamp::daysSinceEpoch();

        $value          = BigNumber::new((string) $data->getAttribute('value'));
        $recipientValue = BigNumber::new((string) $data->getAttribute('recipient_value'));
        $fee            = (string) $data->getAttribute('fee');

        $totalAmount = $value->plus($recipientValue->valueOf())->toFloat();

        return [
            'count'  => (int) round($count / $daysSinceEpoch),
            'amount' => (int) round($totalAmount / $daysSinceEpoch),
            'fee'    => UnitConverter::formatUnits(
                (string) BigNumber::new($fee)->valueOf()->dividedBy($daysSinceEpoch, null, RoundingMode::Down),
                'ark'
            )->toFloat(),
        ];
    }
}
