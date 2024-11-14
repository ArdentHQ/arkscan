<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Facades\Network;
use App\Models\Wallet;
use App\Services\Cache\WalletCache;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;

final class CacheKnownWallets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-known-wallets';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Cache all known wallets by their address.';

    public function handle(): void
    {
        $cache = app(WalletCache::class);

        $knownWallets = collect(Network::knownWallets());

        Wallet::whereIn('address', $knownWallets->pluck('address'))
            ->select([
                'address',
                'attributes',
            ])
            ->get()
            ->each(function (Model $wallet) use ($cache, $knownWallets) : void {
                /** @var Wallet $wallet */
                $knownWallet = $knownWallets->firstWhere('address', $wallet->address);

                $username = null;
                if (! is_null($knownWallet)) {
                    $username = $knownWallet['name'];
                }

                if (! is_null($username)) {
                    $cache->setWalletNameByAddress($wallet->address, $username);
                }
            });
    }
}
