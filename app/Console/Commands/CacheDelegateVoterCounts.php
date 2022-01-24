<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Facades\Wallets;
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
     * @var string
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

        $select = [
            'wallets.public_key',
            'COUNT(voters.public_key) total',
        ];

        $results = Wallets::allWithUsername()
            ->selectRaw(implode(', ', $select))
            ->join(
                'wallets as voters',
                'wallets.public_key',
                (string) DB::raw('voters.attributes->vote')
            )
            ->groupBy('wallets.public_key')
            ->pluck('total', 'public_key');

        $results->each(fn ($total, $publicKey) => $walletCache->setVoterCount($publicKey, $total));
    }
}
