<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Facades\Rounds;
use App\Jobs\CacheProductivityByPublicKey;
use App\Services\Monitor\Monitor;
use Illuminate\Console\Command;

final class CacheDelegateProductivity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-delegate-productivity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate and cache the productivity for each active delegate.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Rounds::allByRound(Monitor::roundNumber())
            ->each(fn ($round) => CacheProductivityByPublicKey::dispatch($round->public_key)->onQueue('productivity'));
    }
}
