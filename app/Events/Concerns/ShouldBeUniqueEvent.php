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

        // dump(
        //     // $lock,
        //     // $lock->get(),
        //     $this->uniqueKey(),
        //     now()->format('Y-m-d H:i:s'),
        //     // debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 8),
        // );

        if ($lock->get() === false) {
            return false;
        }

        return true;
    }

    abstract protected function uniqueTimeout(): int;

    private function uniqueKey(): string
    {
        return sprintf(
            '%s:%s',
            static::UNIQUE_KEY,
            $this->channelName()
        );
    }
}
