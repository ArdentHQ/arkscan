<?php

declare(strict_types=1);

namespace App\Actions;

use App\Facades\Network;
use App\Services\BigNumber;
use App\Services\Cache\NetworkCache;
use Illuminate\Support\Facades\DB;

final class CacheNetworkSupply
{
    public static function execute(): float
    {
        return (new NetworkCache())->setSupply(function (): float {
            $amount = DB::connection('explorer')
                ->query()
                ->selectRaw('coalesce(balance, 0) - coalesce(transaction_amount, 0) AS amount')
                ->from(function ($query) {
                    $query->select([
                        'balance' => function ($query) {
                            $query->selectRaw('SUM(balance)')
                                ->from('wallets')
                                ->whereNot('public_key', Network::genesisPublicKey());
                        },
                        'transaction_amount' => function ($query) {
                            $query->selectRaw('SUM(amount)')
                                ->from('transactions')
                                ->where('recipient_id', Network::genesisAddress());
                        },
                    ]);
                }, 'data')
                ->first()->amount;

            return BigNumber::new($amount)->valueOf()->toFloat();
        });
    }
}
