<?php

declare(strict_types=1);

namespace App\Aggregates;

use App\Contracts\Aggregate;
use App\Models\Block;
use App\Services\BigNumber;

final class ForgedFeesAggregate implements Aggregate
{
    public function aggregate(): string
    {
        return (string) BigNumber::new(Block::sum('total_fee'))->toFloat();
    }
}
