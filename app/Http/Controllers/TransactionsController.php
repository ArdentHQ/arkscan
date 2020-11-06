<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class TransactionsController
{
    public function __invoke(Request $request, Transaction $transaction): View
    {
        $type = $request->input('state.type', 'all');

        return view('transactions', [
            'transactionTypeFilter' => $type,
        ]);
    }
}
