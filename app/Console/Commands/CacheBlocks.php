<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\CacheBlocks as Job;
use App\Services\Cache\BlockCache;
use Illuminate\Console\Command;

final class CacheBlocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-blocks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache expensive block aggregates.';

    public function handle(): void
    {
        (new Job())->handle(new BlockCache());
    }
}
