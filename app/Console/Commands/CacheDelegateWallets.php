<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Facades\Wallets;
use App\Services\Cache\WalletCache;
use Illuminate\Console\Command;

final class CacheDelegateWallets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-delegate-wallets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache all delegates by their public key to avoid expensive database queries.';

    public function handle(WalletCache $cache): void
    {
        Wallets::allWithUsername()
            ->orderBy('balance')
            ->chunk(200, function ($wallets) use ($cache): void {
                foreach ($wallets as $wallet) {
                    $cache->setDelegate($wallet->public_key, $wallet);
                }
            });
    }
}
