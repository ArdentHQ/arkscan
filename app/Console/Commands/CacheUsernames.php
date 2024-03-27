<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Facades\Network;
use App\Facades\Wallets;
use App\Models\Wallet;
use App\Services\Cache\WalletCache;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;

final class CacheUsernames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-usernames';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Cache all usernames by their address and public key.';

    public function handle(): void
    {
        $cache = app(WalletCache::class);

        $knownWallets = collect(Network::knownWallets());

        Wallets::allWithUsername()
            ->orWhereIn('address', $knownWallets->pluck('address'))
            ->select([
                'address',
                'public_key',
                'attributes',
            ])
            ->get()
            ->each(function (Model $wallet) use ($cache, $knownWallets) : void {
                /** @var Wallet $wallet */
                $knownWallet = $knownWallets->firstWhere('address', $wallet->address);

                if (! is_null($knownWallet)) {
                    $username = $knownWallet['name'];
                } else {
                    $username = $wallet->username();
                }

                if (! is_null($username)) {
                    $cache->setUsernameByAddress($wallet->address, $username);

                    if (! is_null($wallet->public_key)) {
                        $cache->setUsernameByPublicKey($wallet->public_key, $username);
                    }
                }
            });
    }
}