<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Events\NewBlock;
use Illuminate\Support\Facades\Cache;

final class WebhooksController
{
    public function __invoke(): void
    {
        if (! request()->hasValidSignature()) {
            abort(401);
        }

        match (request()->input('event')) {
            'block.applied' => $this->handleBlockApplied(),
        };
    }

    private function handleBlockApplied()
    {
        $lock = Cache::lock('foo', config('arkscan.webhooks.block-applied.ttl', 8));

        if ($lock->get()) {
            NewBlock::dispatch();
        }
    }
}
