<?php

declare(strict_types=1);

namespace App\Events\Statistics;

use App\Events\WebsocketEvent;

abstract class StatisticsUpdate extends WebsocketEvent
{
    public const CHANNEL = 'statistics-update';

    final protected function uniqueTimeout(): int
    {
        return config('arkscan.webhooks.statistics-update.ttl', 8);
    }

    final protected function uniqueKey(): string
    {
        return sprintf(
            '%s:%s:%s',
            static::UNIQUE_KEY,
            $this->channelName(),
            $this::class,
        );
    }
}
