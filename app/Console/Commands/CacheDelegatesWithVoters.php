<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Wallet;
use App\Services\Cache\WalletCache;
use Illuminate\Console\Command;

final class CacheDelegatesWithVoters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-delegates-with-voters';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache wallets that have been voted for to avoid duplicate queries.';

    /**
     * Execute the console command.
     */
    public function handle(WalletCache $cache): void
    {
        Wallet::where('attributes->delegate->voteBalance', '>=', 0)->cursor()->each(function ($wallet) use ($cache): void {
            if (! is_null($wallet->public_key)) {
                $cache->setVote($wallet->public_key, $wallet);
            }
        });
    }
}
