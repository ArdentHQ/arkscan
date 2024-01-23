<?php

namespace App\Http\Controllers;

use App\Events\NewBlock;
use Illuminate\Support\Facades\Log;

class WebhooksController
{
    public function __invoke(): void
    {
        if (! request()->hasValidSignature()) {
            abort(401);
        }

        Log::debug('POST webhook: '.request()->input('event'));

        NewBlock::dispatch();
    }
}
