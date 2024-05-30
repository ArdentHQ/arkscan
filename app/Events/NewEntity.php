<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class NewEntity implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithBroadcasting;
    use InteractsWithSockets;
    use SerializesModels;

    public const CHANNEL = 'channel';

    public function __construct(protected ?string $id = null)
    {
        //
    }

    final public function broadcastOn()
    {
        if ($this->id !== null) {
            return new Channel(sprintf(
                '%s.%s',
                static::CHANNEL,
                $this->id
            ));
        }

        return new Channel(static::CHANNEL);
    }

    final public function getId(): ?string
    {
        return $this->id;
    }
}
