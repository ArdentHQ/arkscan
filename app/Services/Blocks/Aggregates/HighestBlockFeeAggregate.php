<?php

declare(strict_types=1);

namespace App\Services\Blocks\Aggregates;

use App\Models\Block;

final class HighestBlockFeeAggregate
{
    public function aggregate(): Block
    {
        return Block::orderBy('total_fee', 'desc')
            ->limit(1)
            ->first();
    }
}
