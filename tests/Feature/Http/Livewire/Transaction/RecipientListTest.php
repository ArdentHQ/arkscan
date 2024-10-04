<?php

declare(strict_types=1);

use App\Http\Livewire\Transaction\RecipientList;
use App\Models\Transaction;
use App\Models\Wallet;
use Livewire\Livewire;

it('should list the first page of records', function () {
    $recipient1 = Wallet::factory()->create();
    $recipient2 = Wallet::factory()->create();
    $recipient3 = Wallet::factory()->create();

    $transaction = Transaction::factory()->multiPayment()->create([
        'asset' => [
            'payments' => [
                [
                    'recipientId' => $recipient1->address,
                    'amount'      => 1 * 1e18,
                ],
                [
                    'recipientId' => $recipient2->address,
                    'amount'      => 2 * 1e18,
                ],
                [
                    'recipientId' => $recipient3->address,
                    'amount'      => 3 * 1e18,
                ],
            ],
        ],
    ]);

    Livewire::test(RecipientList::class, ['transactionId' => $transaction->id])
        ->call('setIsReady')
        ->assertSeeInOrder([
            $recipient3->address,
            $recipient2->address,
            $recipient1->address,
        ]);
});

it('should defer loading', function () {
    $recipient1 = Wallet::factory()->create();
    $recipient2 = Wallet::factory()->create();
    $recipient3 = Wallet::factory()->create();

    $transaction = Transaction::factory()->multiPayment()->create([
        'asset' => [
            'payments' => [
                [
                    'recipientId' => $recipient1->address,
                    'amount'      => 1 * 1e18,
                ],
                [
                    'recipientId' => $recipient2->address,
                    'amount'      => 2 * 1e18,
                ],
                [
                    'recipientId' => $recipient3->address,
                    'amount'      => 3 * 1e18,
                ],
            ],
        ],
    ]);

    Livewire::test(RecipientList::class, ['transactionId' => $transaction->id])
        ->assertDontSee($recipient3->address)
        ->assertDontSee($recipient2->address)
        ->assertDontSee($recipient1->address)
        ->call('setIsReady')
        ->assertSee($recipient3->address)
        ->assertSee($recipient2->address)
        ->assertSee($recipient1->address);
});
