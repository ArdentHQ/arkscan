<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Facades\Rounds;
use App\Jobs\CacheLastBlockByPublicKey;
use App\Models\Block;
use App\Models\Round;
use App\Services\Cache\NetworkCache;
use App\Services\Monitor\Monitor;
use Illuminate\Console\Command;

final class CacheLastBlocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:last-blocks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache the last block for each active delegate in the current round.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        resolve(NetworkCache::class)->setHeight(Block::latestByHeight()->firstOrFail()->height->toNumber());

        /*
         * We are iterating over each round participant and dispatch a job to cache the last block.
         *
         * This might look like it will perform poorly but because of the size of the block table
         * it would be more expensive to execute a DISTINCT query instead of doing 51 queries that
         * look for an exact record which can make use of indices to greatly speed up the process.
         */

        Rounds::allByRound(Monitor::roundNumber())
            ->each(fn ($round) => CacheLastBlockByPublicKey::dispatch($round->public_key));
    }
}
