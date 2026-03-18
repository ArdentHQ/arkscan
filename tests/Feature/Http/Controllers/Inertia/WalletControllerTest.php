<?php

declare(strict_types=1);

use App\Console\Commands\CacheValidatorsWithVoters;
use App\Facades\Network;
use App\Models\Block;
use App\Models\TokenHolder;
use App\Models\TokenAction;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\WalletCache;
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
                ->missing('tokenActions')
                ->missing('tokens')
                ->missing('blocks')
                ->missing('voters')
                ->component('Wallet/Wallet');

            if (is_callable($pageCallback)) {
                $pageCallback($page);
            }

            if (! $withReload) {
                return;
            }

            $page->reloadOnly('wallet,transactions,tokenActions,tokens,blocks,voters', function (Assert $reload) use ($reloadCallback) {
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

it('should have token transfers', function () {
    $altWallet = Wallet::factory()->create();

    $sent = Transaction::factory()
        ->tokenTransfer($altWallet->address, BigNumber::new(1 * 1e18))
        ->create([
            'sender_public_key' => $this->subject->public_key,
            'from'              => $this->subject->address,
        ])
        ->fresh();

    $received = Transaction::factory()
        ->tokenTransfer($this->subject->address, BigNumber::new(1 * 1e18))
        ->create([
            'sender_public_key' => $altWallet->public_key,
            'from'              => $altWallet->address,
        ])
        ->fresh();

    TokenAction::factory()->create([
        'transaction_hash' => $sent->hash,
        'from'             => $this->subject->address,
        'to'               => $altWallet->address,
    ]);

    TokenAction::factory()->create([
        'transaction_hash' => $received->hash,
        'from'             => $altWallet->address,
        'to'               => $this->subject->address,
    ]);

    performWalletRequest(
        $this,
        wallet: $this->subject,
        reloadCallback: function (Assert $reload) use ($sent, $received) {
            $reload->has('tokenActions.data', 2)
                ->where('tokenActions.total', 2)
                ->where('tokenActions.current_page', 1)
                ->where('tokenActions.last_page', 1)
                ->where('tokenActions.meta', [
                    'pageName'  => 'page',
                    'urlParams' => [],
                ])
                ->where('tokenActions.data', function ($transactions) use ($sent, $received) {
                    $transactionIds = collect($transactions)->pluck('transaction_hash');

                    return $transactionIds->contains($sent->hash) && $transactionIds->contains($received->hash);
                });
        },
    );
});

it('should have tokens', function () {
    $tokens = TokenHolder::factory(3)->create([
        'address' => $this->subject->address,
    ]);

    performWalletRequest(
        $this,
        wallet: $this->subject,
        reloadCallback: function (Assert $reload) use ($tokens) {
            $reload->has('tokens.data', 3)
                ->where('tokens.total', 3)
                ->where('tokens.current_page', 1)
                ->where('tokens.last_page', 1)
                ->where('tokens.meta', [
                    'pageName'  => 'page',
                    'urlParams' => [],
                ])
                ->where('tokens.data', function ($tokenData) use ($tokens) {
                    $tokenAddresses = collect($tokenData)->pluck('token.address');

                    foreach ($tokens as $token) {
                        if (! $tokenAddresses->contains($token->token_address)) {
                            return false;
                        }
                    }

                    return true;
                });
        },
    );
});

it('should sort whitelisted tokens at the top', function () {
    $regularToken = TokenHolder::factory()->create([
        'address' => $this->subject->address,
        'balance' => (string) BigNumber::new(1000)->multipliedBy(1e18),
    ]);

    $whitelistedToken = TokenHolder::factory()->create([
        'address' => $this->subject->address,
        'balance' => (string) BigNumber::new(1)->multipliedBy(1e18),
    ]);

    $cache = new WalletCache();
    $cache->setWhitelistedTokens(fn () => [strtolower($whitelistedToken->token_address)]);

    performWalletRequest(
        $this,
        wallet: $this->subject,
        reloadCallback: function (Assert $reload) use ($whitelistedToken, $regularToken) {
            $reload->has('tokens.data', 2)
                ->where('tokens.data.0.token.address', $whitelistedToken->token_address)
                ->where('tokens.data.1.token.address', $regularToken->token_address);
        },
    );
});

it('should sort tokens by balance when no whitelisted tokens', function () {
    $lowBalance = TokenHolder::factory()->create([
        'address' => $this->subject->address,
        'balance' => (string) BigNumber::new(1)->multipliedBy(1e18),
    ]);

    $highBalance = TokenHolder::factory()->create([
        'address' => $this->subject->address,
        'balance' => (string) BigNumber::new(1000)->multipliedBy(1e18),
    ]);

    performWalletRequest(
        $this,
        wallet: $this->subject,
        reloadCallback: function (Assert $reload) use ($highBalance, $lowBalance) {
            $reload->has('tokens.data', 2)
                ->where('tokens.data.0.token.address', $highBalance->token_address)
                ->where('tokens.data.1.token.address', $lowBalance->token_address);
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

it('should have voters', function () {
    $voterWallets = Wallet::factory(4)->create([
        'attributes' => [
            'vote' => $this->subject->address,
        ],
    ]);

    (new CacheValidatorsWithVoters())->handle(new WalletCache());

    (new NetworkCache())->setSupply(fn () => 10 * 1e18);

    performWalletRequest(
        $this,
        wallet: $this->subject,
        reloadCallback: function (Assert $reload) use ($voterWallets) {
            $reload->has('voters.data', 4)
                ->where('voters.total', 4)
                ->where('voters.current_page', 1)
                ->where('voters.last_page', 1)
                ->where('voters.meta', [
                    'pageName'  => 'page',
                    'urlParams' => [],
                ])
                ->where('voters.data', function ($voters) use ($voterWallets) {
                    $voterAddresses = collect($voters)->pluck('address');

                    foreach ($voterWallets as $voterWallet) {
                        if (! $voterAddresses->contains($voterWallet->address)) {
                            return false;
                        }
                    }

                    return true;
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

    $sentTokenAction = Transaction::factory()
        ->tokenTransfer($altWallet->address, BigNumber::new(1 * 1e18))
        ->create([
            'sender_public_key' => $this->subject->public_key,
            'from'              => $this->subject->address,
            'status'            => true,
        ])
        ->fresh();

    $receivedTokenAction = Transaction::factory()
        ->tokenTransfer($this->subject->address, BigNumber::new(1 * 1e18))
        ->create([
            'sender_public_key' => $altWallet->public_key,
            'from'              => $altWallet->address,
            'status'            => true,
        ])
        ->fresh();

    TokenAction::factory()->create([
        'transaction_hash' => $sentTokenAction->hash,
        'from'             => $this->subject->address,
        'to'               => $altWallet->address,
    ]);

    TokenAction::factory()->create([
        'transaction_hash' => $receivedTokenAction->hash,
        'from'             => $altWallet->address,
        'to'               => $this->subject->address,
    ]);

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
        reloadCallback: function (Assert $reload) use ($sent, $received, $sentTokenAction, $receivedTokenAction) {
            $reload->has('transactions.data', 4)
                ->where('transactions.total', 4)
                ->where('transactions.data', function ($transactions) use ($sent, $received, $sentTokenAction, $receivedTokenAction) {
                    $transactionIds = collect($transactions)->pluck('hash');

                    if (! $transactionIds->contains($sent->hash)) {
                        return false;
                    }

                    if (! $transactionIds->contains($received->hash)) {
                        return false;
                    }

                    if (! $transactionIds->contains($sentTokenAction->hash)) {
                        return false;
                    }

                    if (! $transactionIds->contains($receivedTokenAction->hash)) {
                        return false;
                    }

                    return true;
                });
        },
    );
});

it('should filter outgoing multipayment transactions', function () {
    $transfer = Transaction::factory()->transfer()->create([
        'sender_public_key' => $this->subject->public_key,
    ]);

    $incomingMultiPayment = Transaction::factory()
        ->multiPayment([$this->subject->address], [BigNumber::new(1 * 1e18)])
        ->create();

    $outgoingMultiPayment = Transaction::factory()
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
        reloadCallback: function (Assert $reload) use ($transfer, $incomingMultiPayment, $outgoingMultiPayment) {
            $reload->has('transactions.data', 1)
                ->where('transactions.total', 1)
                ->where('transactions.data', function ($transactions) use ($transfer, $incomingMultiPayment, $outgoingMultiPayment) {
                    $transactionIds = collect($transactions)->pluck('hash');

                    if ($transactionIds->contains($transfer->hash)) {
                        return false;
                    }

                    if ($transactionIds->contains($incomingMultiPayment->hash)) {
                        return false;
                    }

                    return $transactionIds->contains($outgoingMultiPayment->hash);
                });
        },
    );
});

it('should filter incoming multipayment transactions', function () {
    $transfer = Transaction::factory()->transfer()->create([
        'to' => $this->subject->address,
    ]);

    $incomingMultiPayment = Transaction::factory()
        ->multiPayment([$this->subject->address], [BigNumber::new(1 * 1e18)])
        ->create();

    $outgoingMultiPayment = Transaction::factory()
        ->multiPayment([faker()->wallet['address']], [BigNumber::new(1 * 1e18)])
        ->create([
            'sender_public_key' => $this->subject->public_key,
        ]);

    performWalletRequest(
        $this,
        wallet: $this->subject,
        queryString: [
            'outgoing'            => 'false',
            'incoming'            => 'true',
            'transfers'           => 'false',
            'multipayments'       => 'true',
            'votes'               => 'false',
            'validator'           => 'false',
            'username'            => 'false',
            'contract_deployment' => 'false',
            'others'              => 'false',
        ],
        reloadCallback: function (Assert $reload) use ($transfer, $incomingMultiPayment, $outgoingMultiPayment) {
            $reload->has('transactions.data', 1)
                ->where('transactions.total', 1)
                ->where('transactions.data', function ($transactions) use ($transfer, $incomingMultiPayment, $outgoingMultiPayment) {
                    $transactionIds = collect($transactions)->pluck('hash');

                    if ($transactionIds->contains($transfer->hash)) {
                        return false;
                    }

                    if ($transactionIds->contains($outgoingMultiPayment->hash)) {
                        return false;
                    }

                    return $transactionIds->contains($incomingMultiPayment->hash);
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

it('should show no outgoing transactions when wallet has no public key', function () {
    $wallet = Wallet::factory()->create(['public_key' => null]);

    Transaction::factory()->transfer()->create([
        'to' => $wallet->address,
    ]);

    performWalletRequest(
        $this,
        wallet: $wallet,
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
        reloadCallback: function (Assert $reload) {
            $reload->has('transactions.data', 0)
                ->where('transactions.total', 0);
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

it('should show no results message if no token transfers', function () {
    performWalletRequest(
        $this,
        wallet: $this->subject,
        reloadCallback: function (Assert $reload) {
            $reload->has('tokenActions.data', 0)
                ->where('tokenActions.total', 0)
                ->where('tokenActions.noResultsMessage', trans('tables.tokens.transfers.no_results'));
        },
    );
});

it('should show no results message if no tokens', function () {
    performWalletRequest(
        $this,
        wallet: $this->subject,
        reloadCallback: function (Assert $reload) {
            $reload->has('tokens.data', 0)
                ->where('tokens.total', 0)
                ->where('tokens.noResultsMessage', trans('tables.tokens.no_results'));
        },
    );
});

it('should show no results message if no blocks', function () {
    performWalletRequest(
        $this,
        wallet: $this->subject,
        reloadCallback: function (Assert $reload) {
            $reload->has('blocks.data', 0)
                ->where('blocks.total', 0)
                ->where('blocks.noResultsMessage', trans('tables.wallet.blocks.no_results'));
        },
    );
});

it('should show no results message if no voters', function () {
    performWalletRequest(
        $this,
        wallet: $this->subject,
        reloadCallback: function (Assert $reload) {
            $reload->has('voters.data', 0)
                ->where('voters.total', 0)
                ->where('voters.noResultsMessage', trans('tables.wallets.no_results'));
        },
    );
});
