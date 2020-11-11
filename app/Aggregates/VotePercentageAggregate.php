<?php

declare(strict_types=1);

namespace App\Aggregates;

use App\Actions\CacheNetworkSupply;
use App\Contracts\Aggregate;
use App\Models\Wallet;
use Mattiasgeniar\Percentage\Percentage;

final class VotePercentageAggregate implements Aggregate
{
    public function aggregate(): string
    {
        return (string) Percentage::calculate(
            (float) Wallet::query()
                ->where('balance', '>', 0)
                ->whereNotNull('attributes->vote')
                ->sum('balance'),
            CacheNetworkSupply::execute()
        );
    }
}
