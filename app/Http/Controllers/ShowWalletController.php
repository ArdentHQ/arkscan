<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;

class ShowWalletController extends Controller
{
    public function __invoke(Request $request, Wallet $wallet)
    {
        return $wallet;
    }
}
