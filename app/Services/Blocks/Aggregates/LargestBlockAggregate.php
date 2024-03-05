<?php

declare(strict_types=1);

namespace App\Services\Blocks\Aggregates;

use App\Models\Block;

final class LargestBlockAggregate
{
    public function aggregate(): ?Block
    {
        return Block::query()
            ->where('height', '>', 0)
            ->orderBy('total_amount', 'desc')
            ->first();
    }
}
