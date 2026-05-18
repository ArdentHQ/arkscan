<?php

declare(strict_types=1);

namespace App\Events\Concerns;

use Illuminate\Support\Facades\Cache;

trait ShouldBeUniqueEvent
{
    public const UNIQUE_KEY = 'webhooks:event';

    protected array $ids = [null];

    protected ?array $broadcastChannels = null;

    final public function broadcastWhen(): bool
    {
        $channels = [];

        foreach ($this->ids as $id) {
            $channelName = $this->channelName($id);
            $lock        = Cache::lock($this->uniqueKeyForChannel($channelName), $this->uniqueTimeout());

            if ($lock->acquire()) {
                $channels[] = $channelName;
            }
        }

        $this->broadcastChannels = $channels;

        return count($channels) > 0;
    }

    abstract protected function uniqueTimeout(): int;

    protected function uniqueKeyForChannel(string $channelName): string
    {
        return sprintf('%s:%s', static::UNIQUE_KEY, $channelName);
    }
}
