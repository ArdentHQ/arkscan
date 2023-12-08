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
        return Block::query()
            ->where('id', function ($query) {
                $query->select('block_id')
                    ->from(function ($query) {
                        $query->from('transactions', 't')
                            ->select([
                                'block_id',
                                DB::raw('SUM(amount) as amount'),
                                'multipayment_volume' => function ($query) {
                                    $query->selectRaw('SUM(MP_AMOUNT)')
                                        ->from(function ($query) {
                                            $query->selectRaw('(jsonb_array_elements(mpt.asset->\'payments\')->>\'amount\')::numeric as MP_AMOUNT')
                                                ->from('transactions', 'mpt')
                                                ->whereColumn('mpt.block_id', 't.block_id');
                                        }, 'b');
                                },
                            ])
                            ->groupBy('block_id')
                            ->whereNot('block_height', 1);
                    }, 't')
                    ->orderByRaw('COALESCE(multipayment_volume, 0) + amount desc')
                    ->limit(1)
                    ->first();
            })
            ->first();
    }
}
