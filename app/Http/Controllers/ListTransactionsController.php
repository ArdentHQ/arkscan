<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class ListTransactionsController extends Controller
{
    public function __invoke(Request $request)
    {
        return Transaction::paginate();
    }
}
