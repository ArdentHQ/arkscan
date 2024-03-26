<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Facades\Wallets;
use App\Models\Wallet;
use App\Services\Cache\WalletCache;
use Illuminate\Console\Command;

final class CacheValidatorWallets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-validator-wallets';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Cache all validators by their public key to avoid expensive database queries.';

    public function handle(WalletCache $cache): void
    {
        Wallets::allWithValidatorPublicKey()
            ->orderBy('balance')
            ->chunk(200, function ($wallets) use ($cache): void {
                /** @var Wallet $wallet */
                foreach ($wallets as $wallet) {
                    /** @var string $publicKey */
                    $publicKey = $wallet->public_key;

                    $cache->setValidator($publicKey, $wallet);
                }
            });
    }
}
