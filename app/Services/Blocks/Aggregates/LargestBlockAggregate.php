<?php

declare(strict_types=1);

namespace App\Services\Blocks\Aggregates;

use App\Models\Block;
use Illuminate\Support\Facades\DB;

final class LargestBlockAggregate
{
    public function aggregate(): ?Block
    {
        return Block::query()
            ->where('id', function ($query) {
                $query->select('block_id')
                    ->from(function ($query) {
                        $query->from('transactions', 't')
                            ->select([
                                'block_id',
                                DB::raw('SUM(amount) as amount'),
                            ])
                            ->groupBy('block_id')
                            ->whereNot('block_height', 1);
                    }, 't')
                    ->orderByRaw('amount desc')
                    ->limit(1)
                    ->first();
            })
            ->first();
    }
}
