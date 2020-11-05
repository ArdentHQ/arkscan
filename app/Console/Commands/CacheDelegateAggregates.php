<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Facades\Wallets;
use App\Services\Cache\DelegateCache;
use App\Services\Monitor\Aggregates\TotalAmountsByPublicKeysAggregate;
use App\Services\Monitor\Aggregates\TotalBlocksByPublicKeysAggregate;
use App\Services\Monitor\Aggregates\TotalFeesByPublicKeysAggregate;
use App\Services\Monitor\Aggregates\TotalRewardsByPublicKeysAggregate;
use Illuminate\Console\Command;

final class CacheDelegateAggregates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-delegate-aggregates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache expensive aggregation data for all delegates.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(DelegateCache $cache)
    {
        $publicKeys = Wallets::allWithUsername()->pluck('public_key')->toArray();

        $cache->setTotalAmounts(fn () => (new TotalAmountsByPublicKeysAggregate())->aggregate($publicKeys));

        $cache->setTotalFees(fn () => (new TotalFeesByPublicKeysAggregate())->aggregate($publicKeys));

        $cache->setTotalRewards(fn () => (new TotalRewardsByPublicKeysAggregate())->aggregate($publicKeys));

        $cache->setTotalBlocks(fn () => (new TotalBlocksByPublicKeysAggregate())->aggregate($publicKeys));
    }
}
