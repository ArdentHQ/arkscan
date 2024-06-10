<?php

declare(strict_types=1);

namespace App\Http\Controllers\Concerns;

use App\Events\NewBlock;

trait HandlesBlockWebhooks
{
    private function handleBlockApplied(): void
    {
        NewBlock::dispatch();
    }

    private function handleGeneratorBlockApplied(): void
    {
        NewBlock::dispatch(request()->input('data.generatorPublicKey'));
    }
}
