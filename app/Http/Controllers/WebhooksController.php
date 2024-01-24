<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Events\NewBlock;
use App\Models\Block;
use Illuminate\Support\Facades\Log;

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
        NewBlock::dispatch();
    }
}
