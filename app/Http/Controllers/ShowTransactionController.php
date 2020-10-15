<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class ShowTransactionController extends Controller
{
    public function __invoke(Request $request, Transaction $transaction)
    {
        return $transaction;
    }
}
