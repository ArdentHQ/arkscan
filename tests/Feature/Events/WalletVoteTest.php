<?php

declare(strict_types=1);

use App\Events\WalletVote;
use Illuminate\Support\Facades\Event;

it('should broadcast on wallet vote channel', function () {
    Event::fake();

    WalletVote::dispatch();

    Event::assertDispatched(WalletVote::class, function ($event) {
        return in_array('wallet-vote', $event->broadcastOn(), true);
    });
});

it('should broadcast on specific wallet vote channel', function () {
    Event::fake();

    WalletVote::dispatch('channel-id');

    Event::assertDispatched(WalletVote::class, function ($event) {
        return in_array('wallet-vote.channel-id', $event->broadcastOn(), true);
    });
});
