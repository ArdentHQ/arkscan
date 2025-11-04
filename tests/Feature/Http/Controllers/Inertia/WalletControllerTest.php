<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use Carbon\Carbon;
use Inertia\Testing\AssertableInertia as Assert;
use function Tests\faker;

beforeEach(function () {
    $this->withoutExceptionHandling();

    $this->subject = Wallet::factory()->create();
});

function performWalletRequest($context, $withReload = true, $pageCallback = null, $reloadCallback = null, ?Wallet $wallet = null, array $queryString = []): mixed
{
    if ($wallet === null) {
        $wallet = Wallet::factory()->create();
    }

    return $context->get(route('wallet', ['wallet' => $wallet, ...$queryString ?? []]))
        ->assertOk()
        ->assertInertia(function (Assert $page) use ($pageCallback, $wallet, $withReload, $reloadCallback) {
            $page->where('wallet.address', $wallet->address)
                ->where('filters', [
                    'transactions' => [
                        'outgoing'            => true,
                        'incoming'            => true,
                        'transfers'           => true,
                        'multipayments'       => true,
                        'votes'               => true,
                        'validator'           => true,
                        'username'            => true,
                        'contract_deployment' => true,
                        'others'              => true,
                    ],
                ])
                ->missing('transactions')
                ->missing('blocks')
                ->component('Wallet/Wallet');

            if (is_callable($pageCallback)) {
                $pageCallback($page);
            }

            if (! $withReload) {
                return;
            }

            $page->reloadOnly('wallet,transactions,blocks', function (Assert $reload) use ($reloadCallback) {
                if (is_callable($reloadCallback)) {
                    $reloadCallback($reload);
                }
            });
        });
}

it('should render the page without any errors', function () {
    performWalletRequest($this);
});

it('should have transactions', function () {
    $altWallet = Wallet::factory()->create();

    $sent = Transaction::factory()
        ->transfer()
        ->create([
            'sender_public_key' => $this->subject->public_key,
            'to'                => $altWallet->address,
        ])
        ->fresh();

    $received = Transaction::factory()
        ->transfer()
        ->create([
            'sender_public_key' => $altWallet->public_key,
            'from'              => $altWallet->address,
            'to'                => $this->subject->address,
        ])
        ->fresh();

    performWalletRequest(
        $this,
        wallet: $this->subject,
        reloadCallback: function (Assert $reload) use ($sent, $received) {
            $reload->has('transactions.data', 2)
                ->where('transactions.total', 2)
                ->where('transactions.current_page', 1)
                ->where('transactions.last_page', 1)
                ->where('transactions.meta', [
                    'pageName'  => 'page',
                    'urlParams' => [],
                ])
                ->where('transactions.data', function ($transactions) use ($sent, $received) {
                    $transactionIds = collect($transactions)->pluck('hash');

                    return $transactionIds->contains($sent->hash) && $transactionIds->contains($received->hash);
                });
        },
    );
});

it('should have blocks', function () {
    $block1 = Block::factory()
        ->create([
            'proposer' => $this->subject->address,
        ])
        ->fresh();

    $block2 = Block::factory()
        ->create([
            'proposer' => $this->subject->address,
        ])
        ->fresh();

    performWalletRequest(
        $this,
        wallet: $this->subject,
        reloadCallback: function (Assert $reload) use ($block1, $block2) {
            $reload->has('blocks.data', 2)
                ->where('blocks.total', 2)
                ->where('blocks.current_page', 1)
                ->where('blocks.last_page', 1)
                ->where('blocks.meta', [
                    'pageName'  => 'page',
                    'urlParams' => [],
                ])
                ->where('blocks.data', function ($blocks) use ($block1, $block2) {
                    $transactionIds = collect($blocks)->pluck('hash');

                    return $transactionIds->contains($block1->hash) && $transactionIds->contains($block2->hash);
                });
        },
    );
});

it('should filter by outgoing transactions', function () {
    $altWallet = Wallet::factory()->create();

    $sent = Transaction::factory()
        ->transfer()
        ->create([
            'sender_public_key' => $this->subject->public_key,
            'to'                => $altWallet->address,
        ])
        ->fresh();

    $received = Transaction::factory()
        ->transfer()
        ->create([
            'sender_public_key' => $altWallet->public_key,
            'from'              => $altWallet->address,
            'to'                => $this->subject->address,
        ])
        ->fresh();

    performWalletRequest(
        $this,
        wallet: $this->subject,
        queryString: [
            'outgoing'            => 'true',
            'incoming'            => 'false',
            'transfers'           => 'true',
            'multipayments'       => 'false',
            'votes'               => 'false',
            'validator'           => 'false',
            'username'            => 'false',
            'contract_deployment' => 'false',
            'others'              => 'false',
        ],
        reloadCallback: function (Assert $reload) use ($sent, $received) {
            $reload->has('transactions.data', 1)
                ->where('transactions.total', 1)
                ->where('transactions.data', function ($transactions) use ($sent, $received) {
                    $transactionIds = collect($transactions)->pluck('hash');

                    return $transactionIds->contains($sent->hash) && ! $transactionIds->contains($received->hash);
                });
        },
    );
});

it('should filter by incoming transactions', function () {
    $altWallet = Wallet::factory()->create();

    $sent = Transaction::factory()
        ->transfer()
        ->create([
            'sender_public_key' => $this->subject->public_key,
            'to'                => $altWallet->address,
        ])
        ->fresh();

    $received = Transaction::factory()
        ->transfer()
        ->create([
            'sender_public_key' => $altWallet->public_key,
            'from'              => $altWallet->address,
            'to'                => $this->subject->address,
        ])
        ->fresh();

    performWalletRequest(
        $this,
        wallet: $this->subject,
        queryString: [
            'outgoing'            => 'false',
            'incoming'            => 'true',
            'transfers'           => 'true',
            'multipayments'       => 'false',
            'votes'               => 'false',
            'validator'           => 'false',
            'username'            => 'false',
            'contract_deployment' => 'false',
            'others'              => 'false',
        ],
        reloadCallback: function (Assert $reload) use ($sent, $received) {
            $reload->has('transactions.data', 1)
                ->where('transactions.total', 1)
                ->where('transactions.data', function ($transactions) use ($sent, $received) {
                    $transactionIds = collect($transactions)->pluck('hash');

                    return ! $transactionIds->contains($sent->hash) && $transactionIds->contains($received->hash);
                });
        },
    );
});

it('should filter by incoming and outgoing transactions', function () {
    $altWallet = Wallet::factory()->create();

    $sent = Transaction::factory()
        ->transfer()
        ->create([
            'sender_public_key' => $this->subject->public_key,
            'to'                => $altWallet->address,
        ])
        ->fresh();

    $received = Transaction::factory()
        ->transfer()
        ->create([
            'sender_public_key' => $altWallet->public_key,
            'from'              => $altWallet->address,
            'to'                => $this->subject->address,
        ])
        ->fresh();

    performWalletRequest(
        $this,
        wallet: $this->subject,
        queryString: [
            'outgoing'            => 'true',
            'incoming'            => 'true',
            'transfers'           => 'true',
            'multipayments'       => 'false',
            'votes'               => 'false',
            'validator'           => 'false',
            'username'            => 'false',
            'contract_deployment' => 'false',
            'others'              => 'false',
        ],
        reloadCallback: function (Assert $reload) use ($sent, $received) {
            $reload->has('transactions.data', 2)
                ->where('transactions.total', 2)
                ->where('transactions.data', function ($transactions) use ($sent, $received) {
                    $transactionIds = collect($transactions)->pluck('hash');

                    return $transactionIds->contains($sent->hash) && $transactionIds->contains($received->hash);
                });
        },
    );
});

it('should filter by multipayment transactions', function () {
    $transfer = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $multiPayment = Transaction::factory()
        ->multiPayment([faker()->wallet['address']], [BigNumber::new(1 * 1e18)])
        ->create([
            'sender_public_key' => $this->subject->public_key,
        ]);

    performWalletRequest(
        $this,
        wallet: $this->subject,
        queryString: [
            'outgoing'            => 'true',
            'incoming'            => 'false',
            'transfers'           => 'false',
            'multipayments'       => 'true',
            'votes'               => 'false',
            'validator'           => 'false',
            'username'            => 'false',
            'contract_deployment' => 'false',
            'others'              => 'false',
        ],
        reloadCallback: function (Assert $reload) use ($transfer, $multiPayment) {
            $reload->has('transactions.data', 1)
                ->where('transactions.total', 1)
                ->where('transactions.data', function ($transactions) use ($transfer, $multiPayment) {
                    $transactionIds = collect($transactions)->pluck('hash');

                    return ! $transactionIds->contains($transfer->hash) && $transactionIds->contains($multiPayment->hash);
                });
        },
    );
});

it('should filter by vote transactions', function () {
    $vote = Transaction::factory()->vote($this->subject->address)->create([
        'sender_public_key' => $this->subject->public_key,
        'timestamp'         => Carbon::parse('2024-11-03 11:33:44')->getTimestampMs(), // oldest transaction
    ]);

    $transfer = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
        'timestamp'         => Carbon::parse('2024-11-04 11:33:44')->getTimestampMs(),
    ]);

    $unvote = Transaction::factory()->unvote()->create([
        'sender_public_key' => $this->subject->public_key,
        'timestamp'         => Carbon::parse('2024-11-03 11:33:44')->getTimestampMs(), // oldest transaction
    ]);

    performWalletRequest(
        $this,
        wallet: $this->subject,
        queryString: [
            'outgoing'            => 'true',
            'incoming'            => 'false',
            'transfers'           => 'false',
            'multipayments'       => 'false',
            'votes'               => 'true',
            'validator'           => 'false',
            'username'            => 'false',
            'contract_deployment' => 'false',
            'others'              => 'false',
        ],
        reloadCallback: function (Assert $reload) use ($transfer, $vote, $unvote) {
            $reload->has('transactions.data', 2)
                ->where('transactions.total', 2)
                ->where('transactions.data', function ($transactions) use ($transfer, $vote, $unvote) {
                    $transactionIds = collect($transactions)->pluck('hash');

                    if ($transactionIds->contains($transfer->hash)) {
                        return false;
                    }

                    return $transactionIds->contains($vote->hash) && $transactionIds->contains($unvote->hash);
                });
        },
    );
});

it('should filter by validator transactions', function () {
    $registration = Transaction::factory()->validatorRegistration()->create([
        'sender_public_key' => $this->subject->public_key,
        'timestamp'         => Carbon::parse('2024-11-03 11:33:44')->getTimestampMs(), // oldest transaction
    ]);

    $transfer = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
        'timestamp'         => Carbon::parse('2024-11-04 11:33:44')->getTimestampMs(),
    ]);

    $resignation = Transaction::factory()->validatorResignation()->create([
        'sender_public_key' => $this->subject->public_key,
        'timestamp'         => Carbon::parse('2024-11-03 11:33:44')->getTimestampMs(), // oldest transaction
    ]);

    performWalletRequest(
        $this,
        wallet: $this->subject,
        queryString: [
            'outgoing'            => 'true',
            'incoming'            => 'false',
            'transfers'           => 'false',
            'multipayments'       => 'false',
            'votes'               => 'false',
            'validator'           => 'true',
            'username'            => 'false',
            'contract_deployment' => 'false',
            'others'              => 'false',
        ],
        reloadCallback: function (Assert $reload) use ($transfer, $registration, $resignation) {
            $reload->has('transactions.data', 2)
                ->where('transactions.total', 2)
                ->where('transactions.data', function ($transactions) use ($transfer, $registration, $resignation) {
                    $transactionIds = collect($transactions)->pluck('hash');

                    if ($transactionIds->contains($transfer->hash)) {
                        return false;
                    }

                    return $transactionIds->contains($registration->hash) && $transactionIds->contains($resignation->hash);
                });
        },
    );
});

it('should filter by username transactions', function () {
    $registration = Transaction::factory()->usernameRegistration()->create([
        'sender_public_key' => $this->subject->public_key,
        'timestamp'         => Carbon::parse('2024-11-03 11:33:44')->getTimestampMs(), // oldest transaction
    ]);

    $transfer = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
        'timestamp'         => Carbon::parse('2024-11-04 11:33:44')->getTimestampMs(),
    ]);

    $resignation = Transaction::factory()->usernameResignation()->create([
        'sender_public_key' => $this->subject->public_key,
        'timestamp'         => Carbon::parse('2024-11-03 11:33:44')->getTimestampMs(), // oldest transaction
    ]);

    performWalletRequest(
        $this,
        wallet: $this->subject,
        queryString: [
            'outgoing'            => 'true',
            'incoming'            => 'false',
            'transfers'           => 'false',
            'multipayments'       => 'false',
            'votes'               => 'false',
            'validator'           => 'false',
            'username'            => 'true',
            'contract_deployment' => 'false',
            'others'              => 'false',
        ],
        reloadCallback: function (Assert $reload) use ($transfer, $registration, $resignation) {
            $reload->has('transactions.data', 2)
                ->where('transactions.total', 2)
                ->where('transactions.data', function ($transactions) use ($transfer, $registration, $resignation) {
                    $transactionIds = collect($transactions)->pluck('hash');

                    if ($transactionIds->contains($transfer->hash)) {
                        return false;
                    }

                    return $transactionIds->contains($registration->hash) && $transactionIds->contains($resignation->hash);
                });
        },
    );
});

it('should filter by contract deployment transactions', function () {
    $contractDeployment = Transaction::factory()->contractDeployment()->create([
        'sender_public_key' => $this->subject->public_key,
        'timestamp'         => Carbon::parse('2024-11-03 11:33:44')->getTimestampMs(), // oldest transaction
    ]);

    $transfer = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
        'timestamp'         => Carbon::parse('2024-11-04 11:33:44')->getTimestampMs(),
    ]);

    performWalletRequest(
        $this,
        wallet: $this->subject,
        queryString: [
            'outgoing'            => 'true',
            'incoming'            => 'false',
            'transfers'           => 'false',
            'multipayments'       => 'false',
            'votes'               => 'false',
            'validator'           => 'false',
            'username'            => 'false',
            'contract_deployment' => 'true',
            'others'              => 'false',
        ],
        reloadCallback: function (Assert $reload) use ($transfer, $contractDeployment) {
            $reload->has('transactions.data', 1)
                ->where('transactions.total', 1)
                ->where('transactions.data', function ($transactions) use ($transfer, $contractDeployment) {
                    $transactionIds = collect($transactions)->pluck('hash');

                    if ($transactionIds->contains($transfer->hash)) {
                        return false;
                    }

                    return $transactionIds->contains($contractDeployment->hash);
                });
        },
    );
});

it('should filter by other transactions to consensus address', function () {
    $transfer = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $other = Transaction::factory()->withPayload('12345678')->create([
        'sender_public_key' => $this->subject->public_key,
        'to'                => Network::knownContract('consensus'),
    ]);

    performWalletRequest(
        $this,
        wallet: $this->subject,
        queryString: [
            'outgoing'            => 'true',
            'incoming'            => 'false',
            'transfers'           => 'false',
            'multipayments'       => 'false',
            'votes'               => 'false',
            'validator'           => 'false',
            'username'            => 'false',
            'contract_deployment' => 'false',
            'others'              => 'true',
        ],
        reloadCallback: function (Assert $reload) use ($transfer, $other) {
            $reload->has('transactions.data', 1)
                ->where('transactions.total', 1)
                ->where('transactions.data', function ($transactions) use ($transfer, $other) {
                    $transactionIds = collect($transactions)->pluck('hash');

                    if ($transactionIds->contains($transfer->hash)) {
                        return false;
                    }

                    return $transactionIds->contains($other->hash);
                });
        },
    );
});

it('should filter by other transactions to non-consensus address', function () {
    $transfer = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $other = Transaction::factory()->withPayload('12345678')->create([
        'sender_public_key' => $this->subject->public_key,
        'to'                => 'not consensus address',
    ]);

    performWalletRequest(
        $this,
        wallet: $this->subject,
        queryString: [
            'outgoing'            => 'true',
            'incoming'            => 'false',
            'transfers'           => 'false',
            'multipayments'       => 'false',
            'votes'               => 'false',
            'validator'           => 'false',
            'username'            => 'false',
            'contract_deployment' => 'false',
            'others'              => 'true',
        ],
        reloadCallback: function (Assert $reload) use ($transfer, $other) {
            $reload->has('transactions.data', 1)
                ->where('transactions.total', 1)
                ->where('transactions.data', function ($transactions) use ($transfer, $other) {
                    $transactionIds = collect($transactions)->pluck('hash');

                    if ($transactionIds->contains($transfer->hash)) {
                        return false;
                    }

                    return $transactionIds->contains($other->hash);
                });
        },
    );
});

it('should not filter transfers to consensus as "other"', function () {
    $transfer = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $other = Transaction::factory()->create([
        'sender_public_key' => $this->subject->public_key,
        'to'                => Network::knownContract('consensus'),
    ]);

    performWalletRequest(
        $this,
        wallet: $this->subject,
        queryString: [
            'outgoing'            => 'true',
            'incoming'            => 'false',
            'transfers'           => 'false',
            'multipayments'       => 'false',
            'votes'               => 'false',
            'validator'           => 'false',
            'username'            => 'false',
            'contract_deployment' => 'false',
            'others'              => 'true',
        ],
        reloadCallback: function (Assert $reload) use ($transfer, $other) {
            $reload->has('transactions.data', 0)
                ->where('transactions.total', 0)
                ->where('transactions.data', function ($transactions) use ($transfer, $other) {
                    $transactionIds = collect($transactions)->pluck('hash');

                    if ($transactionIds->contains($transfer->hash)) {
                        return false;
                    }

                    return ! $transactionIds->contains($other->hash);
                });
        },
    );
});

it('should show no transactions if no filters', function () {
    Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    Transaction::factory()->validatorRegistration()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    performWalletRequest(
        $this,
        wallet: $this->subject,
        queryString: [
            'outgoing'            => 'false',
            'incoming'            => 'false',
            'transfers'           => 'false',
            'multipayments'       => 'false',
            'votes'               => 'false',
            'validator'           => 'false',
            'username'            => 'false',
            'contract_deployment' => 'false',
            'others'              => 'false',
        ],
        reloadCallback: function (Assert $reload) {
            $reload->has('transactions.data', 0)
                ->where('transactions.total', 0)
                ->where('transactions.noResultsMessage', trans('tables.transactions.no_results.no_filters'));
        },
    );
});

it('should show no transactions if no addressing filter', function () {
    Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    Transaction::factory()->validatorRegistration()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    performWalletRequest(
        $this,
        wallet: $this->subject,
        queryString: [
            'outgoing'            => 'false',
            'incoming'            => 'false',
            'transfers'           => 'true',
            'multipayments'       => 'true',
            'votes'               => 'true',
            'validator'           => 'true',
            'username'            => 'true',
            'contract_deployment' => 'true',
            'others'              => 'true',
        ],
        reloadCallback: function (Assert $reload) {
            $reload->has('transactions.data', 0)
                ->where('transactions.total', 0)
                ->where('transactions.noResultsMessage', trans('tables.transactions.no_results.no_addressing_filters'));
        },
    );
});

it('should show no transactions if no type filter', function () {
    Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    Transaction::factory()->validatorRegistration()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    performWalletRequest(
        $this,
        wallet: $this->subject,
        queryString: [
            'outgoing'            => 'true',
            'incoming'            => 'true',
            'transfers'           => 'false',
            'multipayments'       => 'false',
            'votes'               => 'false',
            'validator'           => 'false',
            'username'            => 'false',
            'contract_deployment' => 'false',
            'others'              => 'false',
        ],
        reloadCallback: function (Assert $reload) {
            $reload->has('transactions.data', 0)
                ->where('transactions.total', 0)
                ->where('transactions.noResultsMessage', trans('tables.transactions.no_results.no_results'));
        },
    );
});

it('should show no results message if no transactions matching filter', function () {
    performWalletRequest(
        $this,
        wallet: $this->subject,
        reloadCallback: function (Assert $reload) {
            $reload->has('transactions.data', 0)
                ->where('transactions.total', 0)
                ->where('transactions.noResultsMessage', trans('tables.transactions.no_results.no_results'));
        },
    );
});
