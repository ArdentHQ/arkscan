<?php

declare(strict_types=1);

namespace App\Aggregates;

use App\Contracts\Aggregate;
use App\Models\Wallet;

final class VoteCountAggregate implements Aggregate
{
    public function aggregate(): string
    {
        return (string) (Wallet::query()
            ->where('balance', '>', 0)
            ->whereNotNull('attributes->vote')
            ->sum('balance') / 1e8);
    }
}
