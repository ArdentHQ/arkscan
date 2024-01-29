<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

final class CacheDevelopmentPricing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-development-pricing';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Cache all pricing commands';

    public function handle(): void
    {
        collect([
            'explorer:cache-currencies-data',
            'explorer:cache-prices',
            'explorer:cache-currencies-history --no-delay',
        ])->map(fn (string $command) => Artisan::call($command));
    }
}
