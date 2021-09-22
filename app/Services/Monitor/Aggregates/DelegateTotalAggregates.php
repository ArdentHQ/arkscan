<?php

declare(strict_types=1);

namespace App\Services\Monitor\Aggregates;

use App\Facades\Wallets;
use Illuminate\Database\Eloquent\Collection;

final class DelegateTotalAggregates
{
    public function aggregate(): Collection
    {
        return Wallets::allWithUsername()
            ->join('blocks', 'blocks.generator_public_key', '=', 'wallets.public_key')
            ->selectRaw('
                SUM(blocks.total_amount) as total_amount,
                SUM(blocks.total_fee) as total_fee,
                SUM(blocks.reward) as reward,
                COUNT(blocks.generator_public_key) as count,
                blocks.generator_public_key
            ')
            ->groupBy('blocks.generator_public_key')
            ->get();
    }
}
