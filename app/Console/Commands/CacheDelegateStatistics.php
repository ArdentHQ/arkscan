<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Cache\DelegateCache;
use App\Services\Cache\StatisticsCache;
use App\Services\Cache\WalletCache;
use App\Services\Wallets\Aggregates\UniqueVotersAggregate;
use Illuminate\Console\Command;

final class CacheDelegateStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-delegate-statistics';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Cache expensive delegate aggregates.';

    public function handle(StatisticsCache $cache, WalletCache $walletCache): void
    {
        // $mostVotedDelegate = (new UniqueVotersAggregate())->aggregate();
        // if ($mostVotedDelegate !== null) {
        //     $cache->setMostUniqueVoters($mostVotedDelegate->public_key);

        //     $walletCache->setVoterCount($mostVotedDelegate->public_key, $mostVotedDelegate->voter_count);
        // }

        $leastVotedDelegate = (new UniqueVotersAggregate())->aggregate(sortDescending: false);
        dump($leastVotedDelegate);
        if ($leastVotedDelegate !== null) {
            $cache->setLeastUniqueVoters($leastVotedDelegate->public_key);

            $walletCache->setVoterCount($leastVotedDelegate->public_key, $leastVotedDelegate->voter_count);
        }
    }
}
