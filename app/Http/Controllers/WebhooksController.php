<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Events\NewBlock;
use Illuminate\Support\Facades\Cache;

final class WebhooksController
{
    public function __invoke(): void
    {
        // @phpstan-ignore-next-line
        if (! request()->hasValidSignature()) {
            abort(401);
        }

        $event = request()->input('event');
        if ($event === 'block.applied') {
            $this->handleBlockApplied();
        }
    }

    private function handleBlockApplied(): void
    {
        $lock = Cache::lock('foo', config('arkscan.webhooks.block-applied.ttl', 8));

        if ($lock->get() === false) {
            return;
        }

        NewBlock::dispatch();
    }
}
