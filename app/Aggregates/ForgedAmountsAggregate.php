<?php

declare(strict_types=1);

namespace App\Aggregates;

use App\Contracts\Aggregate;
use App\Facades\Network;
use App\Models\Block;
use App\Services\BigNumber;
use App\Services\NumberFormatter;

final class ForgedAmountsAggregate implements Aggregate
{
    public function aggregate(): string
    {
        return NumberFormatter::currency(
            BigNumber::new(Block::sum('total_amount'))->toFloat(),
            Network::currency()
        );
    }
}
