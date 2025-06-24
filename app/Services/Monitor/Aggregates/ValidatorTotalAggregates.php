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
            ->join('blocks', 'blocks.proposer', '=', 'wallets.address')
            ->selectRaw('
                SUM(transactions.value) as amount,
                SUM(blocks.fee) as fee,
                SUM(blocks.reward) as reward,
                COUNT(blocks.proposer) as count,
                blocks.proposer
            ')
            ->join('transactions', 'transactions.block_hash', '=', 'blocks.hash')
            ->groupBy('blocks.proposer')
            ->get();
    }
}
