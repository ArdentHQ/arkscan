<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\CoreTransactionTypeEnum;
use App\Facades\Network;
use App\Facades\Rounds;
use App\Models\Block;
use App\Models\Transaction;
use App\Services\Cache\StatisticsCache;
use App\Services\Cache\WalletCache;
use App\Services\Wallets\Aggregates\UniqueVotersAggregate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class CacheValidatorStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-validator-statistics';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Cache expensive validator aggregates.';

    public function handle(StatisticsCache $cache, WalletCache $walletCache): void
    {
        $mostVotedValidator = (new UniqueVotersAggregate())->aggregate();
        if ($mostVotedValidator !== null) {
            $cache->setMostUniqueVoters($mostVotedValidator['public_key']);

            $walletCache->setVoterCount($mostVotedValidator['public_key'], $mostVotedValidator['voter_count']);
        }

        $leastVotedValidator = (new UniqueVotersAggregate())->aggregate(sortDescending: false);
        if ($leastVotedValidator !== null) {
            $cache->setLeastUniqueVoters($leastVotedValidator['public_key']);

            $walletCache->setVoterCount($leastVotedValidator['public_key'], $leastVotedValidator['voter_count']);
        }

        $activeValidators = Rounds::current()->validators;

        $newestActiveValidatorTx = Transaction::where('type', '=', CoreTransactionTypeEnum::VALIDATOR_REGISTRATION)
            ->whereIn('sender_public_key', $activeValidators)
            ->orderBy('timestamp', 'desc')
            ->limit(1)
            ->first();

        if ($newestActiveValidatorTx !== null) {
            $cache->setNewestActiveValidator(
                $newestActiveValidatorTx->sender_public_key,
                $newestActiveValidatorTx->timestamp
            );
        }

        $oldestActiveValidatorTx = Transaction::where('type', '=', CoreTransactionTypeEnum::VALIDATOR_REGISTRATION)
            ->whereIn('sender_public_key', $activeValidators)
            ->orderBy('timestamp', 'asc')
            ->limit(1)
            ->first();

        if ($oldestActiveValidatorTx !== null) {
            $cache->setOldestActiveValidator(
                $oldestActiveValidatorTx->sender_public_key,
                $oldestActiveValidatorTx->timestamp
            );
        }

        $mostBlocksForged = Block::select(DB::raw('COUNT(*), generator_public_key'))
            ->groupBy('generator_public_key')
            ->orderBy('count', 'desc')
            ->limit(1)
            ->first();

        if ($mostBlocksForged !== null) {
            $cache->setMostBlocksForged($mostBlocksForged->generator_public_key);
        }
    }
}
