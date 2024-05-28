<?php

declare(strict_types=1);

namespace App\Events;

final class NewBlock extends WebsocketEvent
{
    public const CHANNEL = 'blocks';

    protected function uniqueTimeout(): int
    {
        return config('arkscan.webhooks.block-applied.ttl', 8);
    }
}
