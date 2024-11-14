<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

final class CacheDevelopmentData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-development-data';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Executes all caching commands. DO NOT USE THIS IN PRODUCTION!';

    public function handle(): void
    {
        collect([
            'explorer:cache-network-aggregates',
            'explorer:cache-fees',
            'explorer:cache-transactions',
            'explorer:cache-prices',
            'explorer:cache-volume',
            'explorer:cache-currencies-data',
            'explorer:cache-validator-aggregates',
            'explorer:cache-validator-performance',
            'explorer:cache-validator-productivity',
            'explorer:cache-validator-resignation-ids',
            'explorer:cache-known-wallets',
            'explorer:cache-validator-wallets',
            'explorer:cache-validators-with-voters',
            'explorer:cache-validator-voter-counts',
            'explorer:cache-blocks',
            'explorer:cache-transactions',
            'explorer:cache-address-statistics',
            'explorer:cache-validator-statistics',
            'explorer:cache-market-data-statistics',
            'explorer:cache-annual-statistics --all',
        ])->map(fn (string $command) => Artisan::call($command));
    }
}
