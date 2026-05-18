<?php

declare(strict_types=1);

namespace App\Events;

use App\Events\Concerns\ShouldBeUniqueEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

abstract class WebsocketEvent implements ShouldBroadcast
{
    use Dispatchable;
    use ShouldBeUniqueEvent;
    use Queueable;

    public const CHANNEL = 'channel';

    public function __construct(string|null ...$ids)
    {
        $this->ids = empty($ids) ? [null] : array_values($ids);
        $this->onQueue('reverb');
    }

    /** @return array<string> */
    final public function broadcastOn(): array
    {
        // broadcastWhen() populates this after acquiring per-channel locks.
        // When it hasn't been called (e.g. Event::fake() in tests), fall back
        // to all channels so assertions against the event object still work.
        return $this->broadcastChannels
            ?? array_map(fn ($id) => $this->channelName($id), $this->ids);
    }

    final public function getId(): ?string
    {
        return $this->ids[0] ?? null;
    }

    final protected function channelName(?string $id = null): string
    {
        if ($id !== null) {
            return sprintf(
                '%s.%s',
                static::CHANNEL,
                $id
            );
        }

        return static::CHANNEL;
    }
}
