<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Facades\Wallets;
use App\Services\BigNumber;
use App\Services\Cache\DelegateCache;
use App\Services\Cache\WalletCache;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class CacheDelegateVoterCounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-delegate-voter-counts';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Cache the voter count for each delegate.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $walletCache = new WalletCache();
        $delegateCache = new DelegateCache();

        $select = [
            'wallets.public_key',
            'wallets.balance',
            'COUNT(voters.public_key) total',
        ];

        $results = Wallets::allWithUsername()
            ->selectRaw(implode(', ', $select))
            ->join(
                'wallets as voters',
                'wallets.public_key',
                (string) DB::raw('voters.attributes->vote')
            )
            ->groupBy('wallets.public_key', 'wallets.address')
            ->get();

        $results->each(fn ($wallet) => $walletCache->setVoterCount($wallet['public_key'], $wallet['total']));

        $voterCount = 0;
        $totalVoted = BigNumber::new(0);
        foreach ($results as $wallet) {
            $voterCount += $wallet['total'];

            $totalVoted->plus($wallet['balance']->valueOf());
        }

        $delegateCache->setVoterCount(fn () => $voterCount);
        $delegateCache->setTotalVoted(fn () => $totalVoted->toFloat());
    }
}
