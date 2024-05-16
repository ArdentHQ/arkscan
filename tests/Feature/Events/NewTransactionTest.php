<?php

declare(strict_types=1);

use App\Events\NewTransaction;
use Illuminate\Support\Facades\Event;

it('should broadcast on transactions channel', function () {
    Event::fake();

    NewTransaction::dispatch();

    Event::assertDispatched(NewTransaction::class, function ($event) {
        return $event->broadcastOn()->name === 'transactions';
    });
});

it('should broadcast on specific transactions channel', function () {
    Event::fake();

    NewTransaction::dispatch('channel-id');

    Event::assertDispatched(NewTransaction::class, function ($event) {
        return $event->broadcastOn()->name === 'transactions.channel-id';
    });
});
