<?php

declare(strict_types=1);

namespace App\Actions;

use App\Facades\Network;
use App\Models\Transaction;
use App\Models\Wallet;
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
                ->selectRaw('coalesce(transaction_amount, 0) + coalesce(balance, 0) AS amount')
                ->from(function ($query) {
                    $query->select([
                        'balance' => function ($query) {
                            $query->selectRaw('SUM(balance)')
                                ->from('wallets')
                                ->whereNot('public_key', '03cd365c09739561385c710b34a4b5ef2363dc461efd1336f7d0e16f0d2956cdf0');
                        },
                        'transaction_amount' => function ($query) {
                            $query->selectRaw('SUM(amount)')
                                ->from('transactions')
                                ->where('recipient_id', 'DGG1ovZUrPcBXR84ei2L69YyiXQvQfkUqV');
                        }
                    ]);
                }, 'data')
                ->first()->amount;

            return BigNumber::new($amount)->valueOf()->toFloat();
        });
    }
}
