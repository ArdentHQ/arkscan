<?php

declare(strict_types=1);

use App\Events\NewBlock;
use App\Events\NewTransaction;
use App\Events\WalletVote;
use Carbon\Carbon;
use Illuminate\Broadcasting\BroadcastEvent;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\URL;

it('should not dispatch any event if insecure url', function () {
    Event::fake();

    $event = [
        'event' => 'block.applied',
        'data'  => [
            'generatorPublicKey' => 'public-key',
        ],
    ];

    $this
        ->post(route('webhooks'), $event)
        ->assertUnauthorized();

    Event::assertDispatchedTimes(NewBlock::class, 0);
    Event::assertDispatchedTimes(NewTransaction::class, 0);
    Event::assertDispatchedTimes(WalletVote::class, 0);
});

it('should not dispatch a random event on webhook', function () {
    Event::fake();

    $secureUrl = URL::signedRoute('webhooks');

    $this
        ->post($secureUrl, ['event' => 'random.event'])
        ->assertOk();

    Event::assertDispatchedTimes(NewBlock::class, 0);
});

describe('block', function () {
    beforeEach(function () {
        $this->block = [
            'event' => 'block.applied',
            'data'  => [
                'generatorPublicKey' => 'public-key',
            ],
        ];
    });

    it('should dispatch an event on webhook', function () {
        Event::fake();

        $secureUrl = URL::signedRoute('webhooks');

        $this
            ->post($secureUrl, $this->block)
            ->assertOk();

        Event::assertDispatchedTimes(NewBlock::class, 2);

        Event::assertDispatched(NewBlock::class, function ($event) {
            return $event->broadcastOn()->name === 'blocks';
        });

        Event::assertDispatched(NewBlock::class, function ($event) {
            return $event->broadcastOn()->name === 'blocks.public-key';
        });
    });

    it('should not dispatch multiple times', function () {
        Queue::fake();

        Config::set('arkscan.webhooks.block-applied.ttl', 4);

        $secureUrl = URL::signedRoute('webhooks');

        $this->post($secureUrl, $this->block)
            ->assertOk();
        $this->post($secureUrl, $this->block)
            ->assertOk();
        $this->post($secureUrl, $this->block)
            ->assertOk();
        $this->post($secureUrl, $this->block)
            ->assertOk();

        $this->travel(4)->seconds();

        $this->post($secureUrl, $this->block)
            ->assertOk();

        Queue::assertPushed(BroadcastEvent::class, 4);

        Queue::assertPushed(BroadcastEvent::class, function ($event) {
            return $event->event->broadcastOn()->name === 'blocks';
        });

        Queue::assertPushed(BroadcastEvent::class, function ($event) {
            return $event->event->broadcastOn()->name === 'blocks.public-key';
        });
    });
});

describe('transaction', function () {
    beforeEach(function () {
        $this->transaction = [
            'event' => 'transaction.applied',
            'data'  => [
                'recipientId'     => 'address',
                'senderPublicKey' => 'public-key',
            ],
        ];
    });

    it('should dispatch an event on webhook', function () {
        Event::fake();

        $secureUrl = URL::signedRoute('webhooks');

        $this
            ->post($secureUrl, $this->transaction)
            ->assertOk();

        Event::assertDispatchedTimes(NewTransaction::class, 3);

        Event::assertDispatched(NewTransaction::class, function ($event) {
            return $event->broadcastOn()->name === 'transactions';
        });

        Event::assertDispatched(NewTransaction::class, function ($event) {
            return $event->broadcastOn()->name === 'transactions.public-key';
        });

        Event::assertDispatched(NewTransaction::class, function ($event) {
            return $event->broadcastOn()->name === 'transactions.address';
        });
    });

    it('should not dispatch multiple times', function () {
        Queue::fake();

        Config::set('arkscan.webhooks.transaction-applied.ttl', 4);

        $secureUrl = URL::signedRoute('webhooks');

        $this->post($secureUrl, $this->transaction)
            ->assertOk();
        $this->post($secureUrl, $this->transaction)
            ->assertOk();
        $this->post($secureUrl, $this->transaction)
            ->assertOk();
        $this->post($secureUrl, $this->transaction)
            ->assertOk();

        $this->travel(4)->seconds();

        $this->post($secureUrl, $this->transaction)
            ->assertOk();

        Queue::assertPushed(BroadcastEvent::class, 6);

        Queue::assertPushed(BroadcastEvent::class, function ($event) {
            return $event->event->broadcastOn()->name === 'transactions';
        });

        Queue::assertPushed(BroadcastEvent::class, function ($event) {
            return $event->event->broadcastOn()->name === 'transactions.public-key';
        });

        Queue::assertPushed(BroadcastEvent::class, function ($event) {
            return $event->event->broadcastOn()->name === 'transactions.address';
        });
    });
});

describe('wallet', function () {
    beforeEach(function () {
        $this->vote = [
            'event' => 'wallet.vote',
            'data'  => [
                'transaction' => [
                    'asset' => [
                        'votes' => [
                            '-98765',
                            '+12345',
                        ],
                    ],
                ],
            ],
        ];
    });

    it('should dispatch an event on webhook', function () {
        Event::fake();

        $secureUrl = URL::signedRoute('webhooks');

        $this
            ->post($secureUrl, $this->vote)
            ->assertOk();

        Event::assertDispatchedTimes(WalletVote::class, 2);

        Event::assertDispatched(WalletVote::class, function ($event) {
            return $event->broadcastOn()->name === 'wallet-vote.98765';
        });

        Event::assertDispatched(WalletVote::class, function ($event) {
            return $event->broadcastOn()->name === 'wallet-vote.12345';
        });
    });

    it('should handle only a vote', function () {
        Event::fake();

        $this->vote = [
            'event' => 'wallet.vote',
            'data'  => [
                'transaction' => [
                    'asset' => [
                        'votes' => [
                            '+12345',
                        ],
                    ],
                ],
            ],
        ];

        $secureUrl = URL::signedRoute('webhooks');

        $this
            ->post($secureUrl, $this->vote)
            ->assertOk();

        Event::assertDispatchedTimes(WalletVote::class, 1);

        Event::assertDispatched(WalletVote::class, function ($event) {
            return $event->broadcastOn()->name === 'wallet-vote.12345';
        });
    });

    it('should handle only an unvote', function () {
        Event::fake();

        $this->vote = [
            'event' => 'wallet.vote',
            'data'  => [
                'transaction' => [
                    'asset' => [
                        'votes' => [
                            '-98765',
                        ],
                    ],
                ],
            ],
        ];

        $secureUrl = URL::signedRoute('webhooks');

        $this
            ->post($secureUrl, $this->vote)
            ->assertOk();

        Event::assertDispatchedTimes(WalletVote::class, 1);

        Event::assertDispatched(WalletVote::class, function ($event) {
            return $event->broadcastOn()->name === 'wallet-vote.98765';
        });
    });

    it('wshould not dispatch multiple times', function () {
        $this->freezeTime();

        $this->travelTo(Carbon::parse('2024-04-14 12:25:04'));

        Queue::fake();

        Config::set('arkscan.webhooks.wallet-vote.ttl', 4);

        $secureUrl = URL::signedRoute('webhooks');

        $this->post($secureUrl, $this->vote)
            ->assertOk();
        $this->post($secureUrl, $this->vote)
            ->assertOk();
        $this->post($secureUrl, $this->vote)
            ->assertOk();
        $this->post($secureUrl, $this->vote)
            ->assertOk();

        $this->travel(4)->seconds();

        $this->post($secureUrl, $this->vote)
            ->assertOk();

        Queue::assertPushed(BroadcastEvent::class, 4);

        Queue::assertPushed(BroadcastEvent::class, function ($event) {
            return $event->event->broadcastOn()->name === 'wallet-vote.98765';
        });

        Queue::assertPushed(BroadcastEvent::class, function ($event) {
            return $event->event->broadcastOn()->name === 'wallet-vote.12345';
        });
    });
});
