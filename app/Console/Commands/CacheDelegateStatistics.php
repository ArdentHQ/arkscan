<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Concerns\DispatchesStatisticsEvents;
use App\Enums\CoreTransactionTypeEnum;
use App\Events\Statistics\DelegateDetails;
use App\Facades\Network;
use App\Facades\Rounds;
use App\Models\Block;
use App\Models\Transaction;
use App\Services\Cache\StatisticsCache;
use App\Services\Cache\WalletCache;
use App\Services\Wallets\Aggregates\UniqueVotersAggregate;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

final class CacheDelegateStatistics extends Command
{
    use DispatchesStatisticsEvents;

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
            $publicKey = $mostVotedDelegate['public_key'];
            if ($cache->getMostUniqueVoters() !== $publicKey) {
                $this->hasChanges = true;
            } elseif ($publicKey !== null && $walletCache->getVoterCount($publicKey) !== $mostVotedDelegate['voter_count']) {
                $this->hasChanges = true;
            }

            $cache->setMostUniqueVoters($publicKey);

            $walletCache->setVoterCount($publicKey, $mostVotedDelegate['voter_count']);
        }

        $leastVotedDelegate = (new UniqueVotersAggregate())->aggregate(sortDescending: false);
        if ($leastVotedDelegate !== null) {
            if (! $this->hasChanges) {
                if ($cache->getLeastUniqueVoters() !== $leastVotedDelegate['public_key']) {
                    $this->hasChanges = true;
                } elseif ($leastVotedDelegate['public_key'] !== null && $walletCache->getVoterCount($leastVotedDelegate['public_key']) !== $leastVotedDelegate['voter_count']) {
                    $this->hasChanges = true;
                }
            }

            $cache->setLeastUniqueVoters($leastVotedDelegate['public_key']);

            $walletCache->setVoterCount($leastVotedDelegate['public_key'], $leastVotedDelegate['voter_count']);
        }

        $activeDelegates = Rounds::allByRound(Rounds::current())->pluck(['public_key']);

        $newestActiveDelegateTx = Transaction::where('type', '=', CoreTransactionTypeEnum::DELEGATE_REGISTRATION)
            ->whereIn('sender_public_key', $activeDelegates)
            ->orderBy('timestamp', 'desc')
            ->limit(1)
            ->first();

        if ($newestActiveDelegateTx !== null) {
            if (! $this->hasChanges && Arr::get($cache->getNewestActiveDelegate() ?? [], 'publicKey') !== $newestActiveDelegateTx->sender_public_key) {
                $this->hasChanges = true;
            }

            $cache->setNewestActiveDelegate($newestActiveDelegateTx->sender_public_key, (int) Network::epoch()->timestamp + $newestActiveDelegateTx->timestamp);
        }

        $oldestActiveDelegateTx = Transaction::where('type', '=', CoreTransactionTypeEnum::DELEGATE_REGISTRATION)
            ->whereIn('sender_public_key', $activeDelegates)
            ->orderBy('timestamp', 'asc')
            ->limit(1)
            ->first();

        if ($oldestActiveDelegateTx !== null) {
            $cache->setOldestActiveDelegate($oldestActiveDelegateTx->sender_public_key, (int) Network::epoch()->timestamp + $oldestActiveDelegateTx->timestamp);
        }

        $mostBlocksForged = Block::select(DB::raw('COUNT(*), generator_public_key'))->groupBy('generator_public_key')->orderBy('count', 'desc')->limit(1)->first();
        if ($mostBlocksForged !== null) {
            if (! $this->hasChanges && $cache->getMostBlocksForged() !== $mostBlocksForged->generator_public_key) {
                $this->hasChanges = true;
            }

            $cache->setMostBlocksForged($mostBlocksForged->generator_public_key);
        }

        $this->dispatchEvent(DelegateDetails::class);
    }
}
