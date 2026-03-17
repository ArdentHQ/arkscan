<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia\Concerns;

use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Pagination\LengthAwarePaginator;

trait WithWalletRelations
{
    /**
     * Load senderWallet, recipientWallet, and votedFor relations
     * using a single wallet query instead of three separate eager loads.
     */
    private function loadWalletRelations(LengthAwarePaginator $paginator): void
    {
        $transactions = $paginator->getCollection();

        if ($transactions->isEmpty()) {
            return;
        }

        $addresses = $transactions->pluck('from')
            ->merge($transactions->pluck('to'))
            ->merge($transactions->map->votedForAddress)
            ->filter()
            ->unique();

        $wallets = Wallet::whereIn('address', $addresses)
            ->get()
            ->keyBy(fn (Wallet $w) => strtolower($w->address));

        $transactions->each(function (Transaction $tx) use ($wallets) {
            $tx->setRelation('senderWallet', $wallets->get(strtolower($tx->from)));
            $tx->setRelation('recipientWallet', $tx->to !== null ? $wallets->get(strtolower($tx->to)) : null);
            $tx->setRelation('votedFor', $tx->votedForAddress !== null ? $wallets->get(strtolower($tx->votedForAddress)) : null);
        });
    }
}
