<?php

declare(strict_types=1);

use App\Events\NewBlock;
use Illuminate\Support\Facades\Event;

it('should broadcast on blocks channel', function () {
    Event::fake();

    NewBlock::dispatch();

    Event::assertDispatched(NewBlock::class, function ($event) {
        return $event->broadcastOn()->name === 'blocks';
    });
});

it('should broadcast on specific blocks channel', function () {
    Event::fake();

    NewBlock::dispatch('channel-id');

    Event::assertDispatched(NewBlock::class, function ($event) {
        return $event->broadcastOn()->name === 'blocks.channel-id';
    });
});
