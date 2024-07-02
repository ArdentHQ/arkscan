<?php

declare(strict_types=1);

namespace App\Events\Concerns;

use Illuminate\Support\Facades\Cache;

trait ShouldBeUniqueEvent
{
    public const UNIQUE_KEY = 'webhooks:event';

    final public function broadcastWhen(): bool
    {
        $lock = Cache::lock(
            $this->uniqueKey(),
            $this->uniqueTimeout()
        );

        if ($lock->get() === false) {
            return false;
        }

        return true;
    }

    abstract protected function uniqueTimeout(): int;

    protected function uniqueKey(): string
    {
        return sprintf(
            '%s:%s',
            static::UNIQUE_KEY,
            $this->channelName()
        );
    }
}
