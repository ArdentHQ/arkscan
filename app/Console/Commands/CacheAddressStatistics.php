<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Facades\Network;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Addresses\Aggregates\HoldingsAggregate;
use App\Services\Cache\StatisticsCache;
use ARKEcosystem\Foundation\UserInterface\Support\DateFormat;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class CacheAddressStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-address-statistics';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Cache expensive address statistics';

    public function handle(StatisticsCache $cache): void
    {
        $holdings = (new HoldingsAggregate())->aggregate();

        if ($holdings !== null) {
            $cache->setAddressHoldings($holdings->toArray());
        }

        $genesis = Transaction::orderBy('block_height', 'asc')->limit(1)->first();
        if ($genesis !== null) {
            $cache->setGenesisAddress([
                'address' => $genesis->sender->address,
                'value'   => Carbon::createFromTimestamp(Network::epoch()->timestamp)->format(DateFormat::DATE),
            ]);
        }

        $newest = Wallet::first(); // TODO: https://app.clickup.com/t/86dqtd90x

        $query = Wallet::query()
            ->select('wallets.address', DB::raw('MIN(transactions.timestamp) as min_timestamp'))
            ->leftJoin('transactions', function ($join) {
                $join->on('wallets.public_key', '=', 'transactions.sender_public_key')
                    ->orOn('wallets.address', '=', 'transactions.recipient_id');
            })
            ->where('wallets.updated_at', '>', '2023-12-01')
            ->whereNotNull('transactions.sender_public_key')
            ->whereNotNull('transactions.recipient_id')
            ->groupBy('wallets.address')
            ->orderBy('min_timestamp', 'desc')
            ->limit(1)
            ->get();
        Log::debug($query);

        if ($newest !== null) {
            $cache->setNewestAddress([
                'address' => $newest->address,
                'value'   => Carbon::createFromTimestamp(Carbon::now()->timestamp)->format(DateFormat::DATE), // TODO: https://app.clickup.com/t/86dqtd90x
            ]);
        }

        /** @var array{address?: string, tx_count?: int} $mostTransactions */
        $mostTransactions = (array) DB::connection('explorer')
            ->query()
            ->select([
                DB::raw('count(transactions.id) as tx_count'),
                DB::raw('wallets.address'),
            ])
            ->from('transactions')
            ->join('wallets', 'transactions.sender_public_key', '=', 'wallets.public_key')
            ->groupBy('wallets.address')
            ->orderBy('tx_count', 'desc')
            ->limit(1)
            ->first();
        if (count($mostTransactions) > 0) {
            $cache->setMostTransactions([
                'address' => $mostTransactions['address'],
                'value'   => $mostTransactions['tx_count'],
            ]);
        }

        $largest = Wallet::orderBy('balance', 'desc')->limit(1)->first();
        if ($largest !== null) {
            $cache->setLargestAddress([
                'address' => $largest->address,
                'value'   => $largest->balance->toFloat(),
            ]);
        }
    }
}
