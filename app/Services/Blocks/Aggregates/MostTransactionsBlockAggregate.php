<?php

declare(strict_types=1);

namespace App\Services\Blocks\Aggregates;

use App\Models\Block;

final class MostTransactionsBlockAggregate
{
    public function aggregate(): Block
    {
        return Block::orderBy('number_of_transactions', 'desc')
            ->whereNot('height', 1)
            ->limit(1)
            ->first();
    }
}
