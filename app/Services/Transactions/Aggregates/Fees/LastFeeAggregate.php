<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Fees;

use App\Models\Transaction;
use App\Services\Transactions\Aggregates\Concerns\HasQueries;

final class LastFeeAggregate
{
    use HasQueries;

    private string $type;

    private int $limit = 20;

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function setLimit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function aggregate(): array
    {
        $scope = $this->getScopeByType($this->type);

        $fees = Transaction::withScope(new $scope())
            ->selectRaw('gas_price * COALESCE(receipts.gas_used, 0) as fee')
            ->join('receipts', 'transactions.id', '=', 'receipts.id')
            ->orderByDesc('timestamp')
            ->limit($this->limit)
            ->pluck('fee');

        return [
            'minimum' => $fees->min() ?? 0, // @phpstan-ignore-line
            'average' => $fees->avg() ?? 0,
            'maximum' => $fees->max() ?? 0, // @phpstan-ignore-line
        ];
    }
}
