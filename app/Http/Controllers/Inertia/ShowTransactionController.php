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
        $transaction->loadMissing('votedFor', 'senderWallet', 'recipientWallet');

        return Inertia::render('Transaction/Show', [
            'transaction' => TransactionDTO::fromModel($transaction),
            'details'     => TransactionDetails::fromModel($transaction),
            'recipients'  => Inertia::defer(fn () => $this->recipients($transaction)),
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
