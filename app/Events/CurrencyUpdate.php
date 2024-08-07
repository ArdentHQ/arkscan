<?php

declare(strict_types=1);

namespace App\Events;

final class CurrencyUpdate extends WebsocketEvent
{
    public const CHANNEL = 'currency-update';

    protected function uniqueTimeout(): int
    {
        return config('arkscan.webhooks.currency-update.ttl', 8);
    }
}
