<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use App\DTO\Inertia\Transaction as TransactionDTO;
use App\DTO\Inertia\TransactionDetails;
use App\Models\MultiPayment;
use App\Models\Transaction;
use Inertia\Inertia;
use Inertia\Response;

final class ShowTransactionController
{
    public function __invoke(Transaction $transaction): Response
    {
        return Inertia::render('Transaction/Show', [
            'transaction' => function () use ($transaction) {
                $transaction->loadMissing('votedFor', 'senderWallet', 'recipientWallet');

                return TransactionDTO::fromModel($transaction);
            },
            'details'    => fn () => TransactionDetails::fromModel($transaction),
            'recipients' => Inertia::defer(fn () => $this->recipients($transaction)),
        ])->withMeta('transaction', [
            'txid' => $transaction->hash,
        ]);
    }

    /**
     * @return array<int, array{address: string, amount: string}>
     */
    private function recipients(Transaction $transaction): array
    {
        return $transaction->multiPaymentRecipients
            ->map(fn (MultiPayment $recipient) => [
                'address' => $recipient->to,
                'amount'  => (string) $recipient->amount,
            ])
            ->values()
            ->toArray();
    }
}
