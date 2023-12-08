<?php

declare(strict_types=1);

namespace App\Services\Wallets\Aggregates;

use App\Models\Wallet;

final class UniqueVotersAggregate
{
    public function aggregate($sortDescending = true): ?Wallet
    {
        return Wallet::query()
            ->select([
                '*',
                'voter_count' => function ($query) {
                    $query->selectRaw('COUNT(*)')
                        ->from('wallets', 'voter_wallets')
                        ->whereRaw('voter_wallets.attributes->>\'vote\' = wallets.public_key');
                },
            ])
            ->from('wallets')
            ->whereNotNull('attributes->delegate')
            ->orderBy('voter_count', $sortDescending ? 'desc' : 'asc')
            ->limit(1)
            ->first();
    }
}
