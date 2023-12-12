<?php

declare(strict_types=1);

namespace App\Services\Wallets\Aggregates;

use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

final class UniqueVotersAggregate
{
    public function aggregate(bool $sortDescending = true): ?Wallet
    {
        return Wallet::query()
            ->select(DB::raw('attributes->>\'vote\' as public_key, COUNT(*) as voter_count'))
            ->where('balance', '>=', 1 * 1e8)
            ->whereRaw('attributes->>\'vote\' IS NOT NULL')
            ->groupByRaw('attributes->>\'vote\'')
            ->orderBy('voter_count', $sortDescending ? 'desc' : 'asc')
            ->limit(1)
            ->first();
    }
}
