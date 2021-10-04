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
     * @var string
     */
    protected $description = 'Executes all caching commands. DO NOT USE THIS IN PRODUCTION!';

    public function handle(): void
    {
        collect([
            'explorer:cache-network-aggregates',
            'explorer:cache-fees',
            'explorer:cache-transactions',
            'explorer:cache-prices',
            'explorer:cache-currencies-data',
            'explorer:cache-currencies-history --no-delay',
            'explorer:cache-delegate-aggregates',
            'explorer:cache-delegate-performance',
            'explorer:cache-delegate-productivity',
            'explorer:cache-delegate-resignation-ids',
            'explorer:cache-delegate-usernames',
            'explorer:cache-delegate-wallets',
            'explorer:cache-delegates-with-voters',
            'explorer:cache-delegate-voter-counts',
            'explorer:cache-multi-signature-addresses',
        ])->map(fn (string $command) => Artisan::call($command));
    }
}
