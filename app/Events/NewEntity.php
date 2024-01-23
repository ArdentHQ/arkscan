<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBeUnique;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class NewEntity implements ShouldBroadcast, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public const CHANNEL = 'channel';

    final public function broadcastOn()
    {
        return new Channel(static::CHANNEL);
    }

    final public function uniqueId(): string
    {
        return static::class.':'.static::CHANNEL;
    }
}
