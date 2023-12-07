<?php

declare(strict_types=1);

namespace App\Services\Blocks\Aggregates;

use App\Models\Block;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

final class LargestBlockAggregate
{
    public function aggregate(): ?Block
    {
        $subquery = Transaction::select(DB::raw('block_id, jsonb_array_elements(asset->\'payments\')->>\'amount\' as multipayment_amount'));

        return Block::select(DB::raw('b.*, total_amount + sum(multipayment_amount::bigint) as consolidated_amount'))
            ->from('blocks', 'b')
            // @phpstan-ignore-next-line
            ->join(DB::raw('('.$subquery->toSql().') AS d'), 'd.block_id', '=', 'b.id')
            ->groupBy('b.id')
            ->orderBy('consolidated_amount', 'desc')
            ->limit(1)
            ->first();
    }
}
