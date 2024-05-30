<?php

declare(strict_types=1);

namespace App\Events;

use App\Events\Concerns\ShouldBeUniqueEvent;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

abstract class WebsocketEvent implements ShouldBroadcast
{
    use Dispatchable;
    use ShouldBeUniqueEvent;

    public const CHANNEL = 'channel';

    public function __construct(protected ?string $id = null)
    {
        //
    }

    final public function broadcastOn()
    {
        return new Channel($this->channelName());
    }

    final public function getId(): ?string
    {
        return $this->id;
    }

    private function channelName(): string
    {
        if ($this->id !== null) {
            return sprintf(
                '%s.%s',
                static::CHANNEL,
                $this->id
            );
        }

        return static::CHANNEL;
    }
}
