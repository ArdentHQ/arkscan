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

        $fees = Transaction::query()
            ->select('fee')
            ->withScope($scope)
            ->orderByDesc('timestamp')
            ->limit($this->limit)
            ->pluck('fee')
            ->map(fn ($fee) => $fee->toFloat());

        return [
            'minimum' => $fees->min() ?? 0,
            'average' => $fees->avg() ?? 0,
            'maximum' => $fees->max() ?? 0,
        ];
    }
}
