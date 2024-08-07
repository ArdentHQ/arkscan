<?php

declare(strict_types=1);

use App\Events\WalletVote;
use Illuminate\Support\Facades\Event;

it('should broadcast on wallet vote channel', function () {
    Event::fake();

    WalletVote::dispatch();

    Event::assertDispatched(WalletVote::class, function ($event) {
        return $event->broadcastOn()->name === 'wallet-vote';
    });
});

it('should broadcast on specific wallet vote channel', function () {
    Event::fake();

    WalletVote::dispatch('channel-id');

    Event::assertDispatched(WalletVote::class, function ($event) {
        return $event->broadcastOn()->name === 'wallet-vote.channel-id';
    });
});
