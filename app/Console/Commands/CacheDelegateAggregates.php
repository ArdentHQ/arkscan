<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Wallet;
use App\Services\Monitor\Aggregates\TotalAmountsByPublicKeysAggregate;
use App\Services\Monitor\Aggregates\TotalFeesByPublicKeysAggregate;
use App\Services\Monitor\Aggregates\TotalRewardsByPublicKeysAggregate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

final class CacheDelegateAggregates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delegates:aggregate';

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
    public function handle()
    {
        $publicKeys = Wallet::whereNotNull('attributes->delegate->username')->pluck('public_key')->toArray();

        Cache::put('delegates.totalFees', (new TotalFeesByPublicKeysAggregate())->aggregate($publicKeys));

        Cache::put('delegates.totalAmounts', (new TotalAmountsByPublicKeysAggregate())->aggregate($publicKeys));

        Cache::put('delegates.totalRewards', (new TotalRewardsByPublicKeysAggregate())->aggregate($publicKeys));
    }
}
