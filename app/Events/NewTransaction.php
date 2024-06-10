<?php

declare(strict_types=1);

namespace App\Events;

final class NewTransaction extends WebsocketEvent
{
    public const CHANNEL = 'transactions';

    protected function uniqueTimeout(): int
    {
        return config('arkscan.webhooks.transaction-applied.ttl', 8);
    }
}
