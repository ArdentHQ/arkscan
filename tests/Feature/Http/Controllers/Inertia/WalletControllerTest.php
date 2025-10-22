<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Models\Wallet;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->withoutExceptionHandling();

    $this->subject = Wallet::factory()->create();
});

function performWalletRequest($context, $withReload = true, $pageCallback = null, $reloadCallback = null, ?Wallet $wallet = null): mixed
{
    if ($wallet === null) {
        $wallet = Wallet::factory()->create();
    }

    return $context->get(route('wallet-inertia', $wallet))
        ->assertOk()
        ->assertInertia(function (Assert $page) use ($pageCallback, $wallet, $withReload, $reloadCallback) {
            $page->where('wallet.address', $wallet->address)
                ->missing('transactions')
                ->component('Wallet');

            if (is_callable($pageCallback)) {
                $pageCallback($page);
            }

            if (! $withReload) {
                return;
            }

            $page->reloadOnly('wallet,transactions', function (Assert $reload) use ($reloadCallback) {
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
            'from' => $altWallet->address,
            'to' => $this->subject->address,
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
                    "pageName"  => "page",
                    "urlParams" => [],
                ])
                ->where('transactions.data', function ($transactions) use ($sent, $received) {
                    $transactionIds = collect($transactions)->pluck('hash');

                    return $transactionIds->contains($sent->hash) && $transactionIds->contains($received->hash);
                });
        },
    );
});
