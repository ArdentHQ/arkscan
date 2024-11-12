<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Fees;

use App\Models\Transaction;
use App\Services\Transactions\Aggregates\Concerns\HasQueries;
use ArkEcosystem\Crypto\Utils\UnitConverter;

final class LastFeeAggregate
{
    use HasQueries;

    private int $limit = 20;

    public function setLimit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function aggregate(): array
    {
        // TODO: add transaction type scope - https://app.clickup.com/t/86dur8fj6
        $fees = Transaction::query()
            ->selectRaw('gas_price * COALESCE(receipts.gas_used, 0) as fee')
            ->join('receipts', 'transactions.id', '=', 'receipts.id')
            ->orderByDesc('timestamp')
            ->limit($this->limit)
            ->pluck('fee')
            ->map(fn ($fee) => UnitConverter::formatUnits((string) $fee));

        return [
            'minimum' => $fees->min() ?? 0, // @phpstan-ignore-line
            'average' => $fees->max() ?? 0,
            'maximum' => $fees->avg() ?? 0, // @phpstan-ignore-line
        ];
    }
}
