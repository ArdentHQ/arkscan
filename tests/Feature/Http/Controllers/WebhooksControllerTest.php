<?php

declare(strict_types=1);

use App\Events\NewBlock;
use App\Events\NewTransaction;
use App\Events\WalletVote;
use Illuminate\Support\Facades\Event;
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
        Event::fake();

        $secureUrl = URL::signedRoute('webhooks');

        $this->post($secureUrl, $this->block)
            ->assertOk();
        $this->post($secureUrl, $this->block)
            ->assertOk();
        $this->post($secureUrl, $this->block)
            ->assertOk();
        $this->post($secureUrl, $this->block)
            ->assertOk();

        $this->travel(8)->seconds();

        $this->post($secureUrl, $this->block)
            ->assertOk();

        Event::assertDispatchedTimes(NewBlock::class, 4);

        Event::assertDispatched(NewBlock::class, function ($event) {
            return $event->broadcastOn()->name === 'blocks';
        });

        Event::assertDispatched(NewBlock::class, function ($event) {
            return $event->broadcastOn()->name === 'blocks.public-key';
        });
    });
});

describe('transaction', function () {
    beforeEach(function () {
        $this->transaction = [
            'event' => 'transaction.applied',
            'data'  => [
                'recipient_id'      => 'address',
                'sender_public_key' => 'public-key',
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
        Event::fake();

        $secureUrl = URL::signedRoute('webhooks');

        $this->post($secureUrl, $this->transaction)
            ->assertOk();
        $this->post($secureUrl, $this->transaction)
            ->assertOk();
        $this->post($secureUrl, $this->transaction)
            ->assertOk();
        $this->post($secureUrl, $this->transaction)
            ->assertOk();

        $this->travel(8)->seconds();

        $this->post($secureUrl, $this->transaction)
            ->assertOk();

        Event::assertDispatchedTimes(NewTransaction::class, 6);

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
});

describe('wallet', function () {
    beforeEach(function () {
        $this->vote = [
            'event' => 'wallet.vote',
            'data'  => [
                'asset' => [
                    'votes' => [
                        '-98765',
                        '+12345',
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
                'asset' => [
                    'votes' => [
                        '+12345',
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
                'asset' => [
                    'votes' => [
                        '-98765',
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

    it('should not dispatch multiple times', function () {
        Event::fake();

        $secureUrl = URL::signedRoute('webhooks');

        $this->post($secureUrl, $this->vote)
            ->assertOk();
        $this->post($secureUrl, $this->vote)
            ->assertOk();
        $this->post($secureUrl, $this->vote)
            ->assertOk();
        $this->post($secureUrl, $this->vote)
            ->assertOk();

        $this->travel(8)->seconds();

        $this->post($secureUrl, $this->vote)
            ->assertOk();

        Event::assertDispatchedTimes(WalletVote::class, 4);

        Event::assertDispatched(WalletVote::class, function ($event) {
            return $event->broadcastOn()->name === 'wallet-vote.98765';
        });

        Event::assertDispatched(WalletVote::class, function ($event) {
            return $event->broadcastOn()->name === 'wallet-vote.12345';
        });
    });
});
