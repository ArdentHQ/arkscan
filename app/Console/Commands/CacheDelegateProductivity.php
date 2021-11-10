<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Facades\Rounds;
use App\Jobs\CacheProductivityByPublicKey;
use App\Models\Round;
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

    public function handle(): void
    {
        Rounds::allByRound(Monitor::roundNumber())
            ->each(fn (Round $round) => (bool) CacheProductivityByPublicKey::dispatch($round->public_key)->onQueue('productivity'));
    }
}
