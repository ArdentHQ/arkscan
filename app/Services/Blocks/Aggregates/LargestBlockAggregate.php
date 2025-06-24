<?php

declare(strict_types=1);

namespace App\Services\Blocks\Aggregates;

use App\Models\Block;
use Illuminate\Database\Query\Builder;

final class LargestBlockAggregate
{
    public function aggregate(): ?Block
    {
        return Block::query()
            ->select([
                'blocks.*',
                'total_value' => function (Builder $query) {
                    $query->selectRaw('SUM(transactions.value)')
                        ->from('transactions')
                        ->whereColumn('transactions.block_hash', 'blocks.hash');
                },
            ])
            ->where('blocks.number', '>', 0)
            ->orderByRaw('total_value desc')
            ->first();
    }
}
