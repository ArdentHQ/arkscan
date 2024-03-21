<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Facades\Wallets;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\Cache\ValidatorCache;
use App\Services\Cache\WalletCache;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class CacheValidatorVoterCounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-validator-voter-counts';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Cache the voter count for each validator.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $walletCache   = new WalletCache();
        $validatorCache = new ValidatorCache();

        $select = [
            'wallets.public_key',
            'COUNT(voters.public_key) total',
        ];

        $results = Wallets::allWithValidatorPublicKey()
            ->selectRaw(implode(', ', $select))
            ->join(
                'wallets as voters',
                'wallets.public_key',
                (string) DB::raw('voters.attributes->vote')
            )
            ->groupBy('wallets.public_key')
            ->pluck('total', 'public_key');

        $results->each(fn ($total, $publicKey) => $walletCache->setVoterCount($publicKey, $total));

        $validatorCache->setAllVoterCounts($results->toArray());

        $wallets = Wallet::select('balance')
            ->whereRaw("\"attributes\"->>'vote' is not null")
            ->get();

        if ($wallets->count() === 0) {
            return;
        }

        $totalVoted = BigNumber::new(0);
        foreach ($wallets as $wallet) {
            $totalVoted->plus($wallet['balance']->valueOf());
        }

        $validatorCache->setTotalWalletsVoted($wallets->count());
        $validatorCache->setTotalBalanceVoted($totalVoted->toFloat());
    }
}
