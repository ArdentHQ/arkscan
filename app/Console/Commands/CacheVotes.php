<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Wallet;
use App\Services\Cache\WalletCache;
use Illuminate\Console\Command;

final class CacheVotes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:votes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache wallets that have been voted for to avoid duplicate queries.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(WalletCache $cache)
    {
        $publicKeys = Wallet::query()
            ->distinct('attributes->vote')
            ->whereNotNull('attributes->delegate->username')
            ->pluck('attributes')
            ->pluck('vote')
            ->toArray();

        Wallet::whereIn('public_key', $publicKeys)->get()->each(function ($wallet) use ($cache): void {
            if (! is_null($wallet->public_key)) {
                $cache->setVote($wallet->public_key, $wallet);
            }
        });
    }
}
