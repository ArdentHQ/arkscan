<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Jobs\CacheBlocks;
use App\Models\Block;
use App\Models\Transaction;
use App\Services\Addresses\Aggregates\LatestWalletAggregate;
use App\Services\Cache\StatisticsCache;
use App\Services\Cache\TransactionCache;
use App\Services\Timestamp;
use ARKEcosystem\Foundation\UserInterface\Support\DateFormat;
use Carbon\Carbon;
use Illuminate\Broadcasting\BroadcastEvent;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\URL;

it('should not dispatch any event if insecure url', function () {
    Queue::fake();

    $event = [
        'event' => 'block.applied',
        'data'  => [
            'generatorPublicKey' => 'public-key',
        ],
    ];

    $this
        ->post(route('webhooks'), $event)
        ->assertUnauthorized();

    Queue::assertPushed(BroadcastEvent::class, 0);
});

it('should not dispatch a random event on webhook', function () {
    Queue::fake();

    $secureUrl = URL::signedRoute('webhooks');

    $this
        ->post($secureUrl, ['event' => 'random.event'])
        ->assertOk();

    Queue::assertPushed(BroadcastEvent::class, 0);
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
        Queue::fake();

        $secureUrl = URL::signedRoute('webhooks');

        $this
            ->post($secureUrl, $this->block)
            ->assertOk();

        Queue::assertPushed(BroadcastEvent::class, 1);

        Queue::assertPushed(BroadcastEvent::class, function ($event) {
            if ($event->event->queue !== 'reverb') {
                return false;
            }

            if (! in_array('blocks', $event->event->broadcastOn())) {
                return false;
            }

            return in_array('blocks.public-key', $event->event->broadcastOn());
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

        Queue::assertPushed(BroadcastEvent::class, 2);

        Queue::assertPushed(BroadcastEvent::class, function ($event) {
            if (! in_array('blocks', $event->event->broadcastOn())) {
                return false;
            }

            return in_array('blocks.public-key', $event->event->broadcastOn());
        });
    });

    it('should dispatch statistics event if there is a change to block statistics', function () {
        Queue::fake();

        $secureUrl = URL::signedRoute('webhooks');

        $this
            ->post($secureUrl, $this->block)
            ->assertOk();

        Queue::assertPushed(BroadcastEvent::class, 1);
        Queue::assertPushed(CacheBlocks::class, 1);

        Queue::assertPushed(BroadcastEvent::class, function ($event) {
            if (! in_array('blocks', $event->event->broadcastOn())) {
                return false;
            }

            return in_array('blocks.public-key', $event->event->broadcastOn());
        });

        $block = Block::factory()->create([
            'total_amount' => 123 * 1e8,
        ]);

        Transaction::factory()->create([
            'block_id' => $block->id,
            'amount'   => 123 * 1e8,
            'fee'      => 0.123 * 1e8,
        ]);

        Queue::fake();

        $secureUrl = URL::signedRoute('webhooks');

        $this
            ->post($secureUrl, [
                'event' => 'block.applied',
                'data'  => [
                    'generatorPublicKey' => $block->generator_public_key,
                ],
            ])
            ->assertOk();

        Queue::assertPushed(BroadcastEvent::class, 1);
        Queue::assertPushed(CacheBlocks::class, 1);
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
        Queue::fake();

        $secureUrl = URL::signedRoute('webhooks');

        $this
            ->post($secureUrl, $this->transaction)
            ->assertOk();

        Queue::assertPushed(BroadcastEvent::class, 1);

        Queue::assertPushed(BroadcastEvent::class, function ($event) {
            if ($event->event->queue !== 'reverb') {
                return false;
            }

            if (! in_array('transactions', $event->event->broadcastOn())) {
                return false;
            }

            if (! in_array('transactions.public-key', $event->event->broadcastOn())) {
                return false;
            }

            return in_array('transactions.address', $event->event->broadcastOn());
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

        Queue::assertPushed(BroadcastEvent::class, 2);

        Queue::assertPushed(BroadcastEvent::class, function ($event) {
            if (! in_array('transactions', $event->event->broadcastOn())) {
                return false;
            }

            if (! in_array('transactions.public-key', $event->event->broadcastOn())) {
                return false;
            }

            return in_array('transactions.address', $event->event->broadcastOn());
        });
    });

    it('should dispatch statistics event if there is a new wallet', function () {
        Queue::fake();

        $this->travelTo('2024-04-19 00:15:44');

        $transaction = Transaction::factory()->transfer()->create([
            'timestamp' => Timestamp::fromUnix(Carbon::parse('2024-04-19 00:15:44')->unix())->unix(),
        ]);

        (new LatestWalletAggregate())->aggregate();

        $secureUrl = URL::signedRoute('webhooks');

        $cache = new StatisticsCache();

        expect($cache->getNewestAddress())->toEqual([
            'address'   => $transaction->sender->address,
            'timestamp' => $transaction->timestamp,
            'value'     => Carbon::createFromTimestamp((int) $transaction->timestamp + (int) Network::epoch()->timestamp)->format(DateFormat::DATE),
        ]);

        $this
            ->post($secureUrl, $this->transaction)
            ->assertOk();

        Queue::assertPushed(BroadcastEvent::class, 1);

        Queue::assertPushed(BroadcastEvent::class, function ($event) {
            if ($event->event->queue !== 'reverb') {
                return false;
            }

            if (! in_array('transactions', $event->event->broadcastOn())) {
                return false;
            }

            if (! in_array('transactions.public-key', $event->event->broadcastOn())) {
                return false;
            }

            return in_array('transactions.address', $event->event->broadcastOn());
        });

        $this->travelTo('2024-04-20 00:15:44');

        Queue::fake();

        $transaction = Transaction::factory()->create([
            'timestamp' => Timestamp::fromUnix(Carbon::parse('2024-04-20 00:15:44')->unix())->unix(),
        ]);

        $this
            ->post($secureUrl, [
                'event' => 'transaction.applied',
                'data'  => [
                    'recipientId'     => $transaction->recipient_id,
                    'senderPublicKey' => $transaction->sender_public_key,
                ],
            ])
            ->assertOk();

        Queue::assertPushed(BroadcastEvent::class, 1);

        Queue::assertPushed(BroadcastEvent::class, function ($event) use ($transaction) {
            if ($event->event->queue !== 'reverb') {
                return false;
            }

            if (! in_array('transactions', $event->event->broadcastOn())) {
                return false;
            }

            if (! in_array('transactions.'.$transaction->recipient_id, $event->event->broadcastOn())) {
                return false;
            }

            return in_array('transactions.'.$transaction->sender_public_key, $event->event->broadcastOn());
        });
    });

    it('should dispatch statistics event if there is a new largest transaction', function () {
        Queue::fake();

        $cache = new TransactionCache();

        $this->travelTo('2024-04-19 00:15:44');

        $transaction = Transaction::factory()->transfer()->create([
            'amount'    => 1 * 1e8,
            'fee'       => 0.1 * 1e8,
            'timestamp' => Timestamp::fromUnix(Carbon::parse('2024-04-19 00:15:44')->unix())->unix(),
        ]);

        $cache->setLargestIdByAmount($transaction->id);

        $secureUrl = URL::signedRoute('webhooks');

        expect($cache->getLargestIdByAmount())->toEqual($transaction->id);

        $this
            ->post($secureUrl, $this->transaction)
            ->assertOk();

        Queue::assertPushed(BroadcastEvent::class, 1);

        Queue::assertPushed(BroadcastEvent::class, function ($event) {
            if ($event->event->queue !== 'reverb') {
                return false;
            }

            if (! in_array('transactions', $event->event->broadcastOn())) {
                return false;
            }

            if (! in_array('transactions.public-key', $event->event->broadcastOn())) {
                return false;
            }

            return in_array('transactions.address', $event->event->broadcastOn());
        });

        $this->travelTo('2024-04-20 00:15:44');

        Queue::fake();

        $transaction = Transaction::factory()->transfer()->create([
            'amount'    => 20 * 1e8,
            'fee'       => 0.2 * 1e8,
            'timestamp' => Timestamp::fromUnix(Carbon::parse('2024-04-20 00:15:44')->unix())->unix(),
        ]);

        $this
            ->post($secureUrl, [
                'event' => 'transaction.applied',
                'data'  => [
                    'recipientId'     => $transaction->recipient_id,
                    'senderPublicKey' => $transaction->sender_public_key,
                ],
            ])
            ->assertOk();

        Queue::assertPushed(BroadcastEvent::class, 1);

        Queue::assertPushed(BroadcastEvent::class, function ($event) use ($transaction) {
            if ($event->event->queue !== 'reverb') {
                return false;
            }

            if (! in_array('transactions', $event->event->broadcastOn())) {
                return false;
            }

            if (! in_array('transactions.'.$transaction->recipient_id, $event->event->broadcastOn())) {
                return false;
            }

            return in_array('transactions.'.$transaction->sender_public_key, $event->event->broadcastOn());
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
        Queue::fake();

        $secureUrl = URL::signedRoute('webhooks');

        $this
            ->post($secureUrl, $this->vote)
            ->assertOk();

        Queue::assertPushed(BroadcastEvent::class, 2);

        Queue::assertPushed(BroadcastEvent::class, function ($event) {
            if ($event->event->queue !== 'reverb') {
                return false;
            }

            if (! in_array('wallet-vote.98765', $event->event->broadcastOn())) {
                return false;
            }

            return in_array('wallet-vote.12345', $event->event->broadcastOn());
        });
    });

    it('should handle only a vote', function () {
        Queue::fake();

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

        expect(Queue::pushed(BroadcastEvent::class)->count())->toEqual(1);

        Queue::assertPushed(BroadcastEvent::class, function ($event) {
            if ($event->event->queue !== 'reverb') {
                return false;
            }

            return in_array('wallet-vote.12345', $event->event->broadcastOn());
        });
    });

    it('should handle only an unvote', function () {
        Queue::fake();

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

        expect(Queue::pushed(BroadcastEvent::class)->count())->toEqual(1);

        Queue::assertPushed(BroadcastEvent::class, function ($event) {
            if ($event->event->queue !== 'reverb') {
                return false;
            }

            return in_array('wallet-vote.98765', $event->event->broadcastOn());
        });
    });

    it('should not dispatch multiple times', function () {
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
            if ($event->event->queue !== 'reverb') {
                return false;
            }

            if (! in_array('wallet-vote.98765', $event->event->broadcastOn())) {
                return false;
            }

            return in_array('wallet-vote.12345', $event->event->broadcastOn());
        });
    });
});
