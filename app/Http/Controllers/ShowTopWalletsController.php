<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;

class ShowTopWalletsController extends Controller
{
    public function __invoke(Request $request)
    {
        return Wallet::orderBy('balance', 'desc')->paginate();
    }
}
