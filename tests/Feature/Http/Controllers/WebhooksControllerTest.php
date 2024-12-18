<?php

declare(strict_types=1);

use App\Events\NewBlock;
use App\Events\NewTransaction;
use App\Events\Statistics\TransactionDetails;
use App\Events\Statistics\UniqueAddresses;
use App\Events\WalletVote;
use App\Jobs\CacheBlocks;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Addresses\Aggregates\LatestWalletAggregate;
use App\Services\Cache\StatisticsCache;
use App\Services\Cache\TransactionCache;
use ARKEcosystem\Foundation\UserInterface\Support\DateFormat;
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

    it('should dispatch statistics event if there is a change to block statistics', function () {
        Event::fake();
        Queue::fake();

        $secureUrl = URL::signedRoute('webhooks');

        $this
            ->post($secureUrl, $this->block)
            ->assertOk();

        Event::assertDispatchedTimes(NewBlock::class, 2);
        Queue::assertPushed(CacheBlocks::class, 1);

        Event::assertDispatched(NewBlock::class, function ($event) {
            return $event->broadcastOn()->name === 'blocks';
        });

        Event::assertDispatched(NewBlock::class, function ($event) {
            return $event->broadcastOn()->name === 'blocks.public-key';
        });

        $block = Block::factory()->create([
            'total_amount' => 123 * 1e8,
        ]);

        Transaction::factory()->create([
            'block_id'       => $block->id,
            'amount'         => 123 * 1e8,
            'gas_price'      => 5,
        ]);

        $secureUrl = URL::signedRoute('webhooks');

        $this
            ->post($secureUrl, $this->block)
            ->assertOk();

        Queue::assertPushed(CacheBlocks::class, 2);
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

    it('should dispatch statistics event if there is a new wallet', function () {
        Event::fake();

        $this->travelTo('2024-04-19 00:15:44');

        $walletA = Wallet::factory()->create();
        $timestamp = Carbon::parse('2024-04-19 00:15:44')->getTimestampMs();
        $transaction = Transaction::factory()->transfer()->create([
            'sender_public_key' => $walletA->public_key,
            'timestamp' => $timestamp,
        ]);

        $walletA->fill(['updated_at' => $timestamp])->save();

        (new LatestWalletAggregate())->aggregate();

        $secureUrl = URL::signedRoute('webhooks');

        $cache = new StatisticsCache();

        expect($cache->getNewestAddress())->toEqual([
            'address'   => $walletA->address,
            'timestamp' => $timestamp,
            'value'     => Carbon::createFromTimestamp($transaction->timestamp)->format(DateFormat::DATE),
        ]);

        $this
            ->post($secureUrl, $this->transaction)
            ->assertOk();

        Event::assertDispatchedTimes(NewTransaction::class, 3);
        Event::assertDispatchedTimes(UniqueAddresses::class, 0);

        Event::assertDispatched(NewTransaction::class, function ($event) {
            return $event->broadcastOn()->name === 'transactions';
        });

        Event::assertDispatched(NewTransaction::class, function ($event) {
            return $event->broadcastOn()->name === 'transactions.public-key';
        });

        Event::assertDispatched(NewTransaction::class, function ($event) {
            return $event->broadcastOn()->name === 'transactions.address';
        });

        $walletB = Wallet::factory()->create();
        $timestamp = Carbon::parse('2024-04-19 01:16:55')->getTimestampMs();
        $transaction = Transaction::factory()->create([
            'sender_public_key' => $walletB->public_key,
            'timestamp' => $timestamp,
        ]);

        $walletB->fill(['updated_at' => $timestamp])->save();

        $this
            ->post($secureUrl, $this->transaction)
            ->assertOk();

        Event::assertDispatchedTimes(UniqueAddresses::class, 1);
    });

    it('should dispatch statistics event if there is a new largest transaction', function () {
        Event::fake();

        $cache = new TransactionCache();

        $this->travelTo('2024-04-19 00:15:44');

        $transaction = Transaction::factory()->transfer()->create([
            'amount'          => 1 * 1e8,
            'gas_price'       => 5,
            'timestamp'       => Carbon::parse('2024-04-19 00:15:44')->getTimestampMs(),
        ]);

        $cache->setLargestIdByAmount($transaction->id);

        $secureUrl = URL::signedRoute('webhooks');

        expect($cache->getLargestIdByAmount())->toEqual($transaction->id);

        $this
            ->post($secureUrl, $this->transaction)
            ->assertOk();

        Event::assertDispatchedTimes(NewTransaction::class, 3);
        Event::assertDispatchedTimes(TransactionDetails::class, 0);

        Event::assertDispatched(NewTransaction::class, function ($event) {
            return $event->broadcastOn()->name === 'transactions';
        });

        Event::assertDispatched(NewTransaction::class, function ($event) {
            return $event->broadcastOn()->name === 'transactions.public-key';
        });

        Event::assertDispatched(NewTransaction::class, function ($event) {
            return $event->broadcastOn()->name === 'transactions.address';
        });

        $transaction = Transaction::factory()->transfer()->create([
            'amount'    => 20 * 1e8,
            'gas_price' => 6,
            'timestamp' => Carbon::parse('2024-04-20 00:15:44')->getTimestampMs(),
        ]);

        $this
            ->post($secureUrl, $this->transaction)
            ->assertOk();

        Event::assertDispatchedTimes(TransactionDetails::class, 1);
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
            return $event->event->broadcastOn()->name === 'wallet-vote.98765';
        });

        Queue::assertPushed(BroadcastEvent::class, function ($event) {
            return $event->event->broadcastOn()->name === 'wallet-vote.12345';
        });
    });
});
