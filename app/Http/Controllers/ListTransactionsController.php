<?php

declare(strict_types=1);

namespace  App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

final class ListTransactionsController extends Controller
{
    public function __invoke(Request $request)
    {
        return Transaction::paginate();
    }
}
