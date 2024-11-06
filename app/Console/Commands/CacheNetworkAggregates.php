<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Aggregates\DailyFeeAggregate;
use App\Aggregates\TransactionCountAggregate;
use App\Aggregates\TransactionVolumeAggregate;
use App\Aggregates\VoteCountAggregate;
use App\Aggregates\VotePercentageAggregate;
use App\Models\Scopes\ValidatorRegistrationScope;
use App\Models\Transaction;
use App\Services\Cache\NetworkCache;
use Illuminate\Console\Command;

final class CacheNetworkAggregates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-network-aggregates';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Cache expensive network aggregates.';

    public function handle(NetworkCache $cache): void
    {
        $cache->setVolume((new TransactionVolumeAggregate())->aggregate());

        $cache->setTransactionsCount((new TransactionCountAggregate())->aggregate());

        $cache->setVotesCount((new VoteCountAggregate())->aggregate());

        $cache->setVotesPercentage((new VotePercentageAggregate())->aggregate());

        // TODO: re-add validator registration scope - https://app.clickup.com/t/86duufu8e
        $cache->setValidatorRegistrationCount(Transaction::count());

        $cache->setFeesCollected((new DailyFeeAggregate())->aggregate());
    }
}
