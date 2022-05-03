<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Facades\Wallets;
use App\Services\Cache\WalletCache;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class CacheDelegateWallets implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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
