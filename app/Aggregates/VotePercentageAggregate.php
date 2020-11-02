<?php

declare(strict_types=1);

namespace App\Aggregates;

use App\Contracts\Aggregate;
use App\Models\Wallet;
use App\Services\Cache\NetworkCache;
use Mattiasgeniar\Percentage\Percentage;

final class VotePercentageAggregate implements Aggregate
{
    public function aggregate(): string
    {
        return (string) Percentage::calculate((float) Wallet::where('balance', '>', 0)->sum('balance'), (new NetworkCache())->getSupply());
    }
}
