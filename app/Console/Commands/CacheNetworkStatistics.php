<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Aggregates\DailyFeeAggregate;
use App\Aggregates\TransactionCountAggregate;
use App\Aggregates\TransactionVolumeAggregate;
use App\Aggregates\VoteCountAggregate;
use App\Aggregates\VotePercentageAggregate;
use App\Models\Block;
use App\Models\Scopes\DelegateRegistrationScope;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Cache\NetworkCache;
use Illuminate\Console\Command;

final class CacheNetworkStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:statistics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(NetworkCache $cache)
    {
        $cache->setHeight(function (): int {
            $block = Block::latestByHeight()->first();

            if (is_null($block)) {
                return 0;
            }

            return $block->height->toNumber();
        });

        $cache->setSupply(fn () => Wallet::sum('balance'));

        $cache->setVolume(fn () => (new TransactionVolumeAggregate())->aggregate());

        $cache->setTransactionsCount(fn () => (new TransactionCountAggregate())->aggregate());

        $cache->setVotesCount(fn () => (new VoteCountAggregate())->aggregate());

        $cache->setVotesPercentage(fn () => (new VotePercentageAggregate())->aggregate());

        $cache->setDelegateRegistrationCount(fn () => Transaction::withScope(DelegateRegistrationScope::class)->count());

        $cache->setFeesCollected(fn (): string => (new DailyFeeAggregate())->aggregate());
    }
}
