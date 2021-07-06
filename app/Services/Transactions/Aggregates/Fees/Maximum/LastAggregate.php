<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Fees\Maximum;

use App\Models\Transaction;
use App\Services\BigNumber;
use App\Services\Transactions\Aggregates\Concerns\HasQueries;

final class LastAggregate
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

    public function aggregate(): float
    {
        $scope = $this->getScopeByType($this->type);

        $sub = Transaction::select(['id', 'fee'])
            ->withScope($scope)
            ->orderByDesc('timestamp')
            ->limit($this->limit);

        return BigNumber::new(Transaction::fromSub($sub, 'fees')->max('fee') ?? 0)
            ->toFloat();
    }
}
