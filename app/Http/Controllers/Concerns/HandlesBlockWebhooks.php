<?php

declare(strict_types=1);

namespace App\Http\Controllers\Concerns;

use App\Events\NewBlock;
use Illuminate\Support\Facades\Cache;

trait HandlesBlockWebhooks
{
    private function handleBlockApplied(): void
    {
        $lock = Cache::lock('webhooks:block:applied', config('arkscan.webhooks.block-applied.ttl', 8));
        if ($lock->get() === false) {
            return;
        }

        NewBlock::dispatch();
    }

    private function handleGeneratorBlockApplied(): void
    {
        $generatorPublicKey = request()->input('data.generatorPublicKey');
        $lock = Cache::lock('webhooks:block:applied:'.$generatorPublicKey, config('arkscan.webhooks.block-applied.ttl', 8));
        if ($lock->get() === false) {
            return;
        }

        NewBlock::dispatch($generatorPublicKey);
    }
}
