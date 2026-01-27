<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\Inertia\Transaction as TransactionDTO;
use App\Models\Transaction;
use Illuminate\View\View;

final class ShowTransactionController
{
    public function __invoke(Transaction $transaction): View
    {
        return view('app.transaction', [
            'transaction' => TransactionDTO::fromModel($transaction),
        ]);
    }
}
