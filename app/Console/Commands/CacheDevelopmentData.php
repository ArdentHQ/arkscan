<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Cache\DelegateCache;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

final class CacheDevelopmentData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executes all caching commands. Do not use this in production.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(DelegateCache $cache)
    {
        $commands = [
            'delegate-aggregates',
            'delegates',
            'exchange-rates',
            'chart-fee',
            'last-blocks',
            'musig',
            'statistics',
            'past-round-performance',
            'productivity',
            'real-time-statistics',
            'resignation-ids',
            'usernames',
            'voter-count',
            'votes',
        ];

        foreach ($commands as $command) {
            Artisan::call('cache:'.$command);
        }
    }
}
