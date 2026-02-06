<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Models\TokenTransfer;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use Illuminate\Support\Facades\Cache;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    Cache::tags('statistics')->flush();
});

function performRequest($context, $withReload = true, $pageCallback = null, $reloadCallback = null): mixed
{
    return $context->get(route('tokens.transfers'))
        ->assertOk()
        ->assertInertia(function (Assert $page) use ($pageCallback, $withReload, $reloadCallback) {
            $page->missing('transfers')
                ->component('Tokens/Transfers');

            if (is_callable($pageCallback)) {
                $pageCallback($page);
            }

            if (! $withReload) {
                return;
            }

            $page->reloadOnly('transfers', function (Assert $reload) use ($reloadCallback) {
                if (is_callable($reloadCallback)) {
                    $reloadCallback($reload);
                }
            });
        });
}

it('should render the page without any errors', function () {
    $recipientWallet = Wallet::factory()->create();

    Wallet::factory()->create([
        'address' => Network::knownContract('consensus'),
    ]);

    $transaction = Transaction::factory()
        ->tokenTransfer(
            $recipientWallet->address,
            BigNumber::new(100)->multipliedBy(1e18),
        )
        ->create();

    TokenTransfer::factory(3)->create([
        'transaction_hash' => $transaction->hash,
    ]);

    performRequest($this, reloadCallback: function (Assert $page) {
        $page->has('transfers.data', 3)
            ->where('transfers.noResultsMessage', null);
    });
});

it('should provide the no results message if no transfers exist', function () {
    performRequest(
        $this,
        reloadCallback: function (Assert $page) {
            $page->has('transfers.data', 0)
                ->where('transfers.noResultsMessage', (string) trans('tables.tokens.transfers.no_results'));
        },
    );
});

it('should not include contract deployment transactions', function () {
    $recipientWallet = Wallet::factory()->create();

    Wallet::factory()->create([
        'address' => Network::knownContract('consensus'),
    ]);

    $transaction = Transaction::factory()
        ->tokenTransfer(
            $recipientWallet->address,
            BigNumber::new(100)->multipliedBy(1e18),
        )
        ->create();

    TokenTransfer::factory(3)->create([
        'transaction_hash' => $transaction->hash,
    ]);

    $contractDeployment = Transaction::factory()->contractDeployment()->create();

    TokenTransfer::factory(2)->create([
        'transaction_hash' => $contractDeployment->hash,
    ]);

    performRequest($this, reloadCallback: function (Assert $page) {
        $page->has('transfers.data', 3)
            ->where('transfers.noResultsMessage', null);
    });
});
