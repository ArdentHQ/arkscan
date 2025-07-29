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
        $walletCache    = new WalletCache();
        $validatorCache = new ValidatorCache();

        $select = [
            'wallets.address',
            'COUNT(voters.address) total',
        ];

        $results = Wallets::allWithValidatorPublicKey()
            ->selectRaw(implode(', ', $select))
            ->join(
                'wallets as voters',
                'wallets.address',
                (string) DB::raw('voters.attributes->vote')->getValue(DB::connection()->getQueryGrammar())
            )
            ->groupBy('wallets.address')
            ->pluck('total', 'address');

        $results->each(fn ($total, $address) => $walletCache->setVoterCount($address, $total));

        $validatorCache->setAllVoterCounts($results->toArray());

        $wallets = Wallet::select('balance')
            ->whereRaw("\"attributes\"->>'vote' is not null")
            ->get();

        if ($wallets->count() === 0) {
            return;
        }

        $totalVoted = BigNumber::zero();
        foreach ($wallets as $wallet) {
            $totalVoted->plus($wallet['balance']->valueOf());
        }

        $validatorCache->setTotalWalletsVoted($wallets->count());
        $validatorCache->setTotalBalanceVoted($totalVoted->toFloat());
    }
}
