<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\CoreTransactionTypeEnum;
use App\Facades\Rounds;
use App\Models\Block;
use App\Models\Transaction;
use App\Services\Cache\StatisticsCache;
use App\Services\Cache\WalletCache;
use App\Services\Wallets\Aggregates\UniqueVotersAggregate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
        $mostVotedDelegate = (new UniqueVotersAggregate())->aggregate();
        if ($mostVotedDelegate !== null) {
            $cache->setMostUniqueVoters($mostVotedDelegate->public_key);

            $walletCache->setVoterCount($mostVotedDelegate->public_key, $mostVotedDelegate->voter_count);
        }

        $leastVotedDelegate = (new UniqueVotersAggregate())->aggregate(sortDescending: false);
        if ($leastVotedDelegate !== null) {
            $cache->setLeastUniqueVoters($leastVotedDelegate->public_key);

            $walletCache->setVoterCount($leastVotedDelegate->public_key, $leastVotedDelegate->voter_count);
        }

        $activeDelegates = Rounds::allByRound(Rounds::current())->pluck(['public_key']);

        $newestActiveDelegateTx = Transaction::where('type', '=', CoreTransactionTypeEnum::DELEGATE_REGISTRATION)
            ->whereIn('sender_public_key', $activeDelegates)
            ->orderBy('timestamp', 'desc')
            ->limit(1)
            ->first();

        if ($newestActiveDelegateTx !== null) {
            $cache->setNewestActiveDelegate($newestActiveDelegateTx->sender_public_key, $newestActiveDelegateTx->timestamp);
        }

        $oldestActiveDelegateTx = Transaction::where('type', '=', CoreTransactionTypeEnum::DELEGATE_REGISTRATION)
            ->whereIn('sender_public_key', $activeDelegates)
            ->orderBy('timestamp', 'asc')
            ->limit(1)
            ->first();

        if ($oldestActiveDelegateTx !== null) {
            $cache->setOldestActiveDelegate($oldestActiveDelegateTx->sender_public_key, $oldestActiveDelegateTx->timestamp);
        }

        $mostBlocksForged = Block::select(DB::raw('COUNT(*), generator_public_key'))->groupBy('generator_public_key')->orderBy('count', 'desc')->limit(1)->first();
        if ($mostBlocksForged !== null) {
            $cache->setMostBlocksForged($mostBlocksForged->generator_public_key);
        }
    }
}
