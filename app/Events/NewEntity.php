<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

abstract class NewEntity implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    const CHANNEL = 'channel';

    public function broadcastOn()
    {
        return new Channel(static::CHANNEL);
    }
}
