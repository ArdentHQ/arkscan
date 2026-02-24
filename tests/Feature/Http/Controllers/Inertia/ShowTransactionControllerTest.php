<?php

declare(strict_types=1);

use App\Enums\ContractMethod;
use App\Facades\Network;
use App\Models\MultiPayment;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\Cache\NetworkCache;
use Inertia\Testing\AssertableInertia as Assert;
use function Tests\fakeCryptoCompare;

it('should render the page without any errors', function () {
    $this->withoutExceptionHandling();

    fakeCryptoCompare();

    (new NetworkCache())->setHeight(fn () => 1000);

    $transaction = Transaction::factory()->create([
        'block_number' => 900,
        'status'       => true,
    ]);

    $this
        ->get(route('transaction', $transaction->hash))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Transaction/Show')
            ->has('transaction')
            ->has('details')
            ->where('transaction.hash', $transaction->hash)
            ->where('details.confirmations', 100)
            ->where('details.recipientIsContract', false)
            ->where('details.transactionError', null)
            ->where('details.payload', null));
});

it('should return decoded token transfer details', function () {
    $this->withoutExceptionHandling();

    fakeCryptoCompare();

    (new NetworkCache())->setHeight(fn () => 1000);

    $recipient = Wallet::factory()->create();
    Wallet::factory()->create(['address' => Network::knownContract('consensus')]);

    $transaction = Transaction::factory()
        ->tokenTransfer($recipient->address, BigNumber::new('1'))
        ->create([
            'block_number' => 995,
            'status'       => true,
        ]);

    $this
        ->get(route('transaction', $transaction->hash))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Transaction/Show')
            ->where('details.tokenTransfer.recipient', $recipient->address)
            ->where('details.tokenTransfer.amount', '1')
            ->where('details.payload.raw', fn ($payload) => is_string($payload) && $payload !== ''));
});

it('should return null token transfer details when payload has no arguments', function () {
    $this->withoutExceptionHandling();

    fakeCryptoCompare();

    (new NetworkCache())->setHeight(fn () => 1000);

    $transaction = Transaction::factory()
        ->withPayload(ContractMethod::transfer())
        ->create([
            'block_number' => 900,
            'status'       => true,
            'to'           => Network::knownContract('consensus'),
        ]);

    Wallet::factory()->create(['address' => $transaction->from]);
    Wallet::factory()->create(['address' => $transaction->to]);

    $this
        ->get(route('transaction', $transaction->hash))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Transaction/Show')
            ->where('details.tokenTransfer', null));
});

it('should return multipayment recipients', function () {
    $this->withoutExceptionHandling();

    fakeCryptoCompare();

    $recipients = Wallet::factory()->count(2)->create();
    $amounts    = [
        BigNumber::new('1000000000000000000'),
        BigNumber::new('2000000000000000000'),
    ];

    $transaction = Transaction::factory()
        ->multiPayment($recipients->pluck('address')->all(), $amounts)
        ->create([
            'status' => true,
        ]);

    MultiPayment::factory()->create([
        'hash'      => $transaction->hash,
        'to'        => $recipients[0]->address,
        'amount'    => (string) $amounts[0],
        'log_index' => 1,
    ]);

    MultiPayment::factory()->create([
        'hash'      => $transaction->hash,
        'to'        => $recipients[1]->address,
        'amount'    => (string) $amounts[1],
        'log_index' => 2,
    ]);

    $this
        ->get(route('transaction', $transaction->hash))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Transaction/Show')
            ->has('transaction.multiPaymentRecipients', 2)
            ->where('transaction.multiPaymentRecipients', function ($data) use ($recipients, $amounts) {
                $addresses    = collect($data)->pluck('address')->sort()->values()->all();
                $amountValues = collect($data)->pluck('amount')->sort()->values()->all();

                return $addresses === $recipients->pluck('address')->sort()->values()->all()
                    && $amountValues === collect($amounts)->map(fn ($amount) => (string) $amount)->sort()->values()->all();
            }));
});
