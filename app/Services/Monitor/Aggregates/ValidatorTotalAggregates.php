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
            ->selectRaw('SUM(blocks.fee) as fee')
            ->selectRaw('SUM(blocks.reward) as reward')
            ->selectRaw('COUNT(blocks.proposer) as count')
            ->selectRaw('blocks.proposer')
            ->groupBy('blocks.proposer')
            ->get();
    }
}
