<?php

declare(strict_types=1);

namespace App\Http\Controllers\Concerns;

use App\Events\NewBlock;
use App\Jobs\CacheBlocks;

trait HandlesBlockWebhooks
{
    private function handleBlockApplied(): void
    {
        NewBlock::dispatch(null, request()->input('data.proposer'));

        // We'll run the job instead of duplicating the logic as this is the only purpose for the job.
        CacheBlocks::dispatch();
    }
}
