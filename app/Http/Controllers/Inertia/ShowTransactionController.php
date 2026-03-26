<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use App\DTO\Inertia\Transaction as TransactionDTO;
use App\DTO\Inertia\TransactionDetails;
use App\Models\Transaction;
use Inertia\Inertia;
use Inertia\Response;

final class ShowTransactionController
{
    public function __invoke(Transaction $transaction): Response
    {
        $transaction->loadMissing('votedFor', 'multiPaymentRecipients', 'senderWallet', 'recipientWallet');

        return Inertia::renderWithMeta('Transaction/Show', 'transaction', [
            'transaction' => TransactionDTO::fromModel($transaction),
            'details'     => TransactionDetails::fromModel($transaction),
        ], [
            'txid' => $transaction->hash,
        ]);
    }
}
