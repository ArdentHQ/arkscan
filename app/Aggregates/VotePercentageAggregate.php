<?php

declare(strict_types=1);

namespace App\Aggregates;

use App\Contracts\Aggregate;
use App\Models\Wallet;
use App\Services\Blockchain\NetworkStatus;
use App\Services\NumberFormatter;
use Mattiasgeniar\Percentage\Percentage;

final class VotePercentageAggregate implements Aggregate
{
    public function aggregate(): string
    {
        $result = Percentage::calculate(intval(Wallet::sum('balance') / 1e8), NetworkStatus::supply());

        return NumberFormatter::number($result);
    }
}
