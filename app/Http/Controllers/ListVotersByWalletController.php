<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

final class ListVotersByWalletController
{
    public function __invoke(Request $request, Wallet $wallet): \Illuminate\Http\Response
    {
        return Response::noContent();
    }
}
