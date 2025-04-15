<?php

declare(strict_types=1);

namespace App\Services\Monitor\Aggregates;

use App\Facades\Wallets;
use Illuminate\Database\Eloquent\Collection;

final class ValidatorTotalAggregates
{
    public function aggregate(): Collection
    {
        return Wallets::allWithValidatorPublicKey()
            ->join('blocks', 'blocks.generator_address', '=', 'wallets.address')
            ->selectRaw('
                SUM(blocks.amount) as amount,
                SUM(blocks.fee) as fee,
                SUM(blocks.reward) as reward,
                COUNT(blocks.generator_address) as count,
                blocks.generator_address
            ')
            ->groupBy('blocks.generator_address')
            ->get();
    }
}
