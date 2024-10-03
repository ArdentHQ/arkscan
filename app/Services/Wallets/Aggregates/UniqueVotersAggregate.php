<?php

declare(strict_types=1);

namespace App\Services\Wallets\Aggregates;

use App\Models\Wallet;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class UniqueVotersAggregate
{
    /**
     * @return Collection{'public_key': string, 'voter_count': int}>
     */
    public function aggregate(bool $sortDescending = true): ?Collection
    {
        // phpstan-ignore-next-line
        $result = Wallet::query()
            ->select(DB::raw('attributes->>\'vote\' as public_key, COUNT(*) as voter_count'))
            ->where('balance', '>=', 1 * config('currencies.notation.crypto', 1e18))
            ->whereRaw('attributes->>\'vote\' IS NOT NULL')
            ->groupByRaw('attributes->>\'vote\'')
            ->orderBy('voter_count', $sortDescending ? 'desc' : 'asc')
            ->limit(1)
            ->first();

        if ($result === null) {
            return $result;
        }

        return collect($result);
    }
}
