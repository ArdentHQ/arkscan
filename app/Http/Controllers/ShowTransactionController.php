<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\ViewModels\ViewModelFactory;
use Illuminate\View\View;

final class ShowTransactionController
{
    public function __invoke(Transaction $transaction): View
    {
        return view('app.transaction', [
            'transaction' => ViewModelFactory::make($transaction),
        ]);
    }
}
