<?php

declare(strict_types=1);

use App\Events\NewBlock;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;

it('should dispatch event on webhook', function () {
    Event::fake();

    $secureUrl = URL::signedRoute('webhooks');

    $this
        ->post($secureUrl, ['event' => 'block.applied'])
        ->assertOk();

    Event::assertDispatched(NewBlock::class, function ($event) {
        return $event->broadcastOn()->name === 'blocks';
    });
});

it('should not dispatch an event on webhook', function () {
    Event::fake();

    $secureUrl = URL::signedRoute('webhooks');

    $this
        ->post($secureUrl, ['event' => 'random.event'])
        ->assertOk();

    Event::assertDispatchedTimes(NewBlock::class, 0);
});

it('should not dispatch an event if insecure url', function () {
    Event::fake();

    $this
        ->post(route('webhooks'), ['event' => 'block.applied'])
        ->assertUnauthorized();

    Event::assertDispatchedTimes(NewBlock::class, 0);
});
